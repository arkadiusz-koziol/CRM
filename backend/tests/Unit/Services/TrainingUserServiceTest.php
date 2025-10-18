<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Interfaces\Repositories\TrainingUserRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\Training;
use App\Models\TrainingUser;
use App\Models\User;
use App\Services\TrainingUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class TrainingUserServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrainingUserService $trainingUserService;

    private $trainingUserRepositoryMock;

    private $trainingRepositoryMock;

    private $userRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trainingUserRepositoryMock = Mockery::mock(TrainingUserRepositoryInterface::class);
        $this->trainingRepositoryMock = Mockery::mock(TrainingRepositoryInterface::class);
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);

        $this->trainingUserService = new TrainingUserService(
            $this->trainingUserRepositoryMock,
            $this->trainingRepositoryMock,
            $this->userRepositoryMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_assign_user_to_training_calls_repository(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();
        $trainingUser = new TrainingUser;

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($user->id)
            ->andReturn($user);

        $this->trainingUserRepositoryMock
            ->shouldReceive('isUserAssignedToTraining')
            ->once()
            ->with($training, $user)
            ->andReturn(false);

        $this->trainingUserRepositoryMock
            ->shouldReceive('assignUserToTraining')
            ->once()
            ->with($training, $user)
            ->andReturn($trainingUser);

        $result = $this->trainingUserService->assignUserToTraining($training->id, $user->id);

        $this->assertSame($trainingUser, $result);
    }

    public function test_assign_user_to_training_throws_exception_when_training_not_found(): void
    {
        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Training not found');

        $this->trainingUserService->assignUserToTraining(999, 1);
    }

    public function test_assign_user_to_training_throws_exception_when_user_not_found(): void
    {
        $training = Training::factory()->create();

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('User not found');

        $this->trainingUserService->assignUserToTraining($training->id, 999);
    }

    public function test_assign_user_to_training_throws_exception_when_user_already_assigned(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($user->id)
            ->andReturn($user);

        $this->trainingUserRepositoryMock
            ->shouldReceive('isUserAssignedToTraining')
            ->once()
            ->with($training, $user)
            ->andReturn(true);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('User is already assigned to this training');

        $this->trainingUserService->assignUserToTraining($training->id, $user->id);
    }

    public function test_remove_user_from_training_calls_repository(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($user->id)
            ->andReturn($user);

        $this->trainingUserRepositoryMock
            ->shouldReceive('removeUserFromTraining')
            ->once()
            ->with($training, $user)
            ->andReturn(true);

        $result = $this->trainingUserService->removeUserFromTraining($training->id, $user->id);

        $this->assertTrue($result);
    }

    public function test_assign_all_users_to_training_calls_repository(): void
    {
        $training = Training::factory()->create();

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->trainingUserRepositoryMock
            ->shouldReceive('assignAllUsersToTraining')
            ->once()
            ->with($training)
            ->andReturn(5);

        $result = $this->trainingUserService->assignAllUsersToTraining($training->id);

        $this->assertEquals(5, $result);
    }

    public function test_assign_users_by_role_to_training_calls_repository(): void
    {
        $training = Training::factory()->create();

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->trainingUserRepositoryMock
            ->shouldReceive('assignUsersByRoleToTraining')
            ->once()
            ->with($training, 'admin')
            ->andReturn(3);

        $result = $this->trainingUserService->assignUsersByRoleToTraining($training->id, 'admin');

        $this->assertEquals(3, $result);
    }

    public function test_assign_selected_users_to_training_calls_repository(): void
    {
        $training = Training::factory()->create();
        $userIds = [1, 2, 3];

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->trainingUserRepositoryMock
            ->shouldReceive('assignSelectedUsersToTraining')
            ->once()
            ->with($training, $userIds)
            ->andReturn(3);

        $result = $this->trainingUserService->assignSelectedUsersToTraining($training->id, $userIds);

        $this->assertEquals(3, $result);
    }

    public function test_assign_selected_users_to_training_throws_exception_when_no_user_ids(): void
    {
        $training = Training::factory()->create();

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No user IDs provided');

        $this->trainingUserService->assignSelectedUsersToTraining($training->id, []);
    }

    public function test_get_training_users_calls_repository(): void
    {
        $training = Training::factory()->create();
        $users = [['id' => 1, 'name' => 'John']];

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->trainingUserRepositoryMock
            ->shouldReceive('getTrainingUsers')
            ->once()
            ->with($training)
            ->andReturn($users);

        $result = $this->trainingUserService->getTrainingUsers($training->id);

        $this->assertEquals($users, $result);
    }

    public function test_get_user_trainings_calls_repository(): void
    {
        $user = User::factory()->create();
        $trainings = [['id' => 1, 'title' => 'Training 1']];

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($user->id)
            ->andReturn($user);

        $this->trainingUserRepositoryMock
            ->shouldReceive('getUserTrainings')
            ->once()
            ->with($user)
            ->andReturn($trainings);

        $result = $this->trainingUserService->getUserTrainings($user->id);

        $this->assertEquals($trainings, $result);
    }

    public function test_get_user_trainings_throws_exception_when_user_not_found(): void
    {
        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('User not found');

        $this->trainingUserService->getUserTrainings(999);
    }

    public function test_is_user_assigned_to_training_returns_false_when_training_not_found(): void
    {
        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $result = $this->trainingUserService->isUserAssignedToTraining(999, 1);

        $this->assertFalse($result);
    }

    public function test_is_user_assigned_to_training_returns_false_when_user_not_found(): void
    {
        $training = Training::factory()->create();

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $result = $this->trainingUserService->isUserAssignedToTraining($training->id, 999);

        $this->assertFalse($result);
    }

    public function test_is_user_assigned_to_training_calls_repository(): void
    {
        $training = Training::factory()->create();
        $user = User::factory()->create();

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($user->id)
            ->andReturn($user);

        $this->trainingUserRepositoryMock
            ->shouldReceive('isUserAssignedToTraining')
            ->once()
            ->with($training, $user)
            ->andReturn(true);

        $result = $this->trainingUserService->isUserAssignedToTraining($training->id, $user->id);

        $this->assertTrue($result);
    }

    public function test_get_training_user_count_calls_repository(): void
    {
        $training = Training::factory()->create();

        $this->trainingRepositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($training->id)
            ->andReturn($training);

        $this->trainingUserRepositoryMock
            ->shouldReceive('getTrainingUserCount')
            ->once()
            ->with($training)
            ->andReturn(5);

        $result = $this->trainingUserService->getTrainingUserCount($training->id);

        $this->assertEquals(5, $result);
    }
}
