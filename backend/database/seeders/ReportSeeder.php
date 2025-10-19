<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Reports\ReportSource;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

final class ReportSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->command) {
            $this->command->info('Seeding demo reports...');
        }

        $admin = User::first();

        if (! $admin) {
            if ($this->command) {
                $this->command->warn('No admin user found. Skipping report seeding.');
            }

            return;
        }

        // Active Companies Report
        Report::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => 'Active Companies Report',
            'description' => 'List of all active companies with their basic information',
            'source' => ReportSource::COMPANIES,
            'columns' => ['id', 'name', 'industry', 'status', 'region', 'created_at'],
            'filters' => [
                ['field' => 'status', 'operator' => 'eq', 'value' => 'active'],
            ],
            'sorting' => [
                ['field' => 'name', 'direction' => 'asc'],
            ],
            'created_by' => $admin->id,
            'is_public' => true,
        ]);

        // Recent Opportunities Report
        Report::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => 'Recent Opportunities',
            'description' => 'Opportunities created in the last 30 days',
            'source' => ReportSource::OPPORTUNITIES,
            'columns' => ['id', 'title', 'value', 'currency', 'probability', 'status', 'created_at'],
            'filters' => [
                ['field' => 'created_at', 'operator' => 'gte', 'value' => now()->subDays(30)->toDateString()],
            ],
            'sorting' => [
                ['field' => 'value', 'direction' => 'desc'],
            ],
            'created_by' => $admin->id,
            'is_public' => true,
        ]);

        // Overdue Invoices Report
        Report::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => 'Overdue Invoices',
            'description' => 'Invoices that are overdue for payment',
            'source' => ReportSource::INVOICES,
            'columns' => ['id', 'number', 'issue_date', 'due_date', 'amount', 'currency', 'status'],
            'filters' => [
                ['field' => 'status', 'operator' => 'eq', 'value' => 'overdue'],
            ],
            'sorting' => [
                ['field' => 'due_date', 'direction' => 'asc'],
            ],
            'created_by' => $admin->id,
            'is_public' => false,
        ]);

        if ($this->command) {
            $this->command->info('Demo reports seeded successfully!');
            $this->command->info('Created 3 demo reports: Active Companies, Recent Opportunities, Overdue Invoices');
        }
    }
}
