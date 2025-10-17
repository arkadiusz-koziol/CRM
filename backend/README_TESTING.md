# Testing Documentation

This document provides comprehensive information about the testing setup, strategies, and quality gates implemented for the Skytech backend project.

## Overview

The testing infrastructure is designed to achieve:
- **Line Coverage**: ≥98% globally, 100% for critical modules
- **Branch Coverage**: ≥95% globally
- **Mutation Score**: ≥80% (MSI) with Infection PHP
- **Zero Flaky Tests**: All tests must be deterministic and stable
- **Fast Execution**: Complete test suite runs in <8 minutes

## Architecture

### Test Pyramid Structure

```
E2E Tests (5%)
├── Critical business flows
├── End-to-end user journeys
└── Integration with external systems

Feature Tests (15%)
├── API endpoints
├── Authentication & authorization
├── Business logic validation
└── JSON:API contract compliance

Integration Tests (25%)
├── Repository implementations
├── Database operations
├── Event handling
├── Queue processing
└── Cache operations

Unit Tests (55%)
├── Domain entities
├── Business services
├── DTOs and factories
├── Enums and value objects
└── Utility functions
```

## Test Categories

### 1. Unit Tests (`tests/Unit/`)

**Purpose**: Test individual components in isolation with mocked dependencies.

**Coverage**:
- Domain entities (`Domain/`)
- DTOs (`Dto/`)
- Enums (`Enums/`)
- Services (`Services/`)
- Factories (`Factory/`)

**Key Principles**:
- Mock all I/O boundaries (DB, HTTP, Cache, Queue)
- Test business logic in isolation
- Focus on edge cases and error conditions
- Ensure deterministic behavior

### 2. Integration Tests (`tests/Integration/`)

**Purpose**: Test component interactions with real dependencies.

**Coverage**:
- Repository implementations
- Database operations
- Event listeners
- Queue jobs
- Cache operations

**Key Principles**:
- Use real database (PostgreSQL in tests)
- Test transaction boundaries
- Verify data persistence
- Test error handling

### 3. Feature Tests (`tests/Feature/`)

**Purpose**: Test complete features through API endpoints.

**Coverage**:
- HTTP controllers
- Request validation
- Response formatting
- Authentication & authorization
- Business workflows

**Key Principles**:
- Test through HTTP layer
- Validate JSON:API compliance
- Test authentication flows
- Verify error responses

### 4. Contract Tests (`tests/Contract/`)

**Purpose**: Ensure API contract stability and backward compatibility.

**Coverage**:
- OpenAPI specification compliance
- JSON:API format validation
- Schema evolution
- Breaking change detection

### 5. E2E Tests (`tests/E2E/`)

**Purpose**: Test complete user journeys and critical business flows.

**Coverage**:
- User registration and authentication
- Task management workflows
- Activity logging
- Error handling scenarios

## Test Configuration

### Pest Configuration (`pest.php`)

```php
<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(Tests\TestCase::class, RefreshDatabase::class, WithFaker::class)->in('Feature');
uses(Tests\TestCase::class)->in('Unit');

// Global test configuration
beforeEach(function () {
    // Set deterministic time for tests
    \Carbon\Carbon::setTestNow('2024-01-01 12:00:00');
    
    // Set deterministic UUID generation
    \Ramsey\Uuid\Uuid::setFactory(new \Ramsey\Uuid\UuidFactory(
        new \Ramsey\Uuid\Generator\CombGenerator(
            new \Ramsey\Uuid\Random\RandomGeneratorFactory()
        )
    ));
});
```

### PHPUnit Configuration (`phpunit.xml`)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         processIsolation="false"
         stopOnFailure="false"
         cacheDirectory=".phpunit.cache"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
        <testsuite name="Contract">
            <directory>tests/Contract</directory>
        </testsuite>
        <testsuite name="E2E">
            <directory>tests/E2E</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
        <exclude>
            <directory>app/Console</directory>
            <directory>app/Exceptions</directory>
            <directory>app/Http/Kernel.php</directory>
            <directory>app/Http/Middleware</directory>
            <directory>app/Providers</directory>
            <file>app/Http/Controllers/Controller.php</file>
        </exclude>
    </source>
    <coverage>
        <report>
            <html outputDirectory="storage/app/coverage"/>
            <clover outputFile="storage/app/coverage.xml"/>
            <text outputFile="storage/app/coverage.txt"/>
        </report>
    </coverage>
