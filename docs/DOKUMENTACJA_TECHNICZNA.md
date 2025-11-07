---
version: 0.1.0
lang: pl
last_commit: DO USTALENIA
tags: [area:backend, area:frontend, area:mobile, area:api, area:data-model, module:crm, module:training, module:estates, module:tasks, module:tools, module:materials]
source_of_truth: ["PROJECT_DOCS","CODE","API_SPEC"]
---

# Dokumentacja Techniczna - System CRM

## Streszczenie Wykonawcze

System CRM to kompleksowa aplikacja do zarządzania relacjami z klientami, składająca się z backendu opartego na Laravel 11 (PHP 8.4), frontendu Next.js 15 oraz aplikacji mobilnej React Native (Expo). Architektura backendu wykorzystuje Domain-Driven Design (DDD) z wyraźnym podziałem na warstwy: Domain, Application, Infrastructure i API. System obsługuje zarządzanie firmami, kontaktami, szansami sprzedażowymi (opportunities), szkoleniami, zadaniami, nieruchomościami, narzędziami i materiałami. API jest dokumentowane w formacie OpenAPI/Swagger i zwraca odpowiedzi zgodne ze standardem JSON:API. Autoryzacja oparta jest na Laravel Sanctum z systemem uprawnień Spatie Permission.

## Spis Treści

