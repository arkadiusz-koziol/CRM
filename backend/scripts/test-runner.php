<?php

declare(strict_types=1);

/**
 * Comprehensive Test Runner Script.
 *
 * This script runs all tests with coverage, mutation testing, and quality metrics.
 * It provides detailed reporting and enforces quality gates.
 */

require_once __DIR__.'/../vendor/autoload.php';

class TestRunner
{
    private array $config;

    private array $results = [];

    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->config = $this->loadConfig();
    }

    public function runAllTests(): array
    {
        echo "=== COMPREHENSIVE TEST RUNNER ===\n\n";

        $this->runInventory();
        $this->runUnitTests();
        $this->runIntegrationTests();
        $this->runFeatureTests();
        $this->runContractTests();
        $this->runE2ETests();
        $this->runMutationTests();
        $this->runQualityChecks();
        $this->generateReport();

        return $this->results;
    }

    private function loadConfig(): array
    {
        return [
            'coverage' => [
                'min_line' => 98,
                'min_branch' => 95,
                'min_mutation' => 80,
            ],
            'timeout' => 300,
            'parallel' => true,
            'verbose' => true,
        ];
    }

    private function runInventory(): void
    {
        echo "1. Running System Inventory...\n";

        $startTime = microtime(true);
        $command = "php {$this->basePath}/scripts/inventory.php";
        $output = shell_exec($command);
        $endTime = microtime(true);

        $this->results['inventory'] = [
            'status' => 'completed',
            'duration' => $endTime - $startTime,
            'output' => $output,
        ];

        echo '✓ System inventory completed in '.round($endTime - $startTime, 2)."s\n\n";
    }

    private function runUnitTests(): void
    {
        echo "2. Running Unit Tests...\n";

        $startTime = microtime(true);
        $command = "cd {$this->basePath} && ./vendor/bin/phpunit --testsuite=Unit --coverage-html=storage/app/coverage/unit --coverage-clover=storage/app/coverage/unit.xml";
        $output = shell_exec($command);
        $endTime = microtime(true);

        $this->results['unit_tests'] = [
            'status' => $this->parseTestStatus($output),
            'duration' => $endTime - $startTime,
            'output' => $output,
            'coverage' => $this->parseCoverage($output),
        ];

        echo '✓ Unit tests completed in '.round($endTime - $startTime, 2)."s\n";
        echo '  Coverage: '.($this->results['unit_tests']['coverage']['line'] ?? 'N/A').'% line, '.($this->results['unit_tests']['coverage']['branch'] ?? 'N/A')."% branch\n\n";
    }

    private function runIntegrationTests(): void
    {
        echo "3. Running Integration Tests...\n";

        $startTime = microtime(true);
        $command = "cd {$this->basePath} && ./vendor/bin/phpunit --testsuite=Integration --coverage-html=storage/app/coverage/integration --coverage-clover=storage/app/coverage/integration.xml";
        $output = shell_exec($command);
        $endTime = microtime(true);

        $this->results['integration_tests'] = [
            'status' => $this->parseTestStatus($output),
            'duration' => $endTime - $startTime,
            'output' => $output,
            'coverage' => $this->parseCoverage($output),
        ];

        echo '✓ Integration tests completed in '.round($endTime - $startTime, 2)."s\n";
        echo '  Coverage: '.($this->results['integration_tests']['coverage']['line'] ?? 'N/A').'% line, '.($this->results['integration_tests']['coverage']['branch'] ?? 'N/A')."% branch\n\n";
    }

    private function runFeatureTests(): void
    {
        echo "4. Running Feature Tests...\n";

        $startTime = microtime(true);
        $command = "cd {$this->basePath} && ./vendor/bin/phpunit --testsuite=Feature --coverage-html=storage/app/coverage/feature --coverage-clover=storage/app/coverage/feature.xml";
        $output = shell_exec($command);
        $endTime = microtime(true);

        $this->results['feature_tests'] = [
            'status' => $this->parseTestStatus($output),
            'duration' => $endTime - $startTime,
            'output' => $output,
            'coverage' => $this->parseCoverage($output),
        ];

        echo '✓ Feature tests completed in '.round($endTime - $startTime, 2)."s\n";
        echo '  Coverage: '.($this->results['feature_tests']['coverage']['line'] ?? 'N/A').'% line, '.($this->results['feature_tests']['coverage']['branch'] ?? 'N/A')."% branch\n\n";
    }

    private function runContractTests(): void
    {
        echo "5. Running Contract Tests...\n";

        $startTime = microtime(true);
        $command = "cd {$this->basePath} && ./vendor/bin/phpunit --testsuite=Contract --coverage-html=storage/app/coverage/contract --coverage-clover=storage/app/coverage/contract.xml";
        $output = shell_exec($command);
        $endTime = microtime(true);

        $this->results['contract_tests'] = [
            'status' => $this->parseTestStatus($output),
            'duration' => $endTime - $startTime,
            'output' => $output,
            'coverage' => $this->parseCoverage($output),
        ];

        echo '✓ Contract tests completed in '.round($endTime - $startTime, 2)."s\n";
        echo '  Coverage: '.($this->results['contract_tests']['coverage']['line'] ?? 'N/A').'% line, '.($this->results['contract_tests']['coverage']['branch'] ?? 'N/A')."% branch\n\n";
    }

    private function runE2ETests(): void
    {
        echo "6. Running E2E Tests...\n";

        $startTime = microtime(true);
        $command = "cd {$this->basePath} && ./vendor/bin/phpunit --testsuite=E2E --coverage-html=storage/app/coverage/e2e --coverage-clover=storage/app/coverage/e2e.xml";
        $output = shell_exec($command);
        $endTime = microtime(true);

        $this->results['e2e_tests'] = [
            'status' => $this->parseTestStatus($output),
            'duration' => $endTime - $startTime,
            'output' => $output,
            'coverage' => $this->parseCoverage($output),
        ];

        echo '✓ E2E tests completed in '.round($endTime - $startTime, 2)."s\n";
        echo '  Coverage: '.($this->results['e2e_tests']['coverage']['line'] ?? 'N/A').'% line, '.($this->results['e2e_tests']['coverage']['branch'] ?? 'N/A')."% branch\n\n";
    }

    private function runMutationTests(): void
    {
        echo "7. Running Mutation Tests...\n";

        $startTime = microtime(true);
        $command = "cd {$this->basePath} && ./vendor/bin/infection --configuration=infection.json5";
        $output = shell_exec($command);
        $endTime = microtime(true);

        $this->results['mutation_tests'] = [
            'status' => $this->parseMutationStatus($output),
            'duration' => $endTime - $startTime,
            'output' => $output,
            'mutation_score' => $this->parseMutationScore($output),
        ];

        echo '✓ Mutation tests completed in '.round($endTime - $startTime, 2)."s\n";
        echo '  Mutation Score: '.($this->results['mutation_tests']['mutation_score'] ?? 'N/A')."%\n\n";
    }

    private function runQualityChecks(): void
    {
        echo "8. Running Quality Checks...\n";

        $startTime = microtime(true);

        // Run PHP CS Fixer
        $csCommand = "cd {$this->basePath} && ./vendor/bin/pint --test";
        $csOutput = shell_exec($csCommand);

        // Run PHPStan
        $stanCommand = "cd {$this->basePath} && ./vendor/bin/phpstan analyse --memory-limit=2G";
        $stanOutput = shell_exec($stanCommand);

        $endTime = microtime(true);

        $this->results['quality_checks'] = [
            'status' => $this->parseQualityStatus($csOutput, $stanOutput),
            'duration' => $endTime - $startTime,
            'cs_fixer' => $csOutput,
            'phpstan' => $stanOutput,
        ];

        echo '✓ Quality checks completed in '.round($endTime - $startTime, 2)."s\n\n";
    }

    private function generateReport(): void
    {
        echo "9. Generating Comprehensive Report...\n";

        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'summary' => $this->generateSummary(),
            'results' => $this->results,
            'quality_gates' => $this->checkQualityGates(),
            'recommendations' => $this->generateRecommendations(),
        ];

        // Save report to file
        file_put_contents(
            $this->basePath.'/storage/app/test-report.json',
            json_encode($report, JSON_PRETTY_PRINT)
        );

        // Generate HTML report
        $this->generateHtmlReport($report);

        echo "✓ Report generated: storage/app/test-report.json\n";
        echo "✓ HTML report generated: storage/app/test-report.html\n\n";

        // Display summary
        $this->displaySummary($report);
    }

    private function parseTestStatus(string $output): string
    {
        if (str_contains($output, 'PASS')) {
            return 'passed';
        } elseif (str_contains($output, 'FAIL')) {
            return 'failed';
        } else {
            return 'unknown';
        }
    }

    private function parseCoverage(string $output): array
    {
        $coverage = ['line' => 0, 'branch' => 0];

        if (preg_match('/Lines:\s+(\d+\.\d+)%/', $output, $matches)) {
            $coverage['line'] = (float) $matches[1];
        }

        if (preg_match('/Branches:\s+(\d+\.\d+)%/', $output, $matches)) {
            $coverage['branch'] = (float) $matches[1];
        }

        return $coverage;
    }

    private function parseMutationStatus(string $output): string
    {
        if (str_contains($output, 'Infection ran successfully')) {
            return 'passed';
        } elseif (str_contains($output, 'Infection failed')) {
            return 'failed';
        } else {
            return 'unknown';
        }
    }

    private function parseMutationScore(string $output): float
    {
        if (preg_match('/MSI:\s+(\d+\.\d+)%/', $output, $matches)) {
            return (float) $matches[1];
        }

        return 0.0;
    }

    private function parseQualityStatus(string $csOutput, string $stanOutput): string
    {
        $csPassed = ! str_contains($csOutput, 'Error');
        $stanPassed = ! str_contains($stanOutput, 'Error');

        return ($csPassed && $stanPassed) ? 'passed' : 'failed';
    }

    private function generateSummary(): array
    {
        $totalTests = 0;
        $passedTests = 0;
        $failedTests = 0;
        $totalDuration = 0;
        $totalCoverage = ['line' => 0, 'branch' => 0];
        $coverageCount = 0;

        foreach ($this->results as $result) {
            if (isset($result['status'])) {
                $totalTests++;
                if ($result['status'] === 'passed') {
                    $passedTests++;
                } elseif ($result['status'] === 'failed') {
                    $failedTests++;
                }
            }

            if (isset($result['duration'])) {
                $totalDuration += $result['duration'];
            }

            if (isset($result['coverage'])) {
                $totalCoverage['line'] += $result['coverage']['line'];
                $totalCoverage['branch'] += $result['coverage']['branch'];
                $coverageCount++;
            }
        }

        if ($coverageCount > 0) {
            $totalCoverage['line'] /= $coverageCount;
            $totalCoverage['branch'] /= $coverageCount;
        }

        return [
            'total_tests' => $totalTests,
            'passed_tests' => $passedTests,
            'failed_tests' => $failedTests,
            'total_duration' => $totalDuration,
            'average_coverage' => $totalCoverage,
        ];
    }

    private function checkQualityGates(): array
    {
        $gates = [
            'line_coverage' => false,
            'branch_coverage' => false,
            'mutation_score' => false,
            'code_style' => false,
            'static_analysis' => false,
        ];

        // Check coverage gates
        if (isset($this->results['unit_tests']['coverage']['line'])) {
            $gates['line_coverage'] = $this->results['unit_tests']['coverage']['line'] >= $this->config['coverage']['min_line'];
        }

        if (isset($this->results['unit_tests']['coverage']['branch'])) {
            $gates['branch_coverage'] = $this->results['unit_tests']['coverage']['branch'] >= $this->config['coverage']['min_branch'];
        }

        // Check mutation score
        if (isset($this->results['mutation_tests']['mutation_score'])) {
            $gates['mutation_score'] = $this->results['mutation_tests']['mutation_score'] >= $this->config['coverage']['min_mutation'];
        }

        // Check quality gates
        if (isset($this->results['quality_checks']['status'])) {
            $gates['code_style'] = $this->results['quality_checks']['status'] === 'passed';
            $gates['static_analysis'] = $this->results['quality_checks']['status'] === 'passed';
        }

        return $gates;
    }

    private function generateRecommendations(): array
    {
        $recommendations = [];

        // Check coverage recommendations
        if (isset($this->results['unit_tests']['coverage']['line']) &&
            $this->results['unit_tests']['coverage']['line'] < $this->config['coverage']['min_line']) {
            $recommendations[] = 'Line coverage is below threshold. Add more unit tests to improve coverage.';
        }

        if (isset($this->results['unit_tests']['coverage']['branch']) &&
            $this->results['unit_tests']['coverage']['branch'] < $this->config['coverage']['min_branch']) {
            $recommendations[] = 'Branch coverage is below threshold. Add more test cases to cover all branches.';
        }

        // Check mutation score recommendations
        if (isset($this->results['mutation_tests']['mutation_score']) &&
            $this->results['mutation_tests']['mutation_score'] < $this->config['coverage']['min_mutation']) {
            $recommendations[] = 'Mutation score is below threshold. Improve test quality and add more assertions.';
        }

        // Check quality recommendations
        if (isset($this->results['quality_checks']['status']) &&
            $this->results['quality_checks']['status'] === 'failed') {
            $recommendations[] = 'Code style or static analysis issues detected. Fix formatting and type issues.';
        }

        return $recommendations;
    }

    private function generateHtmlReport(array $report): void
    {
        $html = $this->generateHtmlTemplate($report);
        file_put_contents($this->basePath.'/storage/app/test-report.html', $html);
    }

    private function generateHtmlTemplate(array $report): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Test Report - '.date('Y-m-d H:i:s')."</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { background: #f0f0f0; padding: 20px; border-radius: 5px; }
                .summary { background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .failed { background: #ffe8e8; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .recommendations { background: #fff8e8; padding: 15px; border-radius: 5px; margin: 20px 0; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Test Report</h1>
                <p>Generated: ".$report['timestamp']."</p>
            </div>
            
            <div class='summary'>
                <h2>Summary</h2>
                <p>Total Tests: ".$report['summary']['total_tests'].'</p>
                <p>Passed: '.$report['summary']['passed_tests'].'</p>
                <p>Failed: '.$report['summary']['failed_tests'].'</p>
                <p>Duration: '.round($report['summary']['total_duration'], 2).'s</p>
                <p>Average Coverage: '.round($report['summary']['average_coverage']['line'], 2).'% line, '.round($report['summary']['average_coverage']['branch'], 2)."% branch</p>
            </div>
            
            <div class='recommendations'>
                <h2>Recommendations</h2>
                <ul>
                    ".implode('', array_map(fn ($rec) => "<li>$rec</li>", $report['recommendations'])).'
                </ul>
            </div>
        </body>
        </html>
        ';
    }

    private function displaySummary(array $report): void
    {
        echo "=== TEST SUMMARY ===\n";
        echo 'Total Tests: '.$report['summary']['total_tests']."\n";
        echo 'Passed: '.$report['summary']['passed_tests']."\n";
        echo 'Failed: '.$report['summary']['failed_tests']."\n";
        echo 'Duration: '.round($report['summary']['total_duration'], 2)."s\n";
        echo 'Average Coverage: '.round($report['summary']['average_coverage']['line'], 2).'% line, '.round($report['summary']['average_coverage']['branch'], 2)."% branch\n";

        if (! empty($report['recommendations'])) {
            echo "\nRecommendations:\n";
            foreach ($report['recommendations'] as $recommendation) {
                echo "- $recommendation\n";
            }
        }

        echo "\n=== QUALITY GATES ===\n";
        foreach ($report['quality_gates'] as $gate => $passed) {
            $status = $passed ? '✓' : '✗';
            echo "$status ".ucfirst(str_replace('_', ' ', $gate))."\n";
        }

        echo "\n";
    }
}

// Run the test runner
$runner = new TestRunner(__DIR__.'/..');
$results = $runner->runAllTests();

echo "Test runner completed successfully!\n";
