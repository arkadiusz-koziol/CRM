<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Reports;

use App\Domain\Reports\Entity\Report;
use App\Interfaces\Repositories\ReportRepositoryInterface;
use App\Services\Reports\ReportService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

final class ReportServiceTest extends TestCase
{
    private ReportService $service;

    private ReportRepositoryInterface $repository;

    private LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ReportRepositoryInterface::class);
        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->service = new ReportService($this->repository, $this->logger);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_report_successfully(): void
    {
        $this->repository->shouldReceive('save')->once();

        $this->logger->shouldReceive('info')->once()->with('Report created', Mockery::type('array'));

        $reportId = $this->service->create(
            'Test Report',
            'Test Description',
            'companies',
            ['id', 'name', 'status'],
            [['field' => 'status', 'operator' => 'eq', 'value' => 'active']],
            [['field' => 'name', 'direction' => 'asc']],
            'user-123',
            true,
        );

        $this->assertIsString($reportId);
    }

    public function test_it_throws_exception_for_invalid_source(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid report source: invalid-source');

        $this->service->create(
            'Test Report',
            'Test Description',
            'invalid-source',
            ['id', 'name'],
            null,
            null,
            'user-123',
        );
    }

    public function test_it_throws_exception_for_invalid_columns(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Column 'invalid_column' is not allowed for source 'companies'");

        $this->service->create(
            'Test Report',
            'Test Description',
            'companies',
            ['id', 'invalid_column'],
            null,
            null,
            'user-123',
        );
    }

    public function test_it_updates_report_successfully(): void
    {
        $report = Report::create(
            'test-id',
            'Original Report',
            'Original Description',
            'companies',
            ['id', 'name'],
            null,
            null,
            'user-123',
        );

        $this->repository->shouldReceive('findById')->with('test-id')->andReturn($report);
        $this->repository->shouldReceive('save')->once()->with($report);

        $this->logger->shouldReceive('info')->once()->with('Report updated', Mockery::type('array'));

        $this->service->update(
            'test-id',
            'Updated Report',
            'Updated Description',
            ['id', 'name'],
            null,
            null,
            false,
        );

        $this->assertEquals('Updated Report', $report->name());
        $this->assertEquals('Updated Description', $report->description());
    }

    public function test_it_throws_exception_when_updating_nonexistent_report(): void
    {
        $this->repository->shouldReceive('findById')->with('nonexistent-id')->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Report with ID nonexistent-id not found.');

        $this->service->update(
            'nonexistent-id',
            'Updated Report',
            null,
            ['id', 'name'],
            null,
            null,
            false,
        );
    }

    public function test_it_deletes_report_successfully(): void
    {
        $report = Report::create(
            'test-id',
            'Test Report',
            'Test Description',
            'companies',
            ['id', 'name'],
            null,
            null,
            'user-123',
        );

        $this->repository->shouldReceive('findById')->with('test-id')->andReturn($report);
        $this->repository->shouldReceive('save')->once()->with($report);

        $this->logger->shouldReceive('info')->once()->with('Report deleted', Mockery::type('array'));

        $this->service->delete('test-id');

        $this->assertTrue($report->isDeleted());
    }

    public function test_it_finds_reports_by_user(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository->shouldReceive('findByUser')
            ->with('user-123', 15)
            ->andReturn($paginator);

        $result = $this->service->findByUser('user-123', 15);

        $this->assertSame($paginator, $result);
    }

    public function test_it_finds_public_reports(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository->shouldReceive('findPublic')
            ->with(15)
            ->andReturn($paginator);

        $result = $this->service->findPublic(15);

        $this->assertSame($paginator, $result);
    }
}
