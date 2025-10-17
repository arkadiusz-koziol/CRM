<?php

declare(strict_types=1);

/**
 * System Inventory and Risk Assessment Script.
 *
 * This script analyzes the codebase to create a comprehensive inventory
 * of modules, classes, and methods with risk assessment for testing prioritization.
 */

require_once __DIR__.'/../vendor/autoload.php';

class SystemInventory
{
    private array $inventory = [];

    private array $riskAssessment = [];

    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function generateInventory(): array
    {
        $this->scanDirectory($this->basePath.'/app');
        $this->assessRisk();

        return [
            'inventory' => $this->inventory,
            'risk_assessment' => $this->riskAssessment,
            'summary' => $this->generateSummary(),
        ];
    }

    private function scanDirectory(string $directory): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $this->analyzeFile($file->getPathname());
            }
        }
    }

    private function analyzeFile(string $filePath): void
    {
        $relativePath = str_replace($this->basePath.'/', '', $filePath);
        $content = file_get_contents($filePath);

        // Skip if file is empty or contains only whitespace
        if (empty(trim($content))) {
            return;
        }

        $reflection = $this->createReflectionFromFile($filePath);

        if (! $reflection) {
            return;
        }

        $classInfo = [
            'file' => $relativePath,
            'class' => $reflection->getName(),
            'namespace' => $reflection->getNamespaceName(),
            'type' => $this->determineClassType($reflection),
            'methods' => [],
            'properties' => [],
            'interfaces' => $reflection->getInterfaceNames(),
            'parent_class' => $reflection->getParentClass() ? $reflection->getParentClass()->getName() : null,
            'is_abstract' => $reflection->isAbstract(),
            'is_final' => $reflection->isFinal(),
            'is_readonly' => $reflection->isReadOnly(),
            'lines_of_code' => $this->countLinesOfCode($content),
            'cyclomatic_complexity' => $this->calculateCyclomaticComplexity($content),
            'dependencies' => $this->extractDependencies($content),
            'io_operations' => $this->detectIOOperations($content),
            'business_critical' => $this->isBusinessCritical($reflection, $content),
        ];

        // Analyze methods
        foreach ($reflection->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }

            $classInfo['methods'][] = [
                'name' => $method->getName(),
                'visibility' => $this->getMethodVisibility($method),
                'is_static' => $method->isStatic(),
                'is_abstract' => $method->isAbstract(),
                'is_final' => $method->isFinal(),
                'parameters' => $method->getNumberOfParameters(),
                'lines_of_code' => $this->countMethodLines($method, $content),
                'cyclomatic_complexity' => $this->calculateMethodComplexity($method, $content),
                'has_side_effects' => $this->hasSideEffects($method, $content),
                'returns_value' => $method->hasReturnType(),
                'throws_exceptions' => $this->throwsExceptions($method, $content),
            ];
        }

        $this->inventory[] = $classInfo;
    }

    private function createReflectionFromFile(string $filePath): ?ReflectionClass
    {
        try {
            // Extract class name from file content
            $content = file_get_contents($filePath);
            $tokens = token_get_all($content);

            $namespace = '';
            $class = null;
            $inNamespace = false;

            foreach ($tokens as $token) {
                if (is_array($token)) {
                    if ($token[0] === T_NAMESPACE) {
                        $inNamespace = true;
                    } elseif ($token[0] === T_STRING && $inNamespace) {
                        $namespace = $token[1];
                        $inNamespace = false;
                    } elseif ($token[0] === T_CLASS || $token[0] === T_INTERFACE || $token[0] === T_TRAIT) {
                        // Find the class name
                        $nextToken = next($tokens);
                        if (is_array($nextToken) && $nextToken[0] === T_STRING) {
                            $class = $nextToken[1];

                            break;
                        }
                    }
                }
            }

            if ($class) {
                $fullClassName = $namespace ? $namespace.'\\'.$class : $class;

                return new ReflectionClass($fullClassName);
            }
        } catch (Exception $e) {
            // Skip files that can't be reflected
        }

        return null;
    }

    private function determineClassType(ReflectionClass $reflection): string
    {
        $className = $reflection->getName();
        $namespace = $reflection->getNamespaceName();

        if (str_contains($namespace, 'Domain\\')) {
            return 'domain_entity';
        } elseif (str_contains($namespace, 'Services\\')) {
            return 'service';
        } elseif (str_contains($namespace, 'Repositories\\')) {
            return 'repository';
        } elseif (str_contains($namespace, 'Http\\Controllers\\')) {
            return 'controller';
        } elseif (str_contains($namespace, 'Dto\\')) {
            return 'dto';
        } elseif (str_contains($namespace, 'Factory\\')) {
            return 'factory';
        } elseif (str_contains($namespace, 'Enums\\')) {
            return 'enum';
        } elseif (str_contains($namespace, 'Models\\')) {
            return 'model';
        } elseif ($reflection->isInterface()) {
            return 'interface';
        } elseif ($reflection->isTrait()) {
            return 'trait';
        }

        return 'other';
    }

    private function countLinesOfCode(string $content): int
    {
        $lines = explode("\n", $content);
        $codeLines = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (! empty($line) && ! str_starts_with($line, '//') && ! str_starts_with($line, '/*') && ! str_starts_with($line, '*')) {
                $codeLines++;
            }
        }

        return $codeLines;
    }

    private function calculateCyclomaticComplexity(string $content): int
    {
        $complexity = 1; // Base complexity

        $patterns = [
            'if\s*\(',
            'elseif\s*\(',
            'while\s*\(',
            'for\s*\(',
            'foreach\s*\(',
            'switch\s*\(',
            'case\s+',
            'catch\s*\(',
            '&&',
            '\|\|',
            '\?',
            'throw\s+',
        ];

        foreach ($patterns as $pattern) {
            $matches = preg_match_all('/'.$pattern.'/', $content);
            $complexity += $matches;
        }

        return $complexity;
    }

    private function extractDependencies(string $content): array
    {
        $dependencies = [];

        // Extract use statements
        preg_match_all('/use\s+([^;]+);/', $content, $matches);
        foreach ($matches[1] as $use) {
            $dependencies[] = trim($use);
        }

        // Extract constructor parameters
        preg_match_all('/public\s+function\s+__construct\s*\(([^)]*)\)/', $content, $matches);
        if (! empty($matches[1])) {
            $params = explode(',', $matches[1][0]);
            foreach ($params as $param) {
                if (preg_match('/(\w+)\s+\$/', trim($param), $paramMatches)) {
                    $dependencies[] = $paramMatches[1];
                }
            }
        }

        return array_unique($dependencies);
    }

    private function detectIOOperations(string $content): array
    {
        $ioOperations = [];

        $patterns = [
            'database' => ['DB::', 'Model::', 'where(', 'find(', 'create(', 'update(', 'delete('],
            'http' => ['Http::', 'curl_', 'file_get_contents(', 'fopen(', 'fwrite('],
            'cache' => ['Cache::', 'Redis::', 'cache('],
            'queue' => ['Queue::', 'dispatch(', 'job('],
            'file' => ['Storage::', 'File::', 'file_', 'fopen(', 'fwrite(', 'fread('],
            'mail' => ['Mail::', 'mail(', 'send('],
            'log' => ['Log::', 'logger(', 'error_log('],
        ];

        foreach ($patterns as $category => $operations) {
            foreach ($operations as $operation) {
                if (str_contains($content, $operation)) {
                    $ioOperations[] = $category;

                    break;
                }
            }
        }

        return array_unique($ioOperations);
    }

    private function isBusinessCritical(ReflectionClass $reflection, string $content): bool
    {
        $criticalPatterns = [
            'payment',
            'invoice',
            'order',
            'transaction',
            'auth',
            'user',
            'security',
            'permission',
            'role',
            'money',
            'price',
            'cost',
            'revenue',
        ];

        $className = strtolower($reflection->getName());
        $namespace = strtolower($reflection->getNamespaceName());
        $content = strtolower($content);

        foreach ($criticalPatterns as $pattern) {
            if (str_contains($className, $pattern) ||
                str_contains($namespace, $pattern) ||
                str_contains($content, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function getMethodVisibility(ReflectionMethod $method): string
    {
        if ($method->isPublic()) {
            return 'public';
        }
        if ($method->isProtected()) {
            return 'protected';
        }
        if ($method->isPrivate()) {
            return 'private';
        }

        return 'unknown';
    }

    private function countMethodLines(ReflectionMethod $method, string $content): int
    {
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();

        return $endLine - $startLine + 1;
    }

    private function calculateMethodComplexity(ReflectionMethod $method, string $content): int
    {
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();
        $lines = explode("\n", $content);
        $methodContent = implode("\n", array_slice($lines, $startLine - 1, $endLine - $startLine + 1));

        return $this->calculateCyclomaticComplexity($methodContent);
    }

    private function hasSideEffects(ReflectionMethod $method, string $content): bool
    {
        $sideEffectPatterns = [
            'create(',
            'update(',
            'delete(',
            'save(',
            'store(',
            'dispatch(',
            'queue(',
            'mail(',
            'log(',
            'cache(',
            'file_',
            'fopen(',
            'fwrite(',
        ];

        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();
        $lines = explode("\n", $content);
        $methodContent = implode("\n", array_slice($lines, $startLine - 1, $endLine - $startLine + 1));

        foreach ($sideEffectPatterns as $pattern) {
            if (str_contains($methodContent, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function throwsExceptions(ReflectionMethod $method, string $content): bool
    {
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();
        $lines = explode("\n", $content);
        $methodContent = implode("\n", array_slice($lines, $startLine - 1, $endLine - $startLine + 1));

        return str_contains($methodContent, 'throw ') ||
               str_contains($methodContent, 'throws ') ||
               $method->getDocComment() && str_contains($method->getDocComment(), '@throws');
    }

    private function assessRisk(): void
    {
        foreach ($this->inventory as $class) {
            $riskScore = 0;
            $riskFactors = [];

            // Business criticality (40% weight)
            if ($class['business_critical']) {
                $riskScore += 40;
                $riskFactors[] = 'Business critical';
            }

            // Complexity (25% weight)
            if ($class['cyclomatic_complexity'] > 10) {
                $riskScore += 25;
                $riskFactors[] = 'High complexity';
            } elseif ($class['cyclomatic_complexity'] > 5) {
                $riskScore += 15;
                $riskFactors[] = 'Medium complexity';
            }

            // I/O operations (20% weight)
            $ioCount = count($class['io_operations']);
            if ($ioCount > 3) {
                $riskScore += 20;
                $riskFactors[] = 'Multiple I/O operations';
            } elseif ($ioCount > 1) {
                $riskScore += 10;
                $riskFactors[] = 'Some I/O operations';
            }

            // Lines of code (10% weight)
            if ($class['lines_of_code'] > 200) {
                $riskScore += 10;
                $riskFactors[] = 'Large class';
            } elseif ($class['lines_of_code'] > 100) {
                $riskScore += 5;
                $riskFactors[] = 'Medium class';
            }

            // Method complexity (5% weight)
            $complexMethods = array_filter($class['methods'], fn ($m) => $m['cyclomatic_complexity'] > 5);
            if (count($complexMethods) > 2) {
                $riskScore += 5;
                $riskFactors[] = 'Multiple complex methods';
            }

            $this->riskAssessment[] = [
                'class' => $class['class'],
                'file' => $class['file'],
                'type' => $class['type'],
                'risk_score' => $riskScore,
                'risk_level' => $this->getRiskLevel($riskScore),
                'risk_factors' => $riskFactors,
                'priority' => $this->getPriority($riskScore),
            ];
        }

        // Sort by risk score (highest first)
        usort($this->riskAssessment, fn ($a, $b) => $b['risk_score'] <=> $a['risk_score']);
    }

    private function getRiskLevel(int $score): string
    {
        if ($score >= 80) {
            return 'CRITICAL';
        }
        if ($score >= 60) {
            return 'HIGH';
        }
        if ($score >= 40) {
            return 'MEDIUM';
        }
        if ($score >= 20) {
            return 'LOW';
        }

        return 'MINIMAL';
    }

    private function getPriority(int $score): int
    {
        if ($score >= 80) {
            return 1;
        }
        if ($score >= 60) {
            return 2;
        }
        if ($score >= 40) {
            return 3;
        }
        if ($score >= 20) {
            return 4;
        }

        return 5;
    }

    private function generateSummary(): array
    {
        $totalClasses = count($this->inventory);
        $totalMethods = array_sum(array_map(fn ($c) => count($c['methods']), $this->inventory));

        $typeDistribution = [];
        foreach ($this->inventory as $class) {
            $typeDistribution[$class['type']] = ($typeDistribution[$class['type']] ?? 0) + 1;
        }

        $riskDistribution = [];
        foreach ($this->riskAssessment as $assessment) {
            $riskDistribution[$assessment['risk_level']] = ($riskDistribution[$assessment['risk_level']] ?? 0) + 1;
        }

        return [
            'total_classes' => $totalClasses,
            'total_methods' => $totalMethods,
            'type_distribution' => $typeDistribution,
            'risk_distribution' => $riskDistribution,
            'high_priority_classes' => array_filter($this->riskAssessment, fn ($a) => $a['priority'] <= 2),
        ];
    }
}

// Run the inventory
$inventory = new SystemInventory(__DIR__.'/..');
$result = $inventory->generateInventory();

// Output results
echo "=== SYSTEM INVENTORY REPORT ===\n\n";

echo "SUMMARY:\n";
echo 'Total Classes: '.$result['summary']['total_classes']."\n";
echo 'Total Methods: '.$result['summary']['total_methods']."\n\n";

echo "TYPE DISTRIBUTION:\n";
foreach ($result['summary']['type_distribution'] as $type => $count) {
    echo "  $type: $count\n";
}
echo "\n";

echo "RISK DISTRIBUTION:\n";
foreach ($result['summary']['risk_distribution'] as $level => $count) {
    echo "  $level: $count\n";
}
echo "\n";

echo "HIGH PRIORITY CLASSES (Priority 1-2):\n";
foreach ($result['summary']['high_priority_classes'] as $class) {
    echo "  {$class['class']} ({$class['type']}) - {$class['risk_level']} - {$class['file']}\n";
    echo '    Risk Factors: '.implode(', ', $class['risk_factors'])."\n";
}
echo "\n";

// Save detailed report
file_put_contents(
    __DIR__.'/../storage/app/inventory.json',
    json_encode($result, JSON_PRETTY_PRINT)
);

echo "Detailed report saved to: storage/app/inventory.json\n";
