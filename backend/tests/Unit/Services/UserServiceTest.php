<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Dto\CreateUserDto;
use App\Enums\UserRoles;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserRepositoryInterface $userRepository;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepository);
    }

    public function test_create_user(): void
    {
        $dto = new CreateUserDto(
            name: 'John',
            surname: 'Doe',
            email: 'john.doe@example.com',
            phone: '123456789',
            password: 'password',
            city: 'Warsaw',
            vovoidship: 'Mazowieckie'
        );

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('assignRole')->with(UserRoles::USER->value);

        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($user);

        $createdUser = $this->userService->createUser($dto);

        $this->assertEquals($user, $createdUser);
    }

    public function test_update_user(): void
    {
        $user = $this->createUser();
        $data = ['name' => 'Updated Name'];

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, $data)
            ->willReturn(true);

        $result = $this->userService->updateUser($user, $data);

        $this->assertTrue($result);
    }

    public function test_get_user_by_id(): void
    {
        $userId = 1;
        $user = $this->createMock(User::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $foundUser = $this->userService->getUserById($userId);

        $this->assertEquals($user, $foundUser);
    }

    public function test_delete_user(): void
    {
        $user = $this->createUser();

        $this->userRepository
            ->expects($this->once())
            ->method('delete')
            ->with($user)
            ->willReturn(true);

        $result = $this->userService->deleteUser($user);

        $this->assertTrue($result);
    }

    public function test_change_password(): void
    {
        $user = $this->createUser();
        $newPassword = 'new_password';

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, ['password' => $newPassword])
            ->willReturn(true);

        $result = $this->userService->changePassword($user, $newPassword);

        $this->assertTrue($result);
    }

    public function test_get_all_users(): void
    {
        $users = Collection::times(3, fn () => $this->createUser())->all();

        $this->userRepository
            ->expects($this->once())
            ->method('list')
            ->willReturn($users);

        $allUsers = $this->userService->getAllUsers();

        $this->assertEquals($users, $allUsers);
    }

    public function test_create_user_with_special_characters(): void
    {
        $dto = new CreateUserDto(
            name: 'José María',
            surname: 'García-López',
            email: 'jose.garcia@example.com',
            phone: '+48123456789',
            password: 'password',
            city: 'Madrid',
            vovoidship: 'Comunidad de Madrid'
        );

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('assignRole')->with(UserRoles::USER->value);

        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($user);

        $createdUser = $this->userService->createUser($dto);

        $this->assertEquals($user, $createdUser);
    }

    public function test_create_user_with_unicode_characters(): void
    {
        $dto = new CreateUserDto(
            name: '张三',
            surname: '李四',
            email: 'zhangsan@example.com',
            phone: '+86123456789',
            password: 'password',
            city: '北京',
            vovoidship: '北京市'
        );

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('assignRole')->with(UserRoles::USER->value);

        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($user);

        $createdUser = $this->userService->createUser($dto);

        $this->assertEquals($user, $createdUser);
    }

    public function test_create_user_with_emoji_characters(): void
    {
        $dto = new CreateUserDto(
            name: 'John 😊',
            surname: 'Doe 🎉',
            email: 'john@example.com',
            phone: '+48123456789',
            password: 'password',
            city: 'Warsaw 🏙️',
            vovoidship: 'Mazowieckie 🗺️'
        );

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('assignRole')->with(UserRoles::USER->value);

        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($user);

        $createdUser = $this->userService->createUser($dto);

        $this->assertEquals($user, $createdUser);
    }

    public function test_create_user_with_very_long_strings(): void
    {
        $longString = str_repeat('a', 1000);

        $dto = new CreateUserDto(
            name: $longString,
            surname: $longString,
            email: $longString.'@example.com',
            phone: $longString,
            password: $longString,
            city: $longString,
            vovoidship: $longString
        );

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('assignRole')->with(UserRoles::USER->value);

        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($user);

        $createdUser = $this->userService->createUser($dto);

        $this->assertEquals($user, $createdUser);
    }

    public function test_create_user_with_empty_strings(): void
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

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('assignRole')->with(UserRoles::USER->value);

        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($user);

        $createdUser = $this->userService->createUser($dto);

        $this->assertEquals($user, $createdUser);
    }

    public function test_create_user_with_whitespace_only_strings(): void
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

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('assignRole')->with(UserRoles::USER->value);

        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($user);

        $createdUser = $this->userService->createUser($dto);

        $this->assertEquals($user, $createdUser);
    }

    public function test_update_user_with_special_characters(): void
    {
        $user = $this->createUser();
        $data = [
            'name' => 'José María',
            'surname' => 'García-López',
            'city' => 'Madrid',
            'vovoidship' => 'Comunidad de Madrid',
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, $data)
            ->willReturn(true);

        $result = $this->userService->updateUser($user, $data);

        $this->assertTrue($result);
    }

    public function test_update_user_with_unicode_characters(): void
    {
        $user = $this->createUser();
        $data = [
            'name' => '张三',
            'surname' => '李四',
            'city' => '北京',
            'vovoidship' => '北京市',
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, $data)
            ->willReturn(true);

        $result = $this->userService->updateUser($user, $data);

        $this->assertTrue($result);
    }

    public function test_update_user_with_emoji_characters(): void
    {
        $user = $this->createUser();
        $data = [
            'name' => 'John 😊',
            'surname' => 'Doe 🎉',
            'city' => 'Warsaw 🏙️',
            'vovoidship' => 'Mazowieckie 🗺️',
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, $data)
            ->willReturn(true);

        $result = $this->userService->updateUser($user, $data);

        $this->assertTrue($result);
    }

    public function test_update_user_with_very_long_strings(): void
    {
        $user = $this->createUser();
        $longString = str_repeat('a', 1000);
        $data = [
            'name' => $longString,
            'surname' => $longString,
            'city' => $longString,
            'vovoidship' => $longString,
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, $data)
            ->willReturn(true);

        $result = $this->userService->updateUser($user, $data);

        $this->assertTrue($result);
    }

    public function test_update_user_with_empty_strings(): void
    {
        $user = $this->createUser();
        $data = [
            'name' => '',
            'surname' => '',
            'city' => '',
            'vovoidship' => '',
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, $data)
            ->willReturn(true);

        $result = $this->userService->updateUser($user, $data);

        $this->assertTrue($result);
    }

    public function test_update_user_with_whitespace_only_strings(): void
    {
        $user = $this->createUser();
        $data = [
            'name' => '   ',
            'surname' => "\t\n",
            'city' => '   ',
            'vovoidship' => '   ',
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, $data)
            ->willReturn(true);

        $result = $this->userService->updateUser($user, $data);

        $this->assertTrue($result);
    }

    public function test_change_password_with_special_characters(): void
    {
        $user = $this->createUser();
        $newPassword = 'p@ssw0rd!@#$%^&*()_+-=[]{}|;:,.<>?';

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, ['password' => $newPassword])
            ->willReturn(true);

        $result = $this->userService->changePassword($user, $newPassword);

        $this->assertTrue($result);
    }

    public function test_change_password_with_unicode_characters(): void
    {
        $user = $this->createUser();
        $newPassword = '密码123';

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, ['password' => $newPassword])
            ->willReturn(true);

        $result = $this->userService->changePassword($user, $newPassword);

        $this->assertTrue($result);
    }

    public function test_change_password_with_emoji_characters(): void
    {
        $user = $this->createUser();
        $newPassword = 'password😊🎉';

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, ['password' => $newPassword])
            ->willReturn(true);

        $result = $this->userService->changePassword($user, $newPassword);

        $this->assertTrue($result);
    }

    public function test_change_password_with_very_long_string(): void
    {
        $user = $this->createUser();
        $newPassword = str_repeat('a', 1000);

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, ['password' => $newPassword])
            ->willReturn(true);

        $result = $this->userService->changePassword($user, $newPassword);

        $this->assertTrue($result);
    }

    public function test_change_password_with_empty_string(): void
    {
        $user = $this->createUser();
        $newPassword = '';

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, ['password' => $newPassword])
            ->willReturn(true);

        $result = $this->userService->changePassword($user, $newPassword);

        $this->assertTrue($result);
    }

    public function test_change_password_with_whitespace_only_string(): void
    {
        $user = $this->createUser();
        $newPassword = '   ';

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, ['password' => $newPassword])
            ->willReturn(true);

        $result = $this->userService->changePassword($user, $newPassword);

        $this->assertTrue($result);
    }

    public function test_get_all_users_with_empty_result(): void
    {
        $users = [];

        $this->userRepository
            ->expects($this->once())
            ->method('list')
            ->willReturn($users);

        $allUsers = $this->userService->getAllUsers();

        $this->assertEquals($users, $allUsers);
        $this->assertIsArray($allUsers);
        $this->assertCount(0, $allUsers);
    }

    public function test_get_all_users_with_large_result(): void
    {
        $users = Collection::times(1000, fn () => $this->createUser())->all();

        $this->userRepository
            ->expects($this->once())
            ->method('list')
            ->willReturn($users);

        $allUsers = $this->userService->getAllUsers();

        $this->assertEquals($users, $allUsers);
        $this->assertIsArray($allUsers);
        $this->assertCount(1000, $allUsers);
    }

    public function test_get_user_by_id_with_non_existent_id(): void
    {
        $nonExistentId = 999999;

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($nonExistentId)
            ->willReturn(null);

        $foundUser = $this->userService->getUserById($nonExistentId);

        $this->assertNull($foundUser);
    }

    public function test_delete_user_with_non_existent_user(): void
    {
        $user = $this->createUser();

        $this->userRepository
            ->expects($this->once())
            ->method('delete')
            ->with($user)
            ->willReturn(false);

        $result = $this->userService->deleteUser($user);

        $this->assertFalse($result);
    }

    public function test_update_user_with_non_existent_user(): void
    {
        $user = $this->createUser();
        $data = ['name' => 'Updated Name'];

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, $data)
            ->willReturn(false);

        $result = $this->userService->updateUser($user, $data);

        $this->assertFalse($result);
    }

    public function test_change_password_with_non_existent_user(): void
    {
        $user = $this->createUser();
        $newPassword = 'new_password';

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->with($user, ['password' => $newPassword])
            ->willReturn(false);

        $result = $this->userService->changePassword($user, $newPassword);

        $this->assertFalse($result);
    }

    private function createUser(): User
    {
        $user = $this->createMock(User::class);
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->phone = '+48123456789';
        $user->city = 'Warsaw';
        $user->vovoidship = 'Mazowieckie';
        $user->created_at = '2024-01-01 12:00:00';
        $user->updated_at = '2024-01-01 12:00:00';

        return $user;
    }
}
