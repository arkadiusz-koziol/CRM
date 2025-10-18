<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\Training;
use App\Models\TrainingUser;
use App\Models\User;
use App\Repositories\TrainingUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class TrainingUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TrainingUserRepository $trainingUserRepository;

    private $userRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->trainingUserRepository = new TrainingUserRepository(new TrainingUser, $this->userRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_assign_user_to_training_creates_record(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $result = $this->trainingUserRepository->assignUserToTraining($training, $user);

        $this->assertInstanceOf(TrainingUser::class, $result);
        $this->assertEquals($training->id, $result->training_id);
        $this->assertEquals($user->id, $result->user_id);

        $this->assertDatabaseHas('training_user', [
            'training_id' => $training->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_remove_user_from_training_deletes_record(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        TrainingUser::create([
            'training_id' => $training->id,
            'user_id' => $user->id,
        ]);

        $result = $this->trainingUserRepository->removeUserFromTraining($training, $user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('training_user', [
            'training_id' => $training->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_remove_user_from_training_returns_false_when_no_record(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $result = $this->trainingUserRepository->removeUserFromTraining($training, $user);

        $this->assertFalse($result);
    }

    public function test_assign_all_users_to_training_assigns_all_users(): void
    {
        $training = Training::factory()->create();
        $users = User::factory()->count(3)->create();

        $this->userRepositoryMock
            ->shouldReceive('findAll')
            ->once()
            ->andReturn($users->toArray());

        $result = $this->trainingUserRepository->assignAllUsersToTraining($training);

        $this->assertEquals(3, $result);

        foreach ($users as $user) {
            $this->assertDatabaseHas('training_user', [
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }
    }

    public function test_assign_all_users_to_training_returns_zero_when_no_users(): void
    {
        $training = Training::factory()->create();

        $this->userRepositoryMock
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([]);

        $result = $this->trainingUserRepository->assignAllUsersToTraining($training);

        $this->assertEquals(0, $result);
    }

    public function test_assign_users_by_role_to_training_assigns_users_with_role(): void
    {
        $training = Training::factory()->create();
        $role = Role::create(['name' => 'test_role']);

        $usersWithRole = User::factory()->count(2)->create();
        foreach ($usersWithRole as $user) {
            $user->assignRole($role);
        }

        $usersWithoutRole = User::factory()->count(2)->create();

        $this->userRepositoryMock
            ->shouldReceive('findByRole')
            ->once()
            ->with('test_role')
            ->andReturn($usersWithRole->toArray());

        $result = $this->trainingUserRepository->assignUsersByRoleToTraining($training, 'test_role');

        $this->assertEquals(2, $result);

        foreach ($usersWithRole as $user) {
            $this->assertDatabaseHas('training_user', [
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }

        foreach ($usersWithoutRole as $user) {
            $this->assertDatabaseMissing('training_user', [
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }
    }

    public function test_assign_users_by_role_to_training_returns_zero_when_no_users_with_role(): void
    {
        $training = Training::factory()->create();
        Role::create(['name' => 'empty_role']);

        $this->userRepositoryMock
            ->shouldReceive('findByRole')
            ->once()
            ->with('empty_role')
            ->andReturn([]);

        $result = $this->trainingUserRepository->assignUsersByRoleToTraining($training, 'empty_role');

        $this->assertEquals(0, $result);
    }

    public function test_assign_selected_users_to_training_assigns_specified_users(): void
    {
        $training = Training::factory()->create();
        $users = User::factory()->count(3)->create();
        $userIds = $users->pluck('id')->toArray();

        $result = $this->trainingUserRepository->assignSelectedUsersToTraining($training, $userIds);

        $this->assertEquals(3, $result);

        foreach ($users as $user) {
            $this->assertDatabaseHas('training_user', [
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }
    }

    public function test_assign_selected_users_to_training_returns_zero_when_empty_array(): void
    {
        $training = Training::factory()->create();

        $result = $this->trainingUserRepository->assignSelectedUsersToTraining($training, []);

        $this->assertEquals(0, $result);
    }

    public function test_remove_all_users_from_training_removes_all_assignments(): void
    {
        $training = Training::factory()->create();
        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            TrainingUser::create([
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }

        $result = $this->trainingUserRepository->removeAllUsersFromTraining($training);

        $this->assertEquals(3, $result);

        foreach ($users as $user) {
            $this->assertDatabaseMissing('training_user', [
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }
    }

    public function test_get_training_users_returns_assigned_users(): void
    {
        $training = Training::factory()->create();
        $users = User::factory()->count(2)->create();

        foreach ($users as $user) {
            TrainingUser::create([
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }

        $result = $this->trainingUserRepository->getTrainingUsers($training);

        $this->assertCount(2, $result);

        foreach ($result as $trainingUser) {
            $this->assertEquals($training->id, $trainingUser['training_id']);
            $this->assertArrayHasKey('user', $trainingUser);
        }
    }

    public function test_get_user_trainings_returns_user_trainings(): void
    {
        $user = User::factory()->create();
        $trainings = Training::factory()->count(2)->create();

        foreach ($trainings as $training) {
            TrainingUser::create([
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }

        $result = $this->trainingUserRepository->getUserTrainings($user);

        $this->assertCount(2, $result);

        foreach ($result as $trainingUser) {
            $this->assertEquals($user->id, $trainingUser['user_id']);
            $this->assertArrayHasKey('training', $trainingUser);
        }
    }

    public function test_is_user_assigned_to_training_returns_true_when_assigned(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        TrainingUser::create([
            'training_id' => $training->id,
            'user_id' => $user->id,
        ]);

        $result = $this->trainingUserRepository->isUserAssignedToTraining($training, $user);

        $this->assertTrue($result);
    }

    public function test_is_user_assigned_to_training_returns_false_when_not_assigned(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $result = $this->trainingUserRepository->isUserAssignedToTraining($training, $user);

        $this->assertFalse($result);
    }

    public function test_get_training_user_count_returns_correct_count(): void
    {
        $training = Training::factory()->create();
        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            TrainingUser::create([
                'training_id' => $training->id,
                'user_id' => $user->id,
            ]);
        }

        $result = $this->trainingUserRepository->getTrainingUserCount($training);

        $this->assertEquals(3, $result);
    }

    public function test_get_training_user_count_returns_zero_when_no_assignments(): void
    {
        $training = Training::factory()->create();

        $result = $this->trainingUserRepository->getTrainingUserCount($training);

        $this->assertEquals(0, $result);
    }
}
