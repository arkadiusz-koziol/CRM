<?php

declare(strict_types=1);

namespace Tests\Unit\Dto;

use App\Dto\CreateUserDto;
use PHPUnit\Framework\TestCase;

class CreateUserDtoTest extends TestCase
{
    public function testCreatesDtoWithAllRequiredFields(): void
    {
        $dto = new CreateUserDto(
            name: 'John',
            surname: 'Doe',
            email: 'john.doe@example.com',
            phone: '+48123456789',
            password: 'password123',
            city: 'Warsaw',
            vovoidship: 'Mazowieckie'
        );

        $this->assertEquals('John', $dto->getName());
        $this->assertEquals('Doe', $dto->getSurname());
        $this->assertEquals('john.doe@example.com', $dto->getEmail());
        $this->assertEquals('+48123456789', $dto->getPhone());
        $this->assertEquals('password123', $dto->getPassword());
        $this->assertEquals('Warsaw', $dto->getCity());
        $this->assertEquals('Mazowieckie', $dto->getVovoidship());
    }

    public function testCreatesDtoWithNullOptionalFields(): void
    {
        $dto = new CreateUserDto(
            name: 'Jane',
            surname: 'Smith',
            email: 'jane.smith@example.com',
            phone: null,
            password: 'password456',
            city: null,
            vovoidship: null
        );

        $this->assertEquals('Jane', $dto->getName());
        $this->assertEquals('Smith', $dto->getSurname());
        $this->assertEquals('jane.smith@example.com', $dto->getEmail());
        $this->assertNull($dto->getPhone());
        $this->assertEquals('password456', $dto->getPassword());
        $this->assertNull($dto->getCity());
        $this->assertNull($dto->getVovoidship());
    }

    public function testHandlesEmptyStrings(): void
    {
        $dto = new CreateUserDto(
            name: '',
            surname: '',
            email: '',
            phone: '',
            password: '',
            city: '',
            vovoidship: ''
        );

        $this->assertEquals('', $dto->getName());
        $this->assertEquals('', $dto->getSurname());
        $this->assertEquals('', $dto->getEmail());
        $this->assertEquals('', $dto->getPhone());
        $this->assertEquals('', $dto->getPassword());
        $this->assertEquals('', $dto->getCity());
        $this->assertEquals('', $dto->getVovoidship());
    }

    public function testHandlesSpecialCharactersInNames(): void
    {
        $dto = new CreateUserDto(
            name: 'José María',
            surname: 'García-López',
            email: 'jose.garcia@example.com',
            phone: '+48123456789',
            password: 'password123',
            city: 'Madrid',
            vovoidship: 'Comunidad de Madrid'
        );

        $this->assertEquals('José María', $dto->getName());
        $this->assertEquals('García-López', $dto->getSurname());
        $this->assertEquals('jose.garcia@example.com', $dto->getEmail());
        $this->assertEquals('Madrid', $dto->getCity());
        $this->assertEquals('Comunidad de Madrid', $dto->getVovoidship());
    }

    public function testHandlesUnicodeCharacters(): void
    {
        $dto = new CreateUserDto(
            name: '张三',
            surname: '李四',
            email: 'zhangsan@example.com',
            phone: '+86123456789',
            password: 'password123',
            city: '北京',
            vovoidship: '北京市'
        );

        $this->assertEquals('张三', $dto->getName());
        $this->assertEquals('李四', $dto->getSurname());
        $this->assertEquals('zhangsan@example.com', $dto->getEmail());
        $this->assertEquals('+86123456789', $dto->getPhone());
        $this->assertEquals('北京', $dto->getCity());
        $this->assertEquals('北京市', $dto->getVovoidship());
    }

    public function testHandlesEmojiCharacters(): void
    {
        $dto = new CreateUserDto(
            name: 'John 😊',
            surname: 'Doe 🎉',
            email: 'john@example.com',
            phone: '+48123456789',
            password: 'password123',
            city: 'Warsaw 🏙️',
            vovoidship: 'Mazowieckie 🗺️'
        );

        $this->assertEquals('John 😊', $dto->getName());
        $this->assertEquals('Doe 🎉', $dto->getSurname());
        $this->assertEquals('john@example.com', $dto->getEmail());
        $this->assertEquals('Warsaw 🏙️', $dto->getCity());
        $this->assertEquals('Mazowieckie 🗺️', $dto->getVovoidship());
    }

    public function testHandlesVeryLongStrings(): void
    {
        $longString = str_repeat('a', 1000);
        
        $dto = new CreateUserDto(
            name: $longString,
            surname: $longString,
            email: $longString . '@example.com',
            phone: $longString,
            password: $longString,
            city: $longString,
            vovoidship: $longString
        );

        $this->assertEquals($longString, $dto->getName());
        $this->assertEquals($longString, $dto->getSurname());
        $this->assertEquals($longString . '@example.com', $dto->getEmail());
        $this->assertEquals($longString, $dto->getPhone());
        $this->assertEquals($longString, $dto->getPassword());
        $this->assertEquals($longString, $dto->getCity());
        $this->assertEquals($longString, $dto->getVovoidship());
    }

