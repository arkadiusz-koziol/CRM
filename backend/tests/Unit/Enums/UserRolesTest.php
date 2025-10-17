<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\UserRoles;
use PHPUnit\Framework\TestCase;

class UserRolesTest extends TestCase
{
    public function test_enum_values(): void
    {
        $this->assertEquals('admin', UserRoles::ADMIN->value);
        $this->assertEquals('user', UserRoles::USER->value);
        $this->assertEquals('technician', UserRoles::TECHNICIAN->value);
        $this->assertEquals('driver', UserRoles::DRIVER->value);
        $this->assertEquals('object_manager', UserRoles::OBJECT_MANAGER->value);
    }

    public function test_allowed_for_web(): void
    {
        $expected = [UserRoles::ADMIN->value];
        $this->assertEquals($expected, UserRoles::allowedForWeb());
    }

    public function test_allowed_for_api(): void
    {
        $expected = [
            UserRoles::ADMIN->value,
            UserRoles::USER->value,
            UserRoles::TECHNICIAN->value,
            UserRoles::DRIVER->value,
        ];
        $this->assertEquals($expected, UserRoles::allowedForApi());
    }

    public function test_enum_cases(): void
    {
        $cases = UserRoles::cases();
        $this->assertCount(5, $cases);

        $expectedValues = [
            'admin',
            'user',
            'technician',
            'driver',
            'object_manager',
        ];

        $actualValues = array_map(fn ($case) => $case->value, $cases);
        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_enum_from_value(): void
    {
        $this->assertEquals(UserRoles::ADMIN, UserRoles::from('admin'));
        $this->assertEquals(UserRoles::USER, UserRoles::from('user'));
        $this->assertEquals(UserRoles::TECHNICIAN, UserRoles::from('technician'));
        $this->assertEquals(UserRoles::DRIVER, UserRoles::from('driver'));
        $this->assertEquals(UserRoles::OBJECT_MANAGER, UserRoles::from('object_manager'));
    }

    public function test_enum_try_from_value(): void
    {
        $this->assertEquals(UserRoles::ADMIN, UserRoles::tryFrom('admin'));
        $this->assertEquals(UserRoles::USER, UserRoles::tryFrom('user'));
        $this->assertEquals(UserRoles::TECHNICIAN, UserRoles::tryFrom('technician'));
        $this->assertEquals(UserRoles::DRIVER, UserRoles::tryFrom('driver'));
        $this->assertEquals(UserRoles::OBJECT_MANAGER, UserRoles::tryFrom('object_manager'));
        $this->assertNull(UserRoles::tryFrom('invalid_role'));
        $this->assertNull(UserRoles::tryFrom(''));
    }

    public function test_enum_from_value_throws_exception_for_invalid_value(): void
    {
        $this->expectException(\ValueError::class);
        UserRoles::from('invalid_role');
    }

    public function test_enum_from_value_throws_exception_for_empty_string(): void
    {
        $this->expectException(\ValueError::class);
        UserRoles::from('');
    }

    public function test_enum_from_value_throws_exception_for_null(): void
    {
        $this->expectException(\TypeError::class);
        UserRoles::from(null);
    }

    public function test_enum_name(): void
    {
        $this->assertEquals('ADMIN', UserRoles::ADMIN->name);
        $this->assertEquals('USER', UserRoles::USER->name);
        $this->assertEquals('TECHNICIAN', UserRoles::TECHNICIAN->name);
        $this->assertEquals('DRIVER', UserRoles::DRIVER->name);
        $this->assertEquals('OBJECT_MANAGER', UserRoles::OBJECT_MANAGER->name);
    }

    public function test_enum_to_string(): void
    {
        $this->assertEquals('admin', UserRoles::ADMIN->value);
        $this->assertEquals('user', UserRoles::USER->value);
        $this->assertEquals('technician', UserRoles::TECHNICIAN->value);
        $this->assertEquals('driver', UserRoles::DRIVER->value);
        $this->assertEquals('object_manager', UserRoles::OBJECT_MANAGER->value);
    }

    public function test_enum_equality(): void
    {
        $this->assertTrue(UserRoles::ADMIN === UserRoles::ADMIN);
        $this->assertTrue(UserRoles::USER === UserRoles::USER);
        $this->assertTrue(UserRoles::TECHNICIAN === UserRoles::TECHNICIAN);
        $this->assertTrue(UserRoles::DRIVER === UserRoles::DRIVER);
        $this->assertTrue(UserRoles::OBJECT_MANAGER === UserRoles::OBJECT_MANAGER);

        $this->assertFalse(UserRoles::ADMIN === UserRoles::USER);
        $this->assertFalse(UserRoles::USER === UserRoles::TECHNICIAN);
        $this->assertFalse(UserRoles::TECHNICIAN === UserRoles::DRIVER);
        $this->assertFalse(UserRoles::DRIVER === UserRoles::OBJECT_MANAGER);
        $this->assertFalse(UserRoles::OBJECT_MANAGER === UserRoles::ADMIN);
    }

    public function test_enum_inequality(): void
    {
        $this->assertTrue(UserRoles::ADMIN !== UserRoles::USER);
        $this->assertTrue(UserRoles::USER !== UserRoles::TECHNICIAN);
        $this->assertTrue(UserRoles::TECHNICIAN !== UserRoles::DRIVER);
        $this->assertTrue(UserRoles::DRIVER !== UserRoles::OBJECT_MANAGER);
        $this->assertTrue(UserRoles::OBJECT_MANAGER !== UserRoles::ADMIN);

        $this->assertFalse(UserRoles::ADMIN !== UserRoles::ADMIN);
        $this->assertFalse(UserRoles::USER !== UserRoles::USER);
        $this->assertFalse(UserRoles::TECHNICIAN !== UserRoles::TECHNICIAN);
        $this->assertFalse(UserRoles::DRIVER !== UserRoles::DRIVER);
        $this->assertFalse(UserRoles::OBJECT_MANAGER !== UserRoles::OBJECT_MANAGER);
    }

    public function test_enum_in_array(): void
    {
        $webRoles = UserRoles::allowedForWeb();
        $apiRoles = UserRoles::allowedForApi();

        $this->assertContains(UserRoles::ADMIN->value, $webRoles);
        $this->assertNotContains(UserRoles::USER->value, $webRoles);
        $this->assertNotContains(UserRoles::TECHNICIAN->value, $webRoles);
        $this->assertNotContains(UserRoles::DRIVER->value, $webRoles);
        $this->assertNotContains(UserRoles::OBJECT_MANAGER->value, $webRoles);

        $this->assertContains(UserRoles::ADMIN->value, $apiRoles);
        $this->assertContains(UserRoles::USER->value, $apiRoles);
        $this->assertContains(UserRoles::TECHNICIAN->value, $apiRoles);
        $this->assertContains(UserRoles::DRIVER->value, $apiRoles);
        $this->assertNotContains(UserRoles::OBJECT_MANAGER->value, $apiRoles);
    }

    public function test_enum_array_keys(): void
    {
        $webRoles = UserRoles::allowedForWeb();
        $apiRoles = UserRoles::allowedForApi();

        $this->assertArrayHasKey(0, $webRoles);
        $this->assertArrayHasKey(0, $apiRoles);
        $this->assertArrayHasKey(1, $apiRoles);
        $this->assertArrayHasKey(2, $apiRoles);
        $this->assertArrayHasKey(3, $apiRoles);

        $this->assertArrayNotHasKey(1, $webRoles);
        $this->assertArrayNotHasKey(4, $apiRoles);
    }

    public function test_enum_array_values(): void
    {
        $webRoles = UserRoles::allowedForWeb();
        $apiRoles = UserRoles::allowedForApi();

        $this->assertEquals('admin', $webRoles[0]);

        $this->assertEquals('admin', $apiRoles[0]);
        $this->assertEquals('user', $apiRoles[1]);
        $this->assertEquals('technician', $apiRoles[2]);
        $this->assertEquals('driver', $apiRoles[3]);
    }

    public function test_enum_array_count(): void
    {
        $webRoles = UserRoles::allowedForWeb();
        $apiRoles = UserRoles::allowedForApi();

        $this->assertCount(1, $webRoles);
        $this->assertCount(4, $apiRoles);
    }

    public function test_enum_array_types(): void
    {
        $webRoles = UserRoles::allowedForWeb();
        $apiRoles = UserRoles::allowedForApi();

        $this->assertIsArray($webRoles);
        $this->assertIsArray($apiRoles);

        foreach ($webRoles as $role) {
            $this->assertIsString($role);
        }

        foreach ($apiRoles as $role) {
            $this->assertIsString($role);
        }
    }

    public function test_enum_array_uniqueness(): void
    {
        $webRoles = UserRoles::allowedForWeb();
        $apiRoles = UserRoles::allowedForApi();

        $this->assertEquals($webRoles, array_unique($webRoles));
        $this->assertEquals($apiRoles, array_unique($apiRoles));
    }

    public function test_enum_array_order(): void
    {
        $webRoles = UserRoles::allowedForWeb();
        $apiRoles = UserRoles::allowedForApi();

        $this->assertEquals(['admin'], $webRoles);
        $this->assertEquals(['admin', 'user', 'technician', 'driver'], $apiRoles);
    }
}