1. [Kontekst Biznesowy](#kontekst-biznesowy)
2. [Architektura](#architektura)
3. [Moduły / Pakiety](#moduły--pakiety)
4. [API](#api)
5. [Model Danych / Baza](#model-danych--baza)
6. [Konfiguracja i Środowiska](#konfiguracja-i-środowiska)
7. [Scenariusze Użycia](#scenariusze-użycia)
8. [Bezpieczeństwo i Zgodność](#bezpieczeństwo-i-zgodność)
9. [Wydajność i Skalowanie](#wydajność-i-skalowanie)
10. [Monitoring i Logowanie](#monitoring-i-logowanie)
11. [Testowanie i Jakość](#testowanie-i-jakość)
12. [Znane Ograniczenia](#znane-ograniczenia)
13. [Changelog](#changelog)
14. [TODO / Luki Informacyjne](#todo--luki-informacyjne)
15. [Słownik Pojęć](#słownik-pojęć)
16. [Źródła Kontekstu](#źródła-kontekstu)

## Kontekst Biznesowy

### Problem / Cel

System CRM został zaprojektowany do kompleksowego zarządzania relacjami z klientami, obejmując:

- **Zarządzanie firmami i kontaktami**: Centralizacja informacji o klientach, ich statusach i źródłach pozyskania
- **Pipeline sprzedażowy**: Zarządzanie szansami sprzedażowymi (opportunities) z przypisaniem do etapów (stages) i pipeline'ów
- **Szkolenia**: System zarządzania szkoleniami z kategoriami, plikami i przypisaniem użytkowników
- **Zadania**: Zarządzanie zadaniami z priorytetami, statusami i przypisaniem
- **Nieruchomości i plany**: Zarządzanie nieruchomościami z planami i pinami
- **Narzędzia i materiały**: Katalog narzędzi i materiałów

### Metryki Sukcesu

DO USTALENIA - WYMAGA DANYCH

## Architektura

### Przegląd Ogólny

System składa się z trzech głównych komponentów:

```
┌─────────────────┐
│   Frontend      │  Next.js 15 + React 19
│   (apps/web)    │  TypeScript, Tailwind CSS
└────────┬────────┘
         │ HTTP/REST
         │ JSON:API
┌────────▼────────┐
│   Backend API   │  Laravel 11 + PHP 8.4
│  (backend/app)  │  DDD Architecture
└────────┬────────┘
         │
    ┌────┴────┐
    │        │
┌───▼───┐ ┌──▼────┐
│PostgreSQL│ │ Redis │
└─────────┘ └───────┘

┌─────────────────┐
│   Mobile App    │  React Native + Expo
│ (apps/mobile)   │  TypeScript
└─────────────────┘
```

### Warstwy Architektury Backendu

1. **Domain Layer** (`app/Domain/`): Encje domenowe (Company, Contact, Opportunity, TrainingCategory, TrainingFile, Activity)
2. **Application Layer** (`app/Services/`): Serwisy orkiestrujące przypadki użycia
3. **Infrastructure Layer** (`app/Infrastructure/`, `app/Repositories/`): Implementacje repozytoriów, mappery Model ↔ Entity
4. **API Layer** (`app/Http/`): Kontrolery, Requesty, Resources, DTO Factories

### Przepływy Wysokiego Poziomu

#### Tworzenie Firmy

1. Klient wysyła żądanie POST `/api/v1/admin/companies`
2. `CreateCompanyRequest` waliduje dane
3. `CreateCompanyDtoFactory` tworzy DTO z requestu
4. `CompanyService::create()` orkiestruje proces:
   - Sprawdza unikalność VAT ID
   - Tworzy encję `Company` w warstwie Domain
   - Zapisuje przez `CompanyRepository`
   - Loguje zdarzenie
5. `CompanyController` zwraca odpowiedź JSON:API

#### Przypisanie Użytkownika do Szkolenia

1. POST `/api/v1/admin/trainings/{training}/users`
2. `AssignUserToTrainingController` deleguje do `TrainingUserService`
3. Serwis weryfikuje istnienie szkolenia i użytkownika
4. Repozytorium zapisuje relację w tabeli `training_user`
5. Zwraca listę przypisanych użytkowników

## Moduły / Pakiety

### Backend - Moduły Domenowe

#### CRM Module (`app/Domain/Crm/`)

**Odpowiedzialność**: Zarządzanie firmami, kontaktami, szansami sprzedażowymi, pipeline'ami i etapami.

**Encje**:
- `Company`: Firma z polami name, industry, source, status, region, vat_id
- `Contact`: Kontakt z polami first_name, last_name, email, phone, lead_level, owner_user_id, source, status
- `Opportunity`: Szansa sprzedażowa z polami title, company_id, contact_id, value, currency, probability, stage_id, owner_user_id, close_date, status
- `Pipeline`: Pipeline sprzedażowy
- `Stage`: Etap w pipeline'ie

**Interfejsy Publiczne**:
- `CompanyRepositoryInterface`: findCompanyById, findByVatId, search, save, deleteCompany
- `ContactRepositoryInterface`: findContactById, findByEmail, search, save
- `CompanyService`: create, update, delete, search, findByUser, assignUser, removeUser
- `ContactService`: create, update, delete, search

**Zależności**: User, Enums (CompanySource, CompanyStatus, ContactStatus, LeadLevel, OpportunityStatus)

### Uwagi techniczne / Diagnostyka

Dodano sondę testową `DocsPipelineProbe` w module CRM z metodą `isDocsPipelineHealthy(): bool` zwracającą `true`. Element służy wyłącznie do weryfikacji automatyzacji dokumentacji (CI/CD) i nie wpływa na logikę biznesową ani interfejsy API produkcyjne.

#### Training Module (`app/Domain/TrainingCategory/`, `app/Domain/TrainingFile/`)

**Odpowiedzialność**: Zarządzanie kategoriami szkoleń, plikami szkoleniowymi.

**Encje**:
- `TrainingCategory`: Kategoria szkolenia
- `TrainingFile`: Plik szkoleniowy

**Interfejsy Publiczne**:
- `TrainingService`: create, update, delete
- `TrainingCategoryService`: create, update, delete, getAll
- `TrainingFileService`: attach, delete, getByTraining
- `TrainingUserService`: assign, remove, assignAll, assignByRole, assignSelected

**Zależności**: User, Training Model

#### Activity Module (`app/Domain/Activity/`)

**Odpowiedzialność**: Rejestrowanie aktywności użytkowników.

**Encje**:
- `Activity`: Aktywność użytkownika

**Interfejsy Publiczne**:
- `ActivityService`: list, create
- `ActivityRepository`: findByUser, save

**Zależności**: User

### Frontend (`apps/web/`)

**Technologie**: Next.js 15, React 19, TypeScript, Tailwind CSS, TanStack Query, React Hook Form, Zod

**Struktura**:
- `src/app/`: Strony i route handlers (App Router)
- `src/features/`: Feature-based modules (activities, cars, dashboard, estates, materials, tools)
- `src/shared/`: Współdzielone komponenty i utilities (auth, httpClient, queryClient)

**Moduły**:
- Dashboard: Statystyki systemu
- Tools: Zarządzanie narzędziami (lista, tworzenie, edycja)
- Materials: Zarządzanie materiałami
- Estates: Zarządzanie nieruchomościami
- Cars: Zarządzanie samochodami
- Activities: Lista aktywności

### Mobile (`apps/mobile/`)

**Technologie**: React Native 0.79, Expo ~53, TypeScript, TanStack Query, Expo Router

**Struktura**:
- `src/app/`: Ekrany aplikacji (tabs: index, profile, tasks, users)
- `src/shared/`: Współdzielone utilities (auth, httpClient, queryClient)
- `src/testing/`: Konfiguracja testów

**Funkcjonalności**:
- Autoryzacja
- Profil użytkownika
- Zadania
- Użytkownicy

### Shared Packages (`packages/`)

- `api-sdk`: SDK do komunikacji z API
- `config`: Współdzielona konfiguracja
- `i18n`: Internacjonalizacja
- `theme`: Motywy UI
- `ui`: Komponenty UI
- `tsconfig`: Konfiguracje TypeScript
- `eslint-config`: Konfiguracje ESLint

## API

### Base URL

- Development: `http://localhost:8199/api/v1`
- Production: DO USTALENIA

### Autoryzacja

Wszystkie endpointy (oprócz auth) wymagają tokena Bearer w nagłówku:
```
Authorization: Bearer {token}
```

Token uzyskuje się przez endpoint `/api/v1/auth/login`.

### Endpointy Autoryzacji

#### POST `/api/v1/auth/register`

Rejestracja nowego użytkownika.

**Request**:
```json
{
  "name": "string",
  "email": "string",
  "password": "string"
}
```

**Response**: 201 Created
```json
{
  "data": {
    "type": "users",
    "id": "string",
    "attributes": { ... }
  }
}
```

#### POST `/api/v1/auth/login`

Logowanie użytkownika.

**Request**:
```json
{
  "email": "string",
  "password": "string"
}
```

**Response**: 200 OK
```json
{
  "token": "string",
  "user": { ... }
}
```

#### GET `/api/v1/auth/me`

Pobranie danych zalogowanego użytkownika.

**Response**: 200 OK
```json
{
  "data": {
    "type": "users",
    "id": "integer",
    "attributes": {
      "name": "string",
      "surname": "string",
      "email": "string",
      "phone": "string"
    }
  }
}
```

#### POST `/api/v1/auth/logout`

Wylogowanie użytkownika.

**Response**: 204 No Content

#### POST `/api/v1/auth/forgot-password`

Reset hasła - wysłanie tokena.

**Request**:
```json
{
  "email": "string"
}
```

**Response**: 200 OK

#### POST `/api/v1/auth/reset-password`

Reset hasła - ustawienie nowego hasła.

**Request**:
```json
{
  "token": "string",
  "email": "string",
  "password": "string",
  "password_confirmation": "string"
}
```

**Response**: 200 OK

### Endpointy Firm (Companies)

#### GET `/api/v1/admin/companies`

Lista firm z filtrowaniem i paginacją.

**Query Parameters**:
- `name` (string, optional): Filtrowanie po nazwie
- `vat_id` (string, optional): Filtrowanie po VAT ID
- `region` (string, optional): Filtrowanie po regionie
- `status` (enum: active|inactive|prospect, optional)
- `source` (enum: website|referral|social_media|email_campaign|cold_call|trade_show|partner|other, optional)
- `industry` (string, optional)
- `sort_by` (string, default: created_at)
- `sort_direction` (enum: asc|desc, default: desc)
- `per_page` (integer, default: 15, max: 100)
- `page` (integer, default: 1)

**Uprawnienia**: `company.view`

**Response**: 200 OK
```json
{
  "data": [
    {
      "type": "companies",
      "id": "uuid",
      "attributes": {
        "name": "string",
        "industry": "string|null",
        "source": "string",
        "status": "string",
        "region": "string|null",
        "vat_id": "string|null",
        "created_by": "string",
        "created_at": "ISO8601",
        "updated_at": "ISO8601"
      }
    }
  ],
  "meta": {
    "total": "integer",
    "per_page": "integer",
    "current_page": "integer",
    "last_page": "integer",
    "from": "integer|null",
    "to": "integer|null",
    "request_id": "string"
  }
}
```

#### GET `/api/v1/admin/companies/my`

Lista firm przypisanych do zalogowanego użytkownika.

**Uprawnienia**: `company.view`

**Response**: 200 OK (format jak wyżej)

#### GET `/api/v1/admin/companies/{company}`

Szczegóły firmy.

**Uprawnienia**: `company.view`

**Response**: 200 OK
```json
{
  "data": {
    "type": "companies",
    "id": "uuid",
    "attributes": { ... }
  },
  "meta": {
    "request_id": "string"
  }
}
```

#### POST `/api/v1/admin/companies`

Tworzenie nowej firmy.

**Uprawnienia**: `company.create`

**Request**:
```json
{
  "name": "string (required)",
  "industry": "string (optional)",
  "source": "string (required, enum)",
  "status": "string (optional, enum, default: prospect)",
  "region": "string (optional)",
  "vat_id": "string (optional, unique)"
}
```

**Response**: 201 Created

#### PUT `/api/v1/admin/companies/{company}`

Aktualizacja firmy.

**Uprawnienia**: `company.update`

**Request**: (jak POST, wszystkie pola optional)

**Response**: 200 OK

#### DELETE `/api/v1/admin/companies/{company}`

Usunięcie firmy (soft delete).

**Uprawnienia**: `company.delete`

**Response**: 204 No Content

### Endpointy Kontaktów (Contacts)

#### GET `/api/v1/admin/contacts`

Lista kontaktów.

**Uprawnienia**: `contact.view`

**Response**: 200 OK (format JSON:API)

#### GET `/api/v1/admin/contacts/{contact}`

Szczegóły kontaktu.

**Uprawnienia**: `contact.view`

#### POST `/api/v1/admin/contacts`

Tworzenie kontaktu.

**Uprawnienia**: `contact.create`

#### PUT `/api/v1/admin/contacts/{contact}`

Aktualizacja kontaktu.

**Uprawnienia**: `contact.update`

#### DELETE `/api/v1/admin/contacts/{contact}`

Usunięcie kontaktu.

**Uprawnienia**: `contact.delete`

#### POST `/api/v1/admin/contacts/{contact}/companies/link`

Przypisanie kontaktu do firmy.

**Uprawnienia**: `contact.update`

#### DELETE `/api/v1/admin/contacts/{contact}/companies/unlink`

Odpięcie kontaktu od firmy.

**Uprawnienia**: `contact.update`

#### GET `/api/v1/admin/contacts/{contact}/companies`

Lista firm przypisanych do kontaktu.

**Uprawnienia**: `contact.view`

#### POST `/api/v1/admin/contacts/bulk/link-company`

Masowe przypisanie kontaktów do firmy.

**Uprawnienia**: `contact.update`

#### DELETE `/api/v1/admin/contacts/bulk/unlink-company`

Masowe odpięcie kontaktów od firmy.

**Uprawnienia**: `contact.update`

### Endpointy Szkoleń (Trainings)

#### POST `/api/v1/admin/trainings`

Tworzenie szkolenia.

**Uprawnienia**: `training.create`

#### PUT `/api/v1/admin/trainings/{training}`

Aktualizacja szkolenia.

**Uprawnienia**: `training.update`

#### DELETE `/api/v1/admin/trainings/{training}`

Usunięcie szkolenia.

**Uprawnienia**: `training.delete`

#### GET `/api/v1/admin/trainings/{training}/users`

Lista użytkowników przypisanych do szkolenia.

**Uprawnienia**: `training.user.list`

#### POST `/api/v1/admin/trainings/{training}/users`

Przypisanie użytkownika do szkolenia.

**Uprawnienia**: `training.user.assign`

#### DELETE `/api/v1/admin/trainings/{training}/users/{user}`

Usunięcie użytkownika ze szkolenia.

**Uprawnienia**: `training.user.remove`

#### POST `/api/v1/admin/trainings/{training}/users/assign-all`

Przypisanie wszystkich użytkowników do szkolenia.

**Uprawnienia**: `training.user.assign_all`

#### POST `/api/v1/admin/trainings/{training}/users/assign-by-role`

Przypisanie użytkowników według roli.

**Uprawnienia**: `training.user.assign_by_role`

#### POST `/api/v1/admin/trainings/{training}/users/assign-selected`

Przypisanie wybranych użytkowników.

**Uprawnienia**: `training.user.assign_selected`

#### GET `/api/v1/admin/trainings/{training}/files`

Lista plików szkolenia.

**Uprawnienia**: `training.file.list`

#### POST `/api/v1/admin/trainings/{training}/files`

Dodanie pliku do szkolenia.

**Uprawnienia**: `training.file.attach`

#### POST `/api/v1/admin/trainings/{training}/files/multiple`

Dodanie wielu plików do szkolenia.

**Uprawnienia**: `training.file.attach`

#### DELETE `/api/v1/admin/trainings/{training}/files/{file}`

Usunięcie pliku ze szkolenia.

**Uprawnienia**: `training.file.delete`

### Endpointy Kategorii Szkoleń

#### GET `/api/v1/admin/training-categories`

Lista kategorii szkoleń.

**Uprawnienia**: `training.category.list`

#### POST `/api/v1/admin/training-categories`

Tworzenie kategorii.

**Uprawnienia**: `training.category.create`

#### PUT `/api/v1/admin/training-categories/{id}`

Aktualizacja kategorii.

**Uprawnienia**: `training.category.update`

#### DELETE `/api/v1/admin/training-categories/{id}`

Usunięcie kategorii.

**Uprawnienia**: `training.category.delete`

### Endpointy Zadań (Tasks)

#### GET `/api/v1/admin/tasks/list`

Lista zadań.

**Uprawnienia**: `task.list`

#### GET `/api/v1/admin/tasks/{task}`

Szczegóły zadania.

**Uprawnienia**: `task.show`

#### POST `/api/v1/admin/tasks`

Tworzenie zadania.

**Uprawnienia**: `task.create`

#### PUT `/api/v1/admin/tasks/{task}`

Aktualizacja zadania.

**Uprawnienia**: `task.update`

#### DELETE `/api/v1/admin/tasks/{task}`

Usunięcie zadania.

**Uprawnienia**: `task.delete`

### Endpointy Użytkowników

#### GET `/api/v1/admin/users/list`

Lista użytkowników.

**Uprawnienia**: `user.list`

#### GET `/api/v1/admin/users/user/{user}`

Szczegóły użytkownika.

**Uprawnienia**: `user.show`

#### POST `/api/v1/admin/users/user`

Tworzenie użytkownika.

**Uprawnienia**: `user.create`

#### PUT `/api/v1/admin/users/user/{user}`

Aktualizacja użytkownika.

**Uprawnienia**: `user.update`

#### DELETE `/api/v1/admin/users/user/{user}`

Usunięcie użytkownika.

**Uprawnienia**: `user.delete`

#### GET `/api/v1/users/{user}`

Szczegóły użytkownika (dla zwykłych użytkowników).

**Uprawnienia**: `user.show`

#### PUT `/api/v1/users/{user}`

Aktualizacja własnego profilu.

**Uprawnienia**: `user.update`

#### POST `/api/v1/users/change-password`

Zmiana hasła.

**Uprawnienia**: `user.change_password`

### Endpointy Dashboard

#### GET `/api/v1/admin/dashboard/stats`

Statystyki dashboardu.

**Uprawnienia**: DO USTALENIA

**Response**: 200 OK
```json
{
  "data": {
    "type": "dashboard_stats",
    "attributes": {
      // DO USTALENIA - WYMAGA DANYCH
    }
  }
}
```

### Inne Endpointy

- **Narzędzia (Tools)**: `/api/v1/admin/tools` (list, show, store, update, destroy)
- **Materiały (Materials)**: `/api/v1/admin/materials` (list, show, store, update, destroy)
- **Miasta (Cities)**: `/api/v1/admin/cities` (list, create, update, destroy)
- **Nieruchomości (Estates)**: `/api/v1/admin/estates` (list, show, store, update, destroy)
- **Plany (Plans)**: `/api/v1/admin/plans/{estate}` (show, store, destroy)
- **Piny (Pins)**: `/api/v1/admin/pins/{plan}` (show), `/api/v1/users/plans/{plan}/pins` (index, store)
- **Samochody (Cars)**: `/api/v1/admin/cars` (list, show, create, update, delete)
- **Aktywności (Activities)**: `/api/v1/admin/activities/list`

### Format Odpowiedzi JSON:API

Wszystkie odpowiedzi API (oprócz błędów) zwracają dane w formacie JSON:API:

```json
{
  "data": {
    "type": "resource_type",
    "id": "resource_id",
    "attributes": {
      // pola zasobu
    }
  },
  "meta": {
    "request_id": "string"
  }
}
```

Dla kolekcji:
```json
{
  "data": [
    { "type": "...", "id": "...", "attributes": {...} }
  ],
  "meta": {
    "pagination": {...},
    "request_id": "..."
  }
}
```

### Błędy

Format błędów:
```json
{
  "errors": [
    {
      "status": "HTTP_CODE",
      "title": "Error Title",
      "detail": "Error message"
    }
  ]
}
```

## Model Danych / Baza

### Baza Danych

**System**: PostgreSQL 16 (development), DO USTALENIA (production)

**Połączenie**: Konfiguracja w `config/database.php`, domyślnie PostgreSQL

### Tabele

#### users

Główna tabela użytkowników.

**Pola**:
- `id` (bigint, primary key, auto increment)
- `name` (string, required)
- `surname` (string, nullable)
- `email` (string, unique, required)
- `phone` (string, nullable)
- `city` (string, nullable)
- `voivodeship` (string, nullable)
- `email_verified_at` (timestamp, nullable)
- `password` (string, required, hashed)
- `status` (enum: inactive|active|blocked, default: inactive)
- `remember_token` (string, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)
- `deleted_at` (timestamp, nullable, soft delete)

**Indeksy**: email (unique), deleted_at

#### companies

Firmy klientów.

**Pola**:
- `id` (uuid, primary key)
- `name` (string, required)
- `industry` (string, nullable)
- `source` (enum: website|referral|social_media|email_campaign|cold_call|trade_show|partner|other, required)
- `status` (enum: active|inactive|prospect, default: prospect)
- `region` (string, nullable)
- `vat_id` (string, nullable, unique)
- `created_by` (foreignId → users.id, required)
- `created_at` (timestamptz)
- `updated_at` (timestamptz)
- `deleted_at` (timestamptz, nullable, soft delete)

**Indeksy**: vat_id, name, status+source, region, created_by, deleted_at

**Relacje**:
- `created_by` → `users.id` (ON DELETE CASCADE)
- `users` (many-to-many przez `company_users`)
- `contacts` (many-to-many przez `contact_company`)

#### contacts

Kontakty (osoby).

**Pola**:
- `id` (uuid, primary key)
- `first_name` (string, required)
- `last_name` (string, required)
- `email` (string, unique, required)
- `phone` (string, nullable)
- `lead_level` (enum: lead|contact, default: lead)
- `owner_user_id` (foreignId → users.id, nullable)
- `source` (string, required)
- `status` (enum: new|active|dormant|lost, default: new)
- `created_at` (timestamptz)
- `updated_at` (timestamptz)
- `deleted_at` (timestamptz, nullable, soft delete)

**Indeksy**: email (unique), first_name+last_name, status+lead_level, owner_user_id, source, deleted_at

**Relacje**:
- `owner_user_id` → `users.id` (ON DELETE SET NULL)
- `companies` (many-to-many przez `contact_company`)

#### contact_company

Tabela pivot: kontakty ↔ firmy.

**Pola**:
- `contact_id` (uuid, foreign key)
- `company_id` (uuid, foreign key)
- `position` (string, nullable)
- `is_primary` (boolean, nullable)
- `created_at` (timestamptz)
- `updated_at` (timestamptz)

**Relacje**:
- `contact_id` → `contacts.id`
- `company_id` → `companies.id`

#### company_users

Tabela pivot: firmy ↔ użytkownicy (account managerzy).

**Pola**:
- `company_id` (uuid, foreign key)
- `user_id` (bigint, foreign key)
- `role` (string, default: account_manager)
- `created_at` (timestamptz)
- `updated_at` (timestamptz)

**Relacje**:
- `company_id` → `companies.id`
- `user_id` → `users.id`

#### opportunities

Szanse sprzedażowe.

**Pola**:
- `id` (uuid, primary key)
- `title` (string, required)
- `company_id` (uuid, foreign key → companies.id, required)
- `contact_id` (uuid, foreign key → contacts.id, nullable)
- `value` (decimal(15,2), required)
- `currency` (string(3), default: USD)
- `probability` (integer, default: 0, range: 0-100)
- `stage_id` (uuid, foreign key → stages.id, required)
- `owner_user_id` (foreignId → users.id, required)
- `close_date` (date, nullable)
- `status` (enum: open|won|lost, default: open)
- `created_at` (timestamptz)
- `updated_at` (timestamptz)
- `deleted_at` (timestamptz, nullable, soft delete)

**Indeksy**: company_id, contact_id, stage_id, owner_user_id, status, close_date, value, probability, deleted_at

**Relacje**:
- `company_id` → `companies.id` (ON DELETE CASCADE)
- `contact_id` → `contacts.id` (ON DELETE SET NULL)
- `stage_id` → `stages.id` (ON DELETE RESTRICT)
- `owner_user_id` → `users.id` (ON DELETE CASCADE)

#### pipelines

Pipeline'e sprzedażowe.

**Pola**: DO USTALENIA - WYMAGA DANYCH

**Relacje**: `stages` (one-to-many)

#### stages

Etapy w pipeline'ach.

**Pola**: DO USTALENIA - WYMAGA DANYCH

**Relacje**: `pipeline_id` → `pipelines.id`

#### tasks

Zadania.

**Pola**:
- `id` (bigint, primary key, auto increment)
- `title` (string, required)
- `description` (text, required)
- `status` (enum: pending|in_progress|completed|cancelled|on_hold, default: pending)
- `priority` (enum: low|medium|high|urgent, default: medium)
- `assigned_to` (foreignId → users.id, required)
- `created_by` (foreignId → users.id, required)
- `due_date` (timestamp, nullable)
- `completed_at` (timestamp, nullable)
- `estimated_hours` (decimal(8,2), nullable)
- `actual_hours` (decimal(8,2), nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)
- `deleted_at` (timestamp, nullable, soft delete)

**Indeksy**: status+priority, assigned_to+status, due_date+status, created_by

**Relacje**:
- `assigned_to` → `users.id` (ON DELETE CASCADE)
- `created_by` → `users.id` (ON DELETE CASCADE)

#### trainings

Szkolenia.

**Pola**:
- `id` (bigint, primary key, auto increment)
- `title` (string, required)
- `description` (text, nullable)
- `category` (string, required) // DO USTALENIA: czy to foreign key do training_categories?
- `file_path` (string, nullable)
- `file_name` (string, nullable)
- `file_size` (integer, nullable)
- `mime_type` (string, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)
- `deleted_at` (timestamptz, nullable, soft delete)

**Relacje**: `training_user` (many-to-many), `training_files` (one-to-many)

#### training_categories

Kategorie szkoleń.

**Pola**: DO USTALENIA - WYMAGA DANYCH

#### training_user

Tabela pivot: szkolenia ↔ użytkownicy.

**Pola**: DO USTALENIA - WYMAGA DANYCH

#### training_files

Pliki szkoleniowe.

**Pola**: DO USTALENIA - WYMAGA DANYCH

**Relacje**: `training_id` → `trainings.id`

#### activities

Aktywności użytkowników.

**Pola**: DO USTALENIA - WYMAGA DANYCH

#### estates

Nieruchomości.

**Pola**: DO USTALENIA - WYMAGA DANYCH

**Relacje**: `plans` (one-to-many)

#### plans

Plany nieruchomości.

**Pola**: DO USTALENIA - WYMAGA DANYCH

**Relacje**: `estate_id` → `estates.id`, `pins` (one-to-many)

#### pins

Piny na planach.

**Pola**: DO USTALENIA - WYMAGA DANYCH

**Relacje**: `plan_id` → `plans.id`

#### tools

Narzędzia.

**Pola**: DO USTALENIA - WYMAGA DANYCH

#### materials

Materiały.

**Pola**: DO USTALENIA - WYMAGA DANYCH

#### cars

Samochody.

**Pola**: DO USTALENIA - WYMAGA DANYCH

#### cities

Miasta.

**Pola**: DO USTALENIA - WYMAGA DANYCH

### Migracje

Wszystkie migracje znajdują się w `backend/database/migrations/`:

- `0001_01_01_000000_create_users_table.php`
- `2024_11_01_232535_create_permission_tables.php` (Spatie Permission)
- `2024_11_03_095340_create_tools_table.php`
- `2024_11_03_124432_create_materials_table.php`
- `2024_11_04_205643_create_cities_table.php`
- `2024_11_23_173201_create_estates_table.php`
- `2024_11_23_192033_create_plans_table.php`
- `2024_11_23_192549_create_pins_table.php`
- `2025_01_15_000000_create_tasks_table.php`
- `2025_01_27_140000_create_trainings_table.php`
- `2025_01_27_160000_create_training_user_table.php`
- `2025_01_27_170000_create_training_files_table.php`
- `2025_01_27_182000_create_trainings_categories_table.php`
- `2025_01_27_182100_add_category_id_to_trainings_table.php`
- `2025_04_05_141946_create_cars_table.php`
- `2025_09_10_204725_create_activities_table.php`
- `2025_10_18_204547_create_pipelines_table.php`
- `2025_10_18_204549_create_stages_table.php`
- `2025_10_18_204550_create_opportunities_table.php`
- `2025_10_18_213900_create_companies_table.php`
- `2025_10_18_214000_create_contacts_table.php`
- `2025_10_18_214100_create_company_users_table.php`
- `2025_10_18_214200_create_contact_company_table.php`

## Konfiguracja i Środowiska

### Zmienne Środowiskowe Backend

**Plik**: `backend/.env` (na podstawie `backend/.env.example`)

#### Aplikacja

- `APP_NAME`: Nazwa aplikacji (default: Laravel)
- `APP_ENV`: Środowisko (local|staging|production)
- `APP_DEBUG`: Tryb debugowania (true|false)
- `APP_URL`: URL aplikacji (default: http://localhost)
- `APP_TIMEZONE`: Strefa czasowa (default: UTC)
- `APP_LOCALE`: Domyślny język (default: pl)
- `APP_FALLBACK_LOCALE`: Język zapasowy (default: pl)
- `APP_KEY`: Klucz szyfrowania Laravel

#### Baza Danych

- `DB_CONNECTION`: Typ bazy (pgsql|mysql|sqlite, default: sqlite)
- `DB_HOST`: Host bazy danych (default: 127.0.0.1)
- `DB_PORT`: Port bazy danych (5432 dla PostgreSQL)
- `DB_DATABASE`: Nazwa bazy danych
- `DB_USERNAME`: Użytkownik bazy danych
- `DB_PASSWORD`: Hasło bazy danych
- `DB_CHARSET`: Kodowanie (utf8 dla PostgreSQL)
- `DB_FOREIGN_KEYS`: Włącz klucze obce (true|false)

#### Redis

- `REDIS_HOST`: Host Redis (default: 127.0.0.1)
- `REDIS_PORT`: Port Redis (default: 6379)
- `REDIS_PASSWORD`: Hasło Redis (opcjonalne)
- `REDIS_DB`: Numer bazy Redis (default: 0)
- `REDIS_CACHE_DB`: Numer bazy Redis dla cache (default: 1)

#### Cache i Sesje

- `CACHE_DRIVER`: Sterownik cache (redis|file|array)
- `SESSION_DRIVER`: Sterownik sesji (redis|file|database)
- `QUEUE_CONNECTION`: Sterownik kolejek (redis|sync|database)

#### API

- `API_VERSION`: Wersja API (default: v1)

#### Swagger/OpenAPI

- `L5_SWAGGER_GENERATE_ALWAYS`: Generuj zawsze dokumentację (true|false, default: true)
- `L5_SWAGGER_USE_ABSOLUTE_PATH`: Użyj absolutnych ścieżek (true|false)
- `L5_FORMAT_TO_USE_FOR_DOCS`: Format dokumentacji (json|yaml, default: json)

#### Autoryzacja

- `AUTH_GUARD`: Domyślny guard (default: web)
- `AUTH_PASSWORD_BROKER`: Broker resetu hasła (default: users)
- `AUTH_PASSWORD_TIMEOUT`: Timeout potwierdzenia hasła w sekundach (default: 10800)

#### Inne

- `FRONTEND_URL`: URL frontendu (default: http://localhost:3000)

### Docker Compose (Development)

**Plik**: `docker-compose.dev.yml`

**Serwisy**:
- `app`: Kontener PHP/Laravel (port wewnętrzny, volume: `./backend`)
- `nginx`: Reverse proxy (port: 8199, volume: `./backend/public`)
- `pgsql`: PostgreSQL 16 (port: 5433, baza: crm_dev, użytkownik: crm_user)
- `redis`: Redis 7 (port: 6380)

**Zmienne środowiskowe w Docker**:
- `APP_ENV=local`
- `APP_DEBUG=true`
- `DB_CONNECTION=pgsql`
- `DB_HOST=pgsql`
- `DB_DATABASE=crm_dev`
- `DB_USERNAME=crm_user`
- `DB_PASSWORD=crm_password`
- `REDIS_HOST=redis`
- `CACHE_DRIVER=redis`
- `SESSION_DRIVER=redis`
- `QUEUE_CONNECTION=redis`

### Feature Flags

DO USTALENIA - WYMAGA DANYCH

## Scenariusze Użycia

### 1. Rejestracja i Logowanie Użytkownika

**Kroki**:
1. Użytkownik wysyła POST `/api/v1/auth/register` z danymi (name, email, password)
2. System tworzy użytkownika ze statusem `inactive`
3. Użytkownik loguje się przez POST `/api/v1/auth/login`
4. System zwraca token Bearer
5. Użytkownik używa tokena w nagłówku `Authorization: Bearer {token}`

**Pre-warunki**: Brak
**Post-warunki**: Użytkownik zalogowany, token aktywny

### 2. Tworzenie i Przypisanie Firmy do Użytkownika

**Kroki**:
1. Admin tworzy firmę przez POST `/api/v1/admin/companies` (wymaga uprawnienia `company.create`)
2. System waliduje dane (nazwa, source, opcjonalnie VAT ID)
3. System sprawdza unikalność VAT ID (jeśli podany)
4. System tworzy encję `Company` i zapisuje w bazie
5. Admin przypisuje użytkownika jako account managera przez `CompanyService::assignUser()`
6. System zapisuje relację w tabeli `company_users`

**Pre-warunki**: Użytkownik zalogowany, uprawnienie `company.create`
**Post-warunki**: Firma utworzona, użytkownik przypisany

### 3. Przypisanie Użytkowników do Szkolenia

**Kroki**:
1. Admin tworzy szkolenie przez POST `/api/v1/admin/trainings`
2. Admin przypisuje użytkowników przez POST `/api/v1/admin/trainings/{training}/users` (pojedynczo) lub `/assign-all`, `/assign-by-role`, `/assign-selected` (masowo)
3. System weryfikuje istnienie szkolenia i użytkowników
4. System zapisuje relacje w tabeli `training_user`
5. Admin może dodać pliki przez POST `/api/v1/admin/trainings/{training}/files`

**Pre-warunki**: Szkolenie istnieje, użytkownicy istnieją, uprawnienia `training.user.assign`
**Post-warunki**: Użytkownicy przypisani, pliki dołączone (opcjonalnie)

### 4. Zarządzanie Szansami Sprzedażowymi (Opportunities)

**Kroki**:
1. Admin tworzy opportunity przez odpowiedni endpoint (DO USTALENIA - endpoint nie widoczny w routes)
2. System przypisuje opportunity do firmy, kontaktu (opcjonalnie), etapu (stage) i właściciela (owner_user_id)
3. System śledzi wartość, prawdopodobieństwo, datę zamknięcia
4. Admin aktualizuje status (open|won|lost) i etap w pipeline'ie

**Pre-warunki**: Firma istnieje, etap istnieje, użytkownik-właściciel istnieje
**Post-warunki**: Opportunity utworzona, przypisana do pipeline'u

### 5. Zarządzanie Zadaniami

**Kroki**:
1. Użytkownik tworzy zadanie przez POST `/api/v1/admin/tasks` (wymaga `task.create`)
2. System przypisuje zadanie do użytkownika (`assigned_to`) i twórcy (`created_by`)
3. Użytkownik aktualizuje status (pending|in_progress|completed|cancelled|on_hold) i priorytet (low|medium|high|urgent)
4. System śledzi szacowane i rzeczywiste godziny pracy
5. Po ukończeniu system ustawia `completed_at`

**Pre-warunki**: Użytkownik zalogowany, uprawnienie `task.create`
**Post-warunki**: Zadanie utworzone, przypisane

## Bezpieczeństwo i Zgodność

### Autoryzacja i Autentykacja

- **Mechanizm**: Laravel Sanctum (token-based authentication)
- **Token**: Bearer token w nagłówku `Authorization`
- **Czas życia tokena**: DO USTALENIA - WYMAGA DANYCH
- **Refresh token**: DO USTALENIA - WYMAGA DANYCH

### Uprawnienia

- **System**: Spatie Laravel Permission
- **Tabele**: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`
- **Cache**: Uprawnienia cache'owane na 24 godziny
- **Przykładowe uprawnienia**:
  - `user.create`, `user.show`, `user.update`, `user.delete`, `user.list`, `user.change_password`
  - `company.create`, `company.update`, `company.delete`, `company.view`
  - `contact.create`, `contact.update`, `contact.delete`, `contact.view`
  - `task.create`, `task.update`, `task.delete`, `task.show`, `task.list`
  - `training.create`, `training.update`, `training.delete`
  - `training.user.assign`, `training.user.remove`, `training.user.assign_all`, `training.user.assign_by_role`, `training.user.assign_selected`, `training.user.list`
  - `training.file.attach`, `training.file.delete`, `training.file.list`
  - `training.category.create`, `training.category.update`, `training.category.delete`, `training.category.list`
  - `tool.create`, `tool.update`, `tool.delete`, `tool.show`, `tool.list`
  - `material.create`, `material.update`, `material.delete`, `material.show`, `material.list`
  - `estate.create`, `estate.update`, `estate.delete`, `estate.show`, `estate.list`
  - `plan.create`, `plan.delete`, `plan.list`
  - `pin.list.by.plan`, `user.pin.list.by.plan`, `user.pin.create`
  - `car.create`, `car.update`, `car.delete`, `car.show`, `car.list`
  - `city.create`, `city.update`, `city.delete`, `city.list`
  - `activity.list`

### Dane Osobowe (PII)

- **Przechowywane dane**: email, imię, nazwisko, telefon, miasto, województwo (użytkownicy); email, telefon (kontakty)
- **Szyfrowanie**: Hasła hashowane przez bcrypt
- **Soft deletes**: Większość tabel używa soft deletes (zachowanie danych)
- **GDPR**: DO USTALENIA - WYMAGA DANYCH (polityka usuwania, eksportu danych)

### Logowanie Zdarzeń

- **Mechanizm**: PSR-3 LoggerInterface (Monolog)
- **Logowane zdarzenia**: Błędy, tworzenie/aktualizacja/usunięcie firm, kontaktów, zadań (przykłady z `CompanyService`, `ContactService`)
- **Kontekst**: ID zasobów, nazwy, użytkownicy wykonujący akcje
- **Poziom logowania**: DO USTALENIA - WYMAGA DANYCH (prawdopodobnie info dla operacji, error dla błędów)

### CORS

- **Konfiguracja**: `config/cors.php`
- **Dozwolone źródła**: DO USTALENIA - WYMAGA DANYCH
- **Sanctum Stateful**: Włączone dla frontendu (`EnsureFrontendRequestsAreStateful`)

### Rate Limiting

- **Mechanizm**: Laravel ThrottleRequests middleware (na API routes)
- **Limity**: DO USTALENIA - WYMAGA DANYCH

## Wydajność i Skalowanie

### Baza Danych

- **Indeksy**: Wszystkie tabele mają indeksy na kluczowych polach (email, vat_id, status, foreign keys, deleted_at)
- **Paginacja**: Wszystkie listy endpointów wspierają paginację (domyślnie 15, max 100 na stronę)
- **Soft Deletes**: Indeksy na `deleted_at` dla wydajnych zapytań
- **Partial Indexes**: DO USTALENIA - WYMAGA DANYCH (możliwe z `deleted_at IS NULL`)

### Cache

- **Sterownik**: Redis (domyślnie)
- **Uprawnienia**: Cache'owane przez Spatie Permission (24 godziny)
- **Cache API**: DO USTALENIA - WYMAGA DANYCH (czy używane w serwisach)

### Kolejki

- **Sterownik**: Redis (domyślnie)
- **Długie operacje**: DO USTALENIA - WYMAGA DANYCH (które operacje są w kolejce)
- **Idempotency**: DO USTALENIA - WYMAGA DANYCH (czy implementowane)

### Limity

- **Paginacja**: Max 100 rekordów na stronę
- **Request size**: DO USTALENIA - WYMAGA DANYCH
- **Timeout**: DO USTALENIA - WYMAGA DANYCH

### SLA

DO USTALENIA - WYMAGA DANYCH

### Zagrożenia Wydajnościowe

- **N+1 queries**: Potencjalne w relacjach (companies ↔ users, contacts ↔ companies) - wymaga weryfikacji w kodzie
- **Duże kolekcje**: Paginacja obowiązkowa
- **Soft deletes**: Zapytania muszą uwzględniać `deleted_at IS NULL`

## Monitoring i Logowanie

### Eventy

- **System**: Laravel Events/Listeners
- **Przykładowe eventy**: DO USTALENIA - WYMAGA DANYCH (prawdopodobnie InvoiceCreated, UserCreated itp. zgodnie z architekturą)

### Metryki

DO USTALENIA - WYMAGA DANYCH

### Alerty

DO USTALENIA - WYMAGA DANYCH

### Logi

- **Lokalizacja**: `storage/logs/` (Laravel)
- **Format**: DO USTALENIA - WYMAGA DANYCH
- **Rotacja**: DO USTALENIA - WYMAGA DANYCH (prawdopodobnie daily)
- **Poziomy**: error, info (przykłady w serwisach)

## Testowanie i Jakość

### Strategia Testów

**Backend** (`backend/tests/`):
- **Unit Tests** (`Unit/`): Testy encji, value objects, utilities (25 plików)
- **Integration Tests** (`Integration/`): Testy serwisów + repozytoriów, eventów/listenerów (8 plików)
- **Feature Tests** (`Feature/`): Testy endpointów API, kontrakty JSON:API (49 plików)
- **Contract Tests** (`Contract/`): Testy zgodności z OpenAPI
- **E2E Tests** (`E2E/`): Smoke tests dla krytycznych ścieżek

**Frontend**:
- **Unit Tests**: Jest + React Testing Library (konfiguracja w `package.json`)
- **E2E Tests**: Playwright (konfiguracja w `package.json`)

**Mobile**:
- **Unit Tests**: Jest (konfiguracja w `package.json`)
- **E2E Tests**: DO USTALENIA - WYMAGA DANYCH

### Narzędzia

- **PHPUnit**: Backend unit/integration/feature tests
- **Pest**: DO USTALENIA - WYMAGA DANYCH (możliwe użycie)
- **Jest**: Frontend i mobile unit tests
- **Playwright**: Frontend E2E tests
- **MSW**: Mock Service Worker dla testów frontendowych

### Kontrakty API

- **OpenAPI/Swagger**: Dokumentacja w `backend/storage/api-docs/`, generowana automatycznie z adnotacji
- **JSON:API**: Wszystkie odpowiedzi API muszą być zgodne z JSON:API
- **Walidacja**: DO USTALENIA - WYMAGA DANYCH (czy są testy kontraktów)

### Przypadki Brzegowe

- **Unikalność**: VAT ID (companies), email (contacts, users)
- **Soft deletes**: Zapytania nie zwracają usuniętych rekordów
- **Foreign keys**: CASCADE dla companies → opportunities, SET NULL dla contacts → opportunities
- **Walidacja**: Enum values, required fields, format email

### Jakość Kodu

- **PHPStan/Larastan**: Static analysis (konfiguracja w `phpstan.neon`)
- **PHP CS Fixer**: Code style (konfiguracja w `pint.json`)
- **ESLint**: Frontend i mobile (konfiguracja w `eslint.config.mjs`)
- **TypeScript**: Strict mode dla frontendu i mobile

## Znane Ograniczenia

1. **Endpoint Opportunities**: Endpointy do zarządzania opportunities nie są widoczne w `routes/api.php` - DO USTALENIA
2. **Primary Keys**: Users używa `bigint` auto increment, podczas gdy większość innych encji używa UUID - niespójność
3. **Training Category**: Pole `category` w tabeli `trainings` jest stringiem, nie foreign key - DO USTALENIA czy to zamierzone
4. **Dashboard Stats**: Struktura odpowiedzi endpointu `/admin/dashboard/stats` nie jest znana
5. **Frontend API Routes**: Niektóre route handlers w `apps/web/src/app/api/` mogą być nieużywane (np. `test-put`) - DO WERYFIKACJI

## Changelog

### [ADD] Moduły i Funkcjonalności

- **CRM Module**: Dodano moduł zarządzania firmami i kontaktami
  - Encje: `Company`, `Contact`, `Opportunity`, `Pipeline`, `Stage`
  - Endpointy: `/admin/companies`, `/admin/contacts` (CRUD + bulk operations, link/unlink companies)
  - Enums: `CompanySource`, `CompanyStatus`, `ContactStatus`, `LeadLevel`, `OpportunityStatus`
  - Migracje: `create_companies_table`, `create_contacts_table`, `create_company_users_table`, `create_contact_company_table`, `create_opportunities_table`, `create_pipelines_table`, `create_stages_table`

- **Training Module**: Dodano moduł szkoleń
  - Endpointy: `/admin/trainings` (CRUD), `/admin/trainings/{training}/users` (assign/remove/all/by-role/selected), `/admin/trainings/{training}/files` (attach/delete/multiple), `/admin/training-categories` (CRUD)
  - Migracje: `create_trainings_table`, `create_training_user_table`, `create_training_files_table`, `create_trainings_categories_table`

- **Tasks Module**: Dodano moduł zadań
  - Endpointy: `/admin/tasks` (CRUD)
  - Enums: `TaskStatus`, `TaskPriority`
  - Migracja: `create_tasks_table`

- **Estates/Plans/Pins Module**: Dodano moduł nieruchomości
  - Endpointy: `/admin/estates` (CRUD), `/admin/plans/{estate}`, `/admin/pins/{plan}`, `/users/plans/{plan}/pins`
  - Migracje: `create_estates_table`, `create_plans_table`, `create_pins_table`

- **Tools/Materials/Cars/Cities Modules**: Dodano moduły katalogowe
  - Endpointy: `/admin/tools`, `/admin/materials`, `/admin/cars`, `/admin/cities` (CRUD)
  - Migracje: odpowiednie tabele

- **Activities Module**: Dodano moduł aktywności
  - Endpointy: `/admin/activities/list`
  - Migracja: `create_activities_table`

- **Auth Module**: Dodano autoryzację
  - Endpointy: `/auth/register`, `/auth/login`, `/auth/logout`, `/auth/me`, `/auth/forgot-password`, `/auth/reset-password`
  - Laravel Sanctum integration

- **Users Module**: Dodano zarządzanie użytkownikami
  - Endpointy: `/admin/users` (CRUD), `/users/{user}` (show/update), `/users/change-password`
  - Migracja: `create_users_table`

- **Dashboard Module**: Dodano dashboard
  - Endpointy: `/admin/dashboard/stats`

### [UPDATE] Zmiany w Istniejących Modułach

DO USTALENIA - WYMAGA DANYCH (brak informacji o poprzednich wersjach)

### [DEPRECATE] Przestarzałe Funkcjonalności

DO USTALENIA - WYMAGA DANYCH

## TODO / Luki Informacyjne

### Brakujące Dane Techniczne

1. **Wersja**: Aktualna wersja systemu (SEMVER)
2. **Commit**: Ostatni commit hash
3. **Production URL**: URL produkcyjny API i frontendu
4. **SLA**: Service Level Agreements, metryki dostępności
5. **Rate Limiting**: Dokładne limity requestów na endpoint/user
6. **Token Expiry**: Czas życia tokenów Sanctum, mechanizm refresh
7. **Feature Flags**: Lista feature flags i ich wartości
8. **Monitoring**: Narzędzia monitoringu (np. Sentry, New Relic), metryki, alerty
9. **Logging**: Format logów, rotacja, retencja
10. **Queue Jobs**: Które operacje są w kolejce, idempotency keys
11. **Cache Strategy**: Strategia cache'owania danych (poza uprawnieniami)
12. **GDPR**: Polityka przetwarzania danych osobowych, eksport, usuwanie
13. **Backup**: Strategia backupów bazy danych
14. **Deployment**: Proces wdrożenia, CI/CD pipeline
15. **Environment Variables**: Pełna lista zmiennych środowiskowych (production)

### Brakujące Endpointy

1. **Opportunities**: Endpointy CRUD dla opportunities (nie widoczne w routes)
2. **Pipelines/Stages**: Endpointy CRUD dla pipelines i stages (nie widoczne w routes)

### Brakujące Szczegóły Modeli

1. **pipelines**: Pola tabeli, relacje
2. **stages**: Pola tabeli, relacje
3. **training_categories**: Pola tabeli
4. **training_user**: Pola tabeli pivot
5. **training_files**: Pola tabeli
6. **activities**: Pola tabeli
7. **estates**: Pola tabeli
8. **plans**: Pola tabeli
9. **pins**: Pola tabeli
10. **tools**: Pola tabeli
11. **materials**: Pola tabeli
12. **cars**: Pola tabeli
13. **cities**: Pola tabeli

### Brakujące Szczegóły API

1. **Dashboard Stats**: Struktura odpowiedzi `/admin/dashboard/stats`
2. **Request/Response Examples**: Przykłady requestów i odpowiedzi dla wszystkich endpointów (poza companies)
3. **Error Codes**: Pełna lista kodów błędów i ich znaczeń
4. **Validation Rules**: Szczegółowe reguły walidacji dla wszystkich requestów

### Do Weryfikacji

1. **Frontend API Routes**: Czy wszystkie route handlers w `apps/web/src/app/api/` są używane
2. **Training Category**: Czy pole `category` w `trainings` powinno być foreign key
3. **User ID Type**: Czy `users.id` powinno być UUID dla spójności
4. **Mobile E2E**: Czy są testy E2E dla aplikacji mobilnej

## Słownik Pojęć

- **DDD (Domain-Driven Design)**: Architektura oparta na domenie biznesowej, z wyraźnym podziałem na warstwy Domain, Application, Infrastructure, API
- **Entity**: Encja domenowa, immutable, zawiera logikę biznesową (np. `Company`, `Contact`)
- **Repository**: Warstwa dostępu do danych, abstrakcja nad bazą danych
- **Service**: Warstwa aplikacyjna, orkiestruje przypadki użycia
- **DTO (Data Transfer Object)**: Obiekt transferu danych, readonly, tworzony przez Factory
- **Factory**: Klasa tworząca DTO z requestów
- **Resource**: Klasa transformująca dane do formatu JSON:API
- **JSON:API**: Standard formatowania odpowiedzi API
- **Soft Delete**: Logiczne usunięcie rekordu (flaga `deleted_at` zamiast fizycznego usunięcia)
- **Pipeline**: Pipeline sprzedażowy, sekwencja etapów (stages) prowadzących do zamknięcia transakcji
- **Stage**: Etap w pipeline'ie sprzedażowym
- **Opportunity**: Szansa sprzedażowa, potencjalna transakcja
- **Lead**: Potencjalny klient na wczesnym etapie
- **Contact**: Kontakt (osoba), może być przypisany do wielu firm
- **Account Manager**: Użytkownik przypisany do firmy jako opiekun
- **Sanctum**: Laravel Sanctum, system autoryzacji token-based
- **Spatie Permission**: Pakiet Laravel do zarządzania rolami i uprawnieniami

## Źródła Kontekstu

### Pliki Konfiguracyjne

- `backend/composer.json`: Zależności PHP, wersje
- `backend/package.json`: Zależności Node.js backendu
- `backend/config/app.php`: Konfiguracja aplikacji
- `backend/config/database.php`: Konfiguracja bazy danych
- `backend/config/auth.php`: Konfiguracja autoryzacji
- `backend/config/permission.php`: Konfiguracja Spatie Permission
- `backend/config/l5-swagger.php`: Konfiguracja Swagger/OpenAPI
- `backend/config/services.php`: Konfiguracja serwisów zewnętrznych
- `docker-compose.dev.yml`: Konfiguracja Docker Compose
- `package.json`: Konfiguracja monorepo (root)
- `apps/web/package.json`: Konfiguracja frontendu
- `apps/mobile/package.json`: Konfiguracja aplikacji mobilnej

### Routing i API

- `backend/routes/api.php`: Wszystkie endpointy API
- `backend/app/Http/Controllers/`: Kontrolery API
- `backend/app/Http/Controllers/Admin/Companies/CompanyController.php`: Przykładowy kontroler
- `backend/app/Http/Controllers/Auth/LoginController.php`: Kontroler logowania z adnotacjami OpenAPI

### Modele i Encje

- `backend/app/Models/`: Modele Eloquent
- `backend/app/Domain/`: Encje domenowe
- `backend/app/Domain/Crm/Entity/Company.php`: Encja Company
- `backend/app/Domain/Crm/Entity/Contact.php`: Encja Contact
- `backend/app/Models/Company.php`: Model Company
- `backend/app/Models/Contact.php`: Model Contact

### Migracje

- `backend/database/migrations/`: Wszystkie migracje bazy danych
- `backend/database/migrations/2025_10_18_213900_create_companies_table.php`: Migracja companies
- `backend/database/migrations/2025_10_18_214000_create_contacts_table.php`: Migracja contacts
- `backend/database/migrations/2025_01_27_140000_create_trainings_table.php`: Migracja trainings
- `backend/database/migrations/2025_01_15_000000_create_tasks_table.php`: Migracja tasks
- `backend/database/migrations/2025_10_18_204550_create_opportunities_table.php`: Migracja opportunities
- `backend/database/migrations/0001_01_01_000000_create_users_table.php`: Migracja users

### Enums

- `backend/app/Enums/Crm/CompanySource.php`: Źródła firm
- `backend/app/Enums/Crm/CompanyStatus.php`: Statusy firm
- `backend/app/Enums/Crm/ContactStatus.php`: Statusy kontaktów
- `backend/app/Enums/Crm/LeadLevel.php`: Poziomy leadów
- `backend/app/Enums/`: Inne enums (TaskStatus, TaskPriority, UserRoles, UserStatus)

### Serwisy i Repozytoria

- `backend/app/Services/CompanyService.php`: Serwis firm
- `backend/app/Repositories/CompanyRepository.php`: Repozytorium firm
- `backend/app/Services/`: Inne serwisy
- `backend/app/Repositories/`: Inne repozytoria

### Frontend

- `apps/web/src/app/`: Strony Next.js
- `apps/web/src/features/`: Moduły funkcjonalne frontendu
- `apps/web/src/shared/`: Współdzielone komponenty

### Mobile

- `apps/mobile/src/app/`: Ekrany aplikacji mobilnej
- `apps/mobile/src/shared/`: Współdzielone utilities

### Dokumentacja

- `README.md`: Ogólny opis projektu
- `backend/README.md`: Dokumentacja backendu
- `tasks.md`: Backlog zadań (informacje o modułach CRM)

### Reguły Architektury

- `.cursor/rules/backend-rules.mdc`: Reguły architektury backendu (zawsze zastosowane)
- `.cursor/rules/frontend-rules.mdc`: Reguły frontendu (do odczytania)

---

*Dokumentacja wygenerowana na podstawie analizy kodu źródłowego. Ostatnia aktualizacja: DO USTALENIA*