    public function testHandlesWhitespaceOnlyStrings(): void
    {
        $dto = new CreateUserDto(
            name: '   ',
            surname: "\t\n",
            email: '   ',
            phone: '   ',
            password: '   ',
            city: '   ',
            vovoidship: '   '
        );

        $this->assertEquals('   ', $dto->getName());
        $this->assertEquals("\t\n", $dto->getSurname());
        $this->assertEquals('   ', $dto->getEmail());
        $this->assertEquals('   ', $dto->getPhone());
        $this->assertEquals('   ', $dto->getPassword());
        $this->assertEquals('   ', $dto->getCity());
        $this->assertEquals('   ', $dto->getVovoidship());
    }

    public function testHandlesDifferentEmailFormats(): void
    {
        $emailFormats = [
            'user@example.com',
            'user.name@example.com',
            'user+tag@example.com',
            'user123@example123.com',
            'user@subdomain.example.com',
            'user@example.co.uk',
            'user@example.org',
            'user@example.net',
            'user@example.info',
            'user@example.biz',
        ];

        foreach ($emailFormats as $email) {
            $dto = new CreateUserDto(
                name: 'Test',
                surname: 'User',
                email: $email,
                phone: '+48123456789',
                password: 'password123',
                city: 'Warsaw',
                vovoidship: 'Mazowieckie'
            );

            $this->assertEquals($email, $dto->getEmail());
        }
    }

    public function testHandlesDifferentPhoneFormats(): void
    {
        $phoneFormats = [
            '+48123456789',
            '123456789',
            '+1-234-567-8900',
            '+1 (234) 567-8900',
            '+1.234.567.8900',
            '+1 234 567 8900',
            '123-456-7890',
            '(123) 456-7890',
            '123.456.7890',
            '123 456 7890',
        ];

        foreach ($phoneFormats as $phone) {
            $dto = new CreateUserDto(
                name: 'Test',
                surname: 'User',
                email: 'test@example.com',
                phone: $phone,
                password: 'password123',
                city: 'Warsaw',
                vovoidship: 'Mazowieckie'
            );

            $this->assertEquals($phone, $dto->getPhone());
        }
    }

    public function testHandlesNullPhoneWhenOtherFieldsAreProvided(): void
    {
        $dto = new CreateUserDto(
            name: 'Test',
            surname: 'User',
            email: 'test@example.com',
            phone: null,
            password: 'password123',
            city: 'Warsaw',
            vovoidship: 'Mazowieckie'
        );

        $this->assertEquals('Test', $dto->getName());
        $this->assertEquals('User', $dto->getSurname());
        $this->assertEquals('test@example.com', $dto->getEmail());
        $this->assertNull($dto->getPhone());
        $this->assertEquals('password123', $dto->getPassword());
        $this->assertEquals('Warsaw', $dto->getCity());
        $this->assertEquals('Mazowieckie', $dto->getVovoidship());
    }

    public function testHandlesNullCityAndVovoidshipWhenOtherFieldsAreProvided(): void
    {
        $dto = new CreateUserDto(
            name: 'Test',
            surname: 'User',
            email: 'test@example.com',
            phone: '+48123456789',
            password: 'password123',
            city: null,
            vovoidship: null
        );

        $this->assertEquals('Test', $dto->getName());
        $this->assertEquals('User', $dto->getSurname());
        $this->assertEquals('test@example.com', $dto->getEmail());
        $this->assertEquals('+48123456789', $dto->getPhone());
        $this->assertEquals('password123', $dto->getPassword());
        $this->assertNull($dto->getCity());
        $this->assertNull($dto->getVovoidship());
    }

    public function testMaintainsImmutability(): void
    {
        $dto = new CreateUserDto(
            name: 'John',
            surname: 'Doe',
            email: 'john.doe@example.com',
            phone: '+48123456789',
            password: 'password123',
            city: 'Warsaw',
            vovoidship: 'Mazowieckie'
        );

        $originalName = $dto->getName();
        $originalSurname = $dto->getSurname();
        $originalEmail = $dto->getEmail();
        $originalPhone = $dto->getPhone();
        $originalPassword = $dto->getPassword();
        $originalCity = $dto->getCity();
        $originalVovoidship = $dto->getVovoidship();

        // Create a new DTO with different values
        $newDto = new CreateUserDto(
            name: 'Jane',
            surname: 'Smith',
            email: 'jane.smith@example.com',
            phone: '+48987654321',
            password: 'password456',
            city: 'Krakow',
            vovoidship: 'Malopolskie'
        );

        // Original DTO should remain unchanged
        $this->assertEquals($originalName, $dto->getName());
        $this->assertEquals($originalSurname, $dto->getSurname());
        $this->assertEquals($originalEmail, $dto->getEmail());
        $this->assertEquals($originalPhone, $dto->getPhone());
        $this->assertEquals($originalPassword, $dto->getPassword());
        $this->assertEquals($originalCity, $dto->getCity());
        $this->assertEquals($originalVovoidship, $dto->getVovoidship());

        // New DTO should have different values
        $this->assertEquals('Jane', $newDto->getName());
        $this->assertEquals('Smith', $newDto->getSurname());
        $this->assertEquals('jane.smith@example.com', $newDto->getEmail());
        $this->assertEquals('+48987654321', $newDto->getPhone());
        $this->assertEquals('password456', $newDto->getPassword());
        $this->assertEquals('Krakow', $newDto->getCity());
        $this->assertEquals('Malopolskie', $newDto->getVovoidship());
    }
}