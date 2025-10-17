<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Activity\Entity\Activity;
use App\Repositories\ActivityRepository;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class ActivitySeeder extends Seeder
{
    public function __construct(
        private readonly ActivityRepository $activityRepository
    ) {}

    public function run(): void
    {
        $activities = [
            [
                'action' => 'Dodano nowe narzędzie',
                'user_name' => 'Jan Kowalski',
                'user_email' => 'jan.kowalski@skytech.pl',
                'entity_type' => 'tools',
                'entity_id' => 'tool_001',
                'created_at' => Carbon::now()->subMinutes(2),
            ],
            [
                'action' => 'Zaktualizowano materiał',
                'user_name' => 'Anna Nowak',
                'user_email' => 'anna.nowak@skytech.pl',
                'entity_type' => 'materials',
                'entity_id' => 'material_002',
                'created_at' => Carbon::now()->subMinutes(15),
            ],
            [
                'action' => 'Dodano nowy pojazd',
                'user_name' => 'Piotr Wiśniewski',
                'user_email' => 'piotr.wisniewski@skytech.pl',
                'entity_type' => 'cars',
                'entity_id' => 'car_003',
                'created_at' => Carbon::now()->subHour(),
            ],
            [
                'action' => 'Utworzono nową nieruchomość',
                'user_name' => 'Maria Kowalczyk',
                'user_email' => 'maria.kowalczyk@skytech.pl',
                'entity_type' => 'estates',
                'entity_id' => 'estate_004',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'action' => 'Zaktualizowano narzędzie',
                'user_name' => 'Tomasz Zieliński',
                'user_email' => 'tomasz.zielinski@skytech.pl',
                'entity_type' => 'tools',
                'entity_id' => 'tool_005',
                'created_at' => Carbon::now()->subHours(3),
            ],
            [
                'action' => 'Dodano nowy materiał',
                'user_name' => 'Katarzyna Lewandowska',
                'user_email' => 'katarzyna.lewandowska@skytech.pl',
                'entity_type' => 'materials',
                'entity_id' => 'material_006',
                'created_at' => Carbon::now()->subHours(4),
            ],
            [
                'action' => 'Zaktualizowano pojazd',
                'user_name' => 'Michał Dąbrowski',
                'user_email' => 'michal.dabrowski@skytech.pl',
                'entity_type' => 'cars',
                'entity_id' => 'car_007',
                'created_at' => Carbon::now()->subHours(5),
            ],
            [
                'action' => 'Dodano nową nieruchomość',
                'user_name' => 'Agnieszka Wójcik',
                'user_email' => 'agnieszka.wojcik@skytech.pl',
                'entity_type' => 'estates',
                'entity_id' => 'estate_008',
                'created_at' => Carbon::now()->subHours(6),
            ],
            [
                'action' => 'Usunięto narzędzie',
                'user_name' => 'Paweł Kamiński',
                'user_email' => 'pawel.kaminski@skytech.pl',
                'entity_type' => 'tools',
                'entity_id' => 'tool_009',
                'created_at' => Carbon::now()->subHours(7),
            ],
            [
                'action' => 'Zaktualizowano nieruchomość',
                'user_name' => 'Magdalena Szymańska',
                'user_email' => 'magdalena.szymanska@skytech.pl',
                'entity_type' => 'estates',
                'entity_id' => 'estate_010',
                'created_at' => Carbon::now()->subHours(8),
            ],
        ];

        foreach ($activities as $activityData) {
            $activity = Activity::create(
                action: $activityData['action'],
                userName: $activityData['user_name'],
                userEmail: $activityData['user_email'],
                entityType: $activityData['entity_type'],
                entityId: $activityData['entity_id']
            );

            // Override the created_at timestamp
            $activity = new Activity(
                id: $activity->id(),
                action: $activity->action(),
                userName: $activity->userName(),
                userEmail: $activity->userEmail(),
                entityType: $activity->entityType(),
                entityId: $activity->entityId(),
                createdAt: $activityData['created_at']
            );

            $this->activityRepository->save($activity);
        }
    }
}