</phpunit>
```

### Infection Configuration (`infection.json5`)

```json
{
  "$schema": "https://raw.githubusercontent.com/infection/infection/main/resources/schema.json",
  "source": {
    "directories": ["app"],
    "excludes": [
      "app/Console",
      "app/Exceptions",
      "app/Http/Kernel.php",
      "app/Http/Middleware",
      "app/Providers",
      "app/Http/Controllers/Controller.php"
    ]
  },
  "logs": {
    "text": "storage/app/infection.log",
    "summary": "storage/app/infection-summary.log",
    "debug": "storage/app/infection-debug.log"
  },
  "testFramework": "phpunit",
  "bootstrap": "vendor/autoload.php",
  "testFrameworkOptions": "--configuration=phpunit.xml",
  "minMsi": 80,
  "minCoveredMsi": 85,
  "threads": 4,
  "ignoreMsiWithMoreMutants": true,
  "showMutations": true,
  "logVerbosity": "all",
  "noProgress": false,
  "formatters": {
    "progress": true,
    "dot": false,
    "html": true,
    "json": true
  },
  "formatterOptions": {
    "html": {
      "outputDirectory": "storage/app/infection"
    }
  }
}
```

## Running Tests

### Quick Commands

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite=Unit
./vendor/bin/phpunit --testsuite=Integration
./vendor/bin/phpunit --testsuite=Feature
./vendor/bin/phpunit --testsuite=Contract
./vendor/bin/phpunit --testsuite=E2E

# Run with coverage
./vendor/bin/phpunit --coverage-html=storage/app/coverage --coverage-clover=storage/app/coverage.xml

# Run with specific coverage threshold
./vendor/bin/phpunit --coverage-html=storage/app/coverage --coverage-clover=storage/app/coverage.xml --coverage-text=storage/app/coverage.txt

# Run mutation testing
./vendor/bin/infection --configuration=infection.json5

# Run comprehensive test suite
php scripts/test-runner.php
```

### Docker Commands

```bash
# Run tests in Docker container
docker exec -it skytech-backend-dev bash -c "./vendor/bin/phpunit"

# Run with coverage in Docker
docker exec -it skytech-backend-dev bash -c "./vendor/bin/phpunit --coverage-html=storage/app/coverage --coverage-clover=storage/app/coverage.xml"

# Run mutation testing in Docker
docker exec -it skytech-backend-dev bash -c "./vendor/bin/infection --configuration=infection.json5"
```

## Test Data Management

### Factories

```php
// User factory
User::factory()->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);

// Task factory
Task::factory()->create([
    'title' => 'Test Task',
    'status' => TaskStatus::PENDING,
    'priority' => TaskPriority::MEDIUM,
]);
```

### Test Data Helpers

```php
// Create test user
$user = $this->createUser(['name' => 'Test User']);

// Create test task
$task = $this->createTask(['title' => 'Test Task']);

// Create test activity
$activity = $this->createActivity(['action' => 'test_action']);
```

## Quality Gates

### Coverage Requirements

- **Line Coverage**: ≥98% globally, 100% for critical modules
- **Branch Coverage**: ≥95% globally
- **Mutation Score**: ≥80% (MSI)
- **Code Style**: Must pass PHP CS Fixer
- **Static Analysis**: Must pass PHPStan

### CI/CD Integration

```yaml
# GitHub Actions example
- name: Run Tests
  run: |
    composer install --no-interaction
    ./vendor/bin/pest --coverage --min=98
    ./vendor/bin/infection --configuration=infection.json5
    ./vendor/bin/pint --test
    ./vendor/bin/phpstan analyse
```

## Test Patterns

### Unit Test Pattern

```php
<?php

declare(strict_types=1);

use App\Services\UserService;
use App\Dto\CreateUserDto;
use App\Models\User;
use App\Enums\UserRoles;
use App\Interfaces\Repositories\UserRepositoryInterface;
use Mockery\MockInterface;

describe('UserService', function () {
    beforeEach(function () {
        $this->mockRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UserService($this->mockRepository);
    });

    afterEach(function () {
        Mockery::close();
    });

    it('creates a user with all required fields', function () {
        $dto = new CreateUserDto(
            name: 'John',
            surname: 'Doe',
            email: 'john.doe@example.com',
            phone: '+48123456789',
            password: 'password123',
            city: 'Warsaw',
            vovoidship: 'Mazowieckie'
        );

        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('assignRole')->once()->with(UserRoles::USER->value);

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+48123456789',
                'password' => 'password123',
                'city' => 'Warsaw',
                'vovoidship' => 'Mazowieckie',
            ])
            ->andReturn($mockUser);

        $result = $this->service->createUser($dto);

        expect($result)->toBe($mockUser);
    });
});
```

