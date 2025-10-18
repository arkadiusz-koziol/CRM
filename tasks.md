Tasks Backlog
<!--GLOBAL_LOCK: free-->
TASK: TSK-100

title: "Domain: Contacts & Companies – Model & Migrations #100"
branch: "feature/tsk-100-contacts-companies-domain"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "DONE"
last_update: "2025-01-27T12:20:00+00:00"
lock: "free"
checksum: ""

Acceptance Criteria
•	Dodane tabele: companies, contacts, company_users (pivot: account managerzy do firm), contact_company (pivot: wiele kontaktów do wielu firm)
•	Pola minimalne:
•	companies: id(uuid), name, industry(nullable), source(enum), status(enum: active/inactive/prospect), region(nullable), vat_id(nullable, unique), created_by
•	contacts: id(uuid), first_name, last_name, email(unique), phone(nullable), lead_level(enum: lead/contact), owner_user_id(nullable), source(enum), status(enum: new/active/dormant/lost)
•	Indeksy pod wyszukiwanie po vat_id, email, name
•	Soft deletes na wszystkich tabelach
•	Seedery z danymi przykładowymi (min. 10 firm, 30 kontaktów) oraz relacjami
•	OpenAPI: definicje schematów Company/Contact

Work Notes (by dev)
Commits (completed):
•	feat(domain): add Company/Contact aggregates with migrations and enums
•	feat(seed): add Companies/Contacts seeders with sample data
•	docs(openapi): add schemas for Company/Contact
•	test: comprehensive unit and integration tests for CRM domain

Files Changed (completed):
•	database/migrations/2025_10_18_213900_create_companies_table.php
•	database/migrations/2025_10_18_214000_create_contacts_table.php
•	database/migrations/2025_10_18_214100_create_company_users_table.php
•	database/migrations/2025_10_18_214200_create_contact_company_table.php
•	app/Domain/Crm/Entity/Company.php
•	app/Domain/Crm/Entity/Contact.php
•	app/Models/Company.php
•	app/Models/Contact.php
•	database/seeders/CompanySeeder.php
•	database/seeders/ContactSeeder.php
•	app/Enums/Crm/CompanySource.php, CompanyStatus.php, LeadLevel.php, ContactStatus.php
•	openapi/schemas/company.yaml, openapi/schemas/contact.yaml
•	tests/Unit/Domain/Crm/Entity/CompanyTest.php
•	tests/Unit/Domain/Crm/Entity/ContactTest.php
•	tests/Integration/Database/Migrations/CompanyMigrationTest.php
•	tests/Integration/Database/Migrations/ContactMigrationTest.php
•	tests/Integration/Database/Seeders/CompanySeederTest.php
•	tests/Integration/Database/Seeders/ContactSeederTest.php

Tests (completed):
•	39 tests passed with 432 assertions
•	Unit tests: Domain entities with all enum values and edge cases
•	Integration tests: Database migrations with constraints and soft deletes
•	Seeder tests: Sample data validation with 10 companies and 30 contacts
•	Code style: All files formatted with Laravel Pint

Review Notes
LGTM - All acceptance criteria met. Domain entities properly implemented with DDD structure, comprehensive migrations with proper indexes and constraints, complete seeders with sample data, OpenAPI schemas, and comprehensive unit tests. Code follows all architectural rules with proper separation of concerns.

Technical Notes:
•	DDD: New bounded context Crm with proper domain entities
•	UUID v7 for primary keys, foreign keys use bigint for user relationships
•	Snake_case naming, proper indexes for search performance
•	Soft deletes implemented on all tables
•	OpenAPI schemas created for API documentation
•	All tests passing with comprehensive coverage

⸻

TASK: TSK-101

title: “API: Companies – CRUD + List/Filter #101”
branch: “feature/tsk-101-companies-api”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:11:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Endpointy (JSON:API): list (paginacja, search name|vat_id|region|status|source), show, create, update, delete(soft)
•	Walidacje: unikalny vat_id, format emaili w kontaktach linkowanych
•	Uprawnienia: company.view, company.create, company.update, company.delete
•	Dodanie account_manager (powiązanie user→company) – filtr „moje firmy”
•	OpenAPI kompletne z przykładami
•	Aktualizacja seederów ról/pozwoleń

Work Notes (by dev)
Commits (plan):
•	feat(api): Company controller + resource + repository
•	feat(auth): spatie permissions for company*
•	test: feature tests for filters/search/sort/pagination

