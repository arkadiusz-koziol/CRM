<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Export\Formatters;

use App\Services\Export\Formatters\CsvFormatter;
use PHPUnit\Framework\TestCase;

final class CsvFormatterTest extends TestCase
{
    private CsvFormatter $formatter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formatter = new CsvFormatter;
    }

    public function test_formats_data_without_headers(): void
    {
        $data = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ];

        $result = $this->formatter->format($data);

        $this->assertStringContainsString('"John Doe",john@example.com', $result);
        $this->assertStringContainsString('"Jane Smith",jane@example.com', $result);
    }

    public function test_formats_data_with_headers(): void
    {
        $data = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ];
        $headers = ['Name', 'Email'];

        $result = $this->formatter->format($data, $headers);

        $this->assertStringContainsString('Name,Email', $result);
        $this->assertStringContainsString('"John Doe",john@example.com', $result);
        $this->assertStringContainsString('"Jane Smith",jane@example.com', $result);
    }

    public function test_formats_empty_data(): void
    {
        $data = [];

        $result = $this->formatter->format($data);

        $this->assertIsString($result);
        $this->assertEmpty(trim($result));
    }

    public function test_formats_data_with_special_characters(): void
    {
        $data = [
            ['name' => 'John "The Boss" Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane, Smith', 'email' => 'jane@example.com'],
        ];

        $result = $this->formatter->format($data);

        $this->assertStringContainsString('"John ""The Boss"" Doe"', $result);
        $this->assertStringContainsString('"Jane, Smith"', $result);
    }

    public function test_formats_data_with_empty_values(): void
    {
        $data = [
            ['name' => 'John Doe', 'email' => ''],
            ['name' => '', 'email' => 'jane@example.com'],
        ];

        $result = $this->formatter->format($data);

        $this->assertStringContainsString('"John Doe",', $result);
        $this->assertStringContainsString(',jane@example.com', $result);
    }
}
