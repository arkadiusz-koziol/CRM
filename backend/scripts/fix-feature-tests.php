<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Finder\Finder;

echo "Fixing Feature tests...\n";

$finder = new Finder;
$finder->files()->in(__DIR__.'/../tests/Feature')->name('*.php');

foreach ($finder as $file) {
    $content = file_get_contents($file->getRealPath());

    // Skip if already fixed
    if (str_contains($content, 'RefreshDatabase') && str_contains($content, 'PermissionSeeder')) {
        echo "Skipping {$file->getRelativePathname()} - already fixed\n";

        continue;
    }

    // Replace DatabaseTransactions with RefreshDatabase
    $content = str_replace(
        'use Illuminate\Foundation\Testing\DatabaseTransactions;',
        'use Illuminate\Foundation\Testing\RefreshDatabase;',
        $content
    );

    // Add PermissionSeeder import if not present
    if (! str_contains($content, 'use Database\Seeders\PermissionSeeder;')) {
        $content = str_replace(
            'use Tests\TestCase;',
            "use Tests\TestCase;\nuse Database\Seeders\PermissionSeeder;",
            $content
        );
    }

    // Replace DatabaseTransactions trait with RefreshDatabase
    $content = str_replace(
        'use DatabaseTransactions;',
        'use RefreshDatabase;',
        $content
    );

    // Add setUp method if not present
    if (! str_contains($content, 'protected function setUp(): void')) {
        $content = str_replace(
            'use RefreshDatabase;',
            "use RefreshDatabase;\n\n    protected function setUp(): void\n    {\n        parent::setUp();\n        \$this->seed(PermissionSeeder::class);\n    }",
            $content
        );
    }

    file_put_contents($file->getRealPath(), $content);
    echo "Fixed {$file->getRelativePathname()}\n";
}

echo "Feature tests fixed!\n";
