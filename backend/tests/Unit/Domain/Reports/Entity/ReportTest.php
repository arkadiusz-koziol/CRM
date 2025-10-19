<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reports\Entity;

use App\Domain\Reports\Entity\Report;
use Tests\TestCase;

final class ReportTest extends TestCase
{
    public function test_it_creates_report_with_all_required_fields(): void
    {
        $report = Report::create(
            'test-id',
            'Test Report',
            'Test Description',
            'companies',
            ['id', 'name', 'status'],
            [['field' => 'status', 'operator' => 'eq', 'value' => 'active']],
            [['field' => 'name', 'direction' => 'asc']],
            'user-123',
            true,
        );

        $this->assertEquals('test-id', $report->id());
        $this->assertEquals('Test Report', $report->name());
        $this->assertEquals('Test Description', $report->description());
        $this->assertEquals('companies', $report->source());
        $this->assertEquals(['id', 'name', 'status'], $report->columns());
        $this->assertEquals([['field' => 'status', 'operator' => 'eq', 'value' => 'active']], $report->filters());
        $this->assertEquals([['field' => 'name', 'direction' => 'asc']], $report->sorting());
        $this->assertEquals('user-123', $report->createdBy());
        $this->assertTrue($report->isPublic());
        $this->assertFalse($report->isDeleted());
    }

    public function test_it_updates_report_name(): void
    {
        $report = Report::create(
            'test-id',
            'Old Name',
            null,
            'companies',
            ['id', 'name'],
            null,
            null,
            'user-123',
        );

        $report->updateName('New Name');

        $this->assertEquals('New Name', $report->name());
    }

    public function test_it_updates_report_visibility(): void
    {
        $report = Report::create(
            'test-id',
            'Test Report',
            null,
            'companies',
            ['id', 'name'],
            null,
            null,
            'user-123',
            false,
        );

        $report->updateVisibility(true);

        $this->assertTrue($report->isPublic());
    }

    public function test_it_marks_report_as_deleted(): void
    {
        $report = Report::create(
            'test-id',
            'Test Report',
            null,
            'companies',
            ['id', 'name'],
            null,
            null,
            'user-123',
        );

        $this->assertFalse($report->isDeleted());

        $report->delete();

        $this->assertTrue($report->isDeleted());
        $this->assertNotNull($report->deletedAt());
    }

    public function test_it_restores_deleted_report(): void
    {
        $report = Report::create(
            'test-id',
            'Test Report',
            null,
            'companies',
            ['id', 'name'],
            null,
            null,
            'user-123',
        );

        $report->delete();
        $this->assertTrue($report->isDeleted());

        $report->restore();
        $this->assertFalse($report->isDeleted());
        $this->assertNull($report->deletedAt());
    }
}