### Integration Test Pattern

```php
<?php

declare(strict_types=1);

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Dto\CreateUserDto;

describe('UserService Integration', function () {
    it('creates a user and persists to database', function () {
        $repository = new UserRepository();
        $service = new UserService($repository);

        $dto = new CreateUserDto(
            name: 'John',
            surname: 'Doe',
            email: 'john.doe@example.com',
            phone: '+48123456789',
            password: 'password123',
            city: 'Warsaw',
            vovoidship: 'Mazowieckie'
        );

        $user = $service->createUser($dto);

        expect($user)->toBeInstanceOf(User::class);
        expect($user->name)->toBe('John');
        expect($user->email)->toBe('john.doe@example.com');
        expect($user->hasRole(UserRoles::USER->value))->toBeTrue();

        $this->assertDatabaseHas('users', [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);
    });
});
```

### Feature Test Pattern

```php
<?php

declare(strict_types=1);

use App\Models\User;
use App\Enums\UserRoles;

describe('User API', function () {
    it('creates a user via API endpoint', function () {
        $userData = [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+48123456789',
            'password' => 'password123',
            'city' => 'Warsaw',
            'vovoidship' => 'Mazowieckie',
        ];

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'surname',
                        'email',
                        'phone',
                        'city',
                        'vovoidship',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);
    });
});
```

## Edge Cases and Test Scenarios

### Input Validation

- Empty strings
- Null values
- Very long strings
- Special characters
- Unicode characters
- Emoji characters
- Whitespace-only strings
- Malformed data

### Business Logic

- Boundary conditions
- Error states
- Exception handling
- Race conditions
- Concurrent operations
- Data consistency

### Performance

- Large datasets
- Memory usage
- Execution time
- Database queries
- Cache operations

## Reporting

### Coverage Reports

- HTML: `storage/app/coverage/index.html`
- XML: `storage/app/coverage.xml`
- Text: `storage/app/coverage.txt`

### Mutation Reports

- HTML: `storage/app/infection/index.html`
- JSON: `storage/app/infection/infection-results.json`
- Log: `storage/app/infection.log`

### Test Reports

- JSON: `storage/app/test-report.json`
- HTML: `storage/app/test-report.html`

## Troubleshooting

### Common Issues

1. **Tests failing due to timezone issues**
   - Solution: Use `Carbon::setTestNow()` in test setup

2. **UUID generation not deterministic**
   - Solution: Set custom UUID factory in test setup

3. **Database state not reset between tests**
   - Solution: Use `RefreshDatabase` trait

4. **Mock objects not working**
   - Solution: Ensure `Mockery::close()` in `afterEach()`

5. **Coverage not accurate**
   - Solution: Check PHPUnit configuration and source paths

### Debug Commands

```bash
# Run tests with verbose output
./vendor/bin/pest --verbose

# Run specific test with debug
./vendor/bin/pest --filter="testName" --verbose

# Check coverage for specific file
./vendor/bin/pest --coverage --coverage-src=app/Services/UserService.php

# Run mutation testing with debug
./vendor/bin/infection --configuration=infection.json5 --log-verbosity=all
```

## Best Practices

### Test Organization

1. **One test per behavior**: Each test should verify one specific behavior
2. **Descriptive names**: Test names should clearly describe what is being tested
3. **Arrange-Act-Assert**: Structure tests with clear setup, execution, and verification
4. **Independent tests**: Tests should not depend on each other
5. **Deterministic**: Tests should produce the same results every time

### Test Data

1. **Use factories**: Create test data using Laravel factories
2. **Minimal data**: Use only the data necessary for the test
3. **Realistic data**: Use data that reflects real-world scenarios
4. **Edge cases**: Test boundary conditions and error states

### Mocking

1. **Mock boundaries**: Only mock external dependencies
2. **Verify interactions**: Assert that mocked methods are called correctly
3. **Avoid over-mocking**: Don't mock everything, use real objects when possible
4. **Clear expectations**: Set up clear expectations for mocked methods

### Coverage

1. **Aim for high coverage**: Target 100% coverage for critical modules
2. **Quality over quantity**: Focus on meaningful tests, not just coverage
3. **Test edge cases**: Ensure all code paths are tested
4. **Regular monitoring**: Monitor coverage trends over time

## Conclusion

This testing infrastructure provides comprehensive coverage and quality assurance for the Skytech backend project. By following the patterns and practices outlined in this document, you can maintain high code quality and confidence in your application's reliability.

For questions or issues, please refer to the test reports or contact the development team.