Files Changed (plan):
•	app/Interfaces/Repositories/CompanyRepositoryInterface.php
•	app/Repositories/CompanyRepository.php
•	app/Services/CompanyService.php
•	app/Http/Controllers/Admin/Companies/*Controller.php
•	app/Http/Resources/CompanyResource.php, CompanyCollection.php
•	routes/api.php
•	database/seeders/PermissionSeeder.php (nowe scopes)

Tests (plan):
•	20 feature tests: filtry, sorty, ACL, soft delete
•	6 unit tests: service/repo

Technical Notes:
•	Wspólny EloquentRepository base
•	JSON:API meta: total, per-page limits (1–100)

⸻

TASK: TSK-102

title: “API: Contacts – CRUD + Linkowanie do Companies #102”
branch: “feature/tsk-102-contacts-api”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:12:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Endpointy: list (search name|email|phone|status|lead_level|source), show, create, update, delete
•	Masowe przypięcie/odpięcie kontaktów do firmy (N:M)
•	Walidacja unikalnego email
•	Pola rozszerzone: owner_user_id (Account/BDR)
•	Uprawnienia: contact.view|create|update|delete
•	Seedery ról/pozwoleń zaktualizowane

Work Notes (by dev)
Commits (plan):
•	feat(api): Contacts endpoints + mass attach/detach to company
•	test: feature+unit for attach/detach, filters

Files Changed (plan):
•	app/.../ContactRepositoryInterface.php, ContactRepository.php
•	app/Services/ContactService.php
•	app/Http/Controllers/Admin/Contacts/*Controller.php
•	app/Http/Requests/Contacts/*Request.php
•	app/Http/Resources/ContactResource.php
•	routes/api.php

Tests (plan):
•	18 feature tests: attach/detach, ACL, walidacje
•	6 unit tests: service/repo

Technical Notes:
•	Zgodność z JSON:API i OpenAPI
•	Paginacja + sortowanie po last_name, status

⸻

TASK: TSK-110

title: “Leads & Opportunities – Pipeline Domain + Enums #110”
branch: “feature/tsk-110-pipeline-domain”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:13:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Tabele: pipelines, stages (ordered), opportunities
•	Etapy domyślne: prospecting → demo → oferta → negocjacje → wygrana/przegrana
•	Opportunity: id, title, company_id, contact_id(nullable), value(decimal), currency, probability(0–100), stage_id, owner_user_id, close_date(nullable), status(open/won/lost)
•	Seedery: domyślny pipeline + etapy
•	Audyt zmian etapu i probability (activity log)

Work Notes (by dev)
Commits (plan):
•	feat(domain): pipeline/opportunity entities+migrations
•	feat(seed): default pipeline with ordered stages
•	feat(audit): stage change logged in activities

Files Changed (plan):
•	database/migrations/*_create_pipelines_table.php
•	database/migrations/*_create_stages_table.php
•	database/migrations/*_create_opportunities_table.php
•	app/Domain/Crm/Entity/*
•	database/seeders/PipelineSeeder.php

Tests (plan):
•	Repo tests: ordering stages, probability bounds
•	Seeder test: istnieją wszystkie etapy

⸻

TASK: TSK-111

title: “API: Opportunities – CRUD + Kanban List #111”
branch: “feature/tsk-111-opportunities-api-kanban”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:14:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Endpointy: list (grupowanie po stage_id do widoku Kanban), show, create, update (w tym drag&drop zmiana stage), delete
•	Walidacje: value >= 0, probability 0–100, close_date >= today (opcjonalnie)
•	Filtry: owner, company, stage, status, date range
•	Uprawnienia: opportunity.*
•	OpenAPI + przykładowe ładunki do Kanbana

Work Notes (by dev)
Commits (plan):
•	feat(api): Opportunities CRUD + Kanban serializacja
•	test: feature for kanban grouping + DnD stage change

Files Changed (plan):
•	app/.../OpportunityRepositoryInterface.php, OpportunityRepository.php
•	app/Services/OpportunityService.php
•	app/Http/Controllers/Admin/Opportunities/*Controller.php
•	app/Http/Resources/OpportunityResource.php, OpportunityKanbanColumnResource.php
•	routes/api.php

Tests (plan):
•	22 feature tests: filtry, przenoszenie etapów, ACL
•	6 unit: service/repo

Technical Notes:
•	Emisja eventu OpportunityStageChanged (do automatyzacji/notify)

⸻

TASK: TSK-120

title: “Contracts & Invoices – Minimal Domain #120”
branch: “feature/tsk-120-contracts-invoices-domain”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:15:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Tabele: contracts (nr, company_id, start_at, end_at, amount, currency, status: draft/active/expired/terminated), invoices (nr, contract_id(nullable), company_id, issue_date, due_date, amount, currency, status: issued/paid/overdue/cancelled)
•	Indeksy po nr, company_id, daty
•	Seedery demonstracyjne (po 5 umów i 20 faktur)

Work Notes (by dev)
Commits (plan):
•	feat(domain): contracts/invoices migrations+entities
•	feat(seed): demo data for contracts/invoices

Files Changed (plan):
•	database/migrations/*_create_contracts_table.php
•	database/migrations/*_create_invoices_table.php
•	app/Domain/Billing/Entity/Contract.php, Invoice.php
•	database/seeders/BillingSeeder.php

Tests (plan):
•	Repo tests: spójność relacji; status transitions

⸻

TASK: TSK-121

title: “API: Contracts & Invoices – Lifecycle #121”
branch: “feature/tsk-121-contracts-invoices-api”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:16:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Endpointy CRUD dla umów i faktur
•	Masowe akcje: oznacz jako „paid/overdue/cancelled”
•	Walidacje dat (end_at >= start_at; due_date >= issue_date)
•	Zdarzenia domenowe: ContractActivated, InvoicePaid, InvoiceOverdue
•	OpenAPI kompletne
•	Uprawnienia: contract.*, invoice.*

Work Notes (by dev)
Commits (plan):
•	feat(api): contracts/invoices controllers+services+resources
•	feat(events): domain events + listeners (activity log)
•	test: feature/unit

Files Changed (plan):
•	app/Services/Billing/*Service.php
•	app/Http/Controllers/Admin/Contracts/*Controller.php
•	app/Http/Controllers/Admin/Invoices/*Controller.php
•	app/Http/Resources/Billing/*Resource.php
•	routes/api.php

Tests (plan):
•	24 feature: masowe akcje, ACL, walidacje
•	8 unit: services/repositories

⸻

TASK: TSK-130

title: “Workflow Engine – Rules & Scheduler #130”
branch: “feature/tsk-130-workflow-engine”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:17:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Domain: workflows, workflow_rules (JSON condition builder), workflow_actions
•	Typy akcji: create_task, send_email, send_sms, add_tag, assign_owner
•	Przykładowa reguła: „user last_login_at > 30 dni → create_task follow-up”
•	Harmonogram: job co 15 min przetwarza reguły (chunking, idempotency)
•	Activity log + metrics (executed/failed)
•	Seeder z 2 przykładowymi regułami

Work Notes (by dev)
Commits (plan):
•	feat(domain): workflow rules/actions + migrations
•	feat(job): scheduled evaluator with Redis locks
•	feat(seed): demo rules
•	docs: OpenAPI for rule CRUD

Files Changed (plan):
•	app/Domain/Automation/*
•	app/Jobs/Workflow/ProcessRulesJob.php
•	app/Services/Automation/WorkflowService.php
•	routes/api.php (CRUD workflow)
•	config/automation.php

Tests (plan):
•	Unit: predicate evaluation, idempotency keys
•	Feature: end-to-end rule triggers task creation

Technical Notes:
•	Bezpieczna ewaluacja JSON rules (whitelist operatorów: eq, ne, gt, lt, in, between, exists)

⸻

TASK: TSK-131

title: “Notifications Real-time – WebSockets + Pub/Sub #131”
branch: “feature/tsk-131-realtime-notifications”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:18:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Broadcasting via Laravel Echo (socket.io) + Redis
•	Zdarzenia: task assigned, comment added, status changed, opportunity stage changed
•	Powiadomienia dostępowe (tylko właściciel/obserwatorzy)
•	Rate limiting i reconnect strategy
•	OpenAPI: kanały i payloady (dok. techniczna)

Work Notes (by dev)
Commits (plan):
•	feat(realtime): broadcast channels + events
•	feat(front-contract): contract for events payload

Files Changed (plan):
•	config/broadcasting.php, routes/channels.php
•	app/Events/*
•	app/Notifications/* (mosty do e-mail/SMS w razie offline)
•	docs/realtime/contract.md

Tests (plan):
•	Unit: authorization callbacks for channels
•	Feature: events fire & payload structure
TASK: TSK-140

title: “Analytics – Business Dashboards API #140”
branch: “feature/tsk-140-dashboards-kpis”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:19:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Endpoint /v1/admin/dashboard/kpi zwraca: aktywni klienci, wykonane zadania (30d), % ukończonych szkoleń, wykorzystanie materiałów
•	Cache Redis (TTL 5 min), If-None-Match ETag
•	Testy poprawności i wydajności (czas < 300ms na zimno dla 10k rekordów – przy danych testowych)

Work Notes (by dev)
Commits (plan):
•	feat(analytics): KPI service + caching + ETag
•	test(perf): base perf tests

Files Changed (plan):
•	app/Services/Analytics/DashboardService.php
•	app/Http/Controllers/Admin/Dashboard/KpiController.php
•	routes/api.php

Tests (plan):
•	Feature: response shape, cache hit
•	Perf: simple benchmark in test suite

⸻

TASK: TSK-141

title: “Custom Reports – Query Builder + Saved Reports #141”
branch: “feature/tsk-141-custom-reports”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:20:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Tabele: reports, report_runs, report_filters (JSON)
•	Obsługa źródeł: users, companies, contacts, tasks, opportunities, invoices
•	Walidacja pól i projekcji (whitelist kolumn)
•	Zapisywanie i uruchamianie raportów (async job → CSV link)
•	Uprawnienia: report.*
•	Seeder 3 gotowych raportów

Work Notes (by dev)
Commits (plan):
•	feat(reports): domain+api for saved reports
•	feat(queue): async generation + storage links

Files Changed (plan):
•	app/Domain/Reports/*
•	app/Jobs/Reports/RunReportJob.php
•	app/Http/Controllers/Admin/Reports/*Controller.php
•	app/Http/Resources/Report*Resource.php
•	routes/api.php

Tests (plan):
•	Feature: create/run/download, ACL
•	Unit: validator for column whitelist

⸻

TASK: TSK-142

title: “Exports – CSV/XLSX/PDF with Branding #142”
branch: “feature/tsk-142-exports-branding”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:21:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Wspólny serwis ExportService (csv, xlsx, pdf)
•	Branding (logo, stopka) w PDF/XLSX
•	Limit rozmiaru i strumieniowanie (chunk)
•	Testy: porównanie nagłówków, rozmiarów i mime types

Work Notes (by dev)
Commits (plan):
•	feat(export): unified export service + templates
•	test: export tests for 3 formats

Files Changed (plan):
•	app/Services/Export/ExportService.php
•	resources/views/exports/*
•	config/export.php

Tests (plan):
•	Unit: formatters
•	Feature: endpoints generate downloadable files

⸻

TASK: TSK-150

title: “Collaboration: Comments & Notes on Entities #150”
branch: “feature/tsk-150-comments-notes”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:22:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Polimorficzne comments (task, company, contact, opportunity, estate)
•	Markdown + sanitization
•	ACL: widoczność wg ról i zespołów
•	Activity log dla komentarzy

Work Notes (by dev)
Commits (plan):
•	feat(collab): comments polymorphic model+api
•	test: feature ACL + XSS sanitize

Files Changed (plan):
•	database/migrations/*_create_comments_table.php
•	app/Domain/Collab/Entity/Comment.php
•	app/Http/Controllers/Comments/*Controller.php
•	app/Http/Resources/CommentResource.php

Tests (plan):
•	15 feature tests: CRUD, ACL, sanitize

⸻

TASK: TSK-151

title: “Mentions & Tagging (@user) #151”
branch: “feature/tsk-151-mentions-tagging”
assignee: “cursor-dev”
reviewer: “openai-reviewer”
status: “TODO”
last_update: “2025-10-18T21:23:00+02:00”
lock: “free”
checksum: “”

Acceptance Criteria
•	Parsowanie @UserName w komentarzach → notyfikacja WebSocket+email
•	Opt-out per user
•	Mention log (kto kogo, kiedy, gdzie)
•	Testy edge-case (duplikaty, usunięci użytkownicy)

Work Notes (by dev)
Commits (plan):
•	feat(collab): mentions parser + notifications
•	test: mention scenarios

Files Changed (plan):
•	app/Services/Collab/MentionService.php
•	app/Listeners/Comments/DispatchMentions.php
•	app/Events/CommentCreated.php

Tests (plan):
•	Feature: mentions deliver notifications
•	Unit: parser correctness
