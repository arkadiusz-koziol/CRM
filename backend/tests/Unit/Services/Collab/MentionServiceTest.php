<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Collab;

use App\Domain\Collab\Entity\Mention;
use App\Interfaces\Domain\User\UserInterface;
use App\Interfaces\Repositories\MentionRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\RealtimeNotificationServiceInterface;
use App\Services\Collab\MentionService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class MentionServiceTest extends TestCase
{
    private MentionService $mentionService;

    private MentionRepositoryInterface $mentionRepository;

    private UserRepositoryInterface $userRepository;

    private RealtimeNotificationServiceInterface $notificationService;

    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->mentionRepository = $this->createMock(MentionRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->notificationService = $this->createMock(RealtimeNotificationServiceInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->mentionService = new MentionService(
            $this->mentionRepository,
            $this->userRepository,
            $this->notificationService,
            $this->logger,
        );
    }

    public function test_parses_mentions_from_content(): void
    {
        $content = 'Hello @john and @jane, please review this.';
        $commentId = Uuid::uuid7();
        $mentionerUserId = 999;
        $entityType = 'company';
        $entityId = Uuid::uuid7();

        $john = $this->createUser(1, 'john');
        $jane = $this->createUser(2, 'jane');

        $this->userRepository
            ->expects($this->exactly(2))
            ->method('findByUsername')
            ->willReturnMap([
                ['john', $john],
                ['jane', $jane],
            ]);

        $mentionerUser = $this->createUser($mentionerUserId, 'mentioner');

        $this->userRepository
            ->expects($this->exactly(4)) // 2 for mentioned users + 2 for mentioner users
            ->method('findByIntId')
            ->willReturnCallback(function ($id) use ($john, $jane, $mentionerUser) {
                if ($id === 1) {
                    return $john;
                }
                if ($id === 2) {
                    return $jane;
                }
                if ($id === 999) {
                    return $mentionerUser;
                }

            });

        $this->mentionRepository
            ->expects($this->exactly(2))
            ->method('findByCommentAndUser')
            ->willReturn(null);

        $this->mentionRepository
            ->expects($this->exactly(4)) // 2 for initial save + 2 for notification save
            ->method('save');

        $this->notificationService
            ->expects($this->exactly(2))
            ->method('sendMentionNotification');

        $this->notificationService
            ->expects($this->exactly(2))
            ->method('sendMentionEmail');

        $mentions = $this->mentionService->parseAndCreateMentions(
            $content,
            $commentId,
            $mentionerUserId,
            $entityType,
            $entityId,
        );

        $this->assertCount(2, $mentions);
    }

    public function test_skips_self_mentions(): void
    {
        $content = 'Hello @john, please review this.';
        $commentId = Uuid::uuid7();
        $mentionerUserId = 1;
        $entityType = 'company';
        $entityId = Uuid::uuid7();

        $john = $this->createUser(1, 'john');

        $this->userRepository
            ->expects($this->once())
            ->method('findByUsername')
            ->with('john')
            ->willReturn($john);

        $this->mentionRepository
            ->expects($this->never())
            ->method('save');

        $mentions = $this->mentionService->parseAndCreateMentions(
            $content,
            $commentId,
            $mentionerUserId,
            $entityType,
            $entityId,
        );

        $this->assertCount(0, $mentions);
    }

    public function test_skips_users_who_opted_out(): void
    {
        $content = 'Hello @john, please review this.';
        $commentId = Uuid::uuid7();
        $mentionerUserId = 999;
        $entityType = 'company';
        $entityId = Uuid::uuid7();

        $john = $this->createOptedOutUser(1, 'john');

        $this->userRepository
            ->expects($this->once())
            ->method('findByUsername')
            ->with('john')
            ->willReturn($john);

        $this->mentionRepository
            ->expects($this->never())
            ->method('save');

        $mentions = $this->mentionService->parseAndCreateMentions(
            $content,
            $commentId,
            $mentionerUserId,
            $entityType,
            $entityId,
        );

        $this->assertCount(0, $mentions);
    }

    public function test_skips_duplicate_mentions(): void
    {
        $content = 'Hello @john, please review this.';
        $commentId = Uuid::uuid7();
        $mentionerUserId = 999;
        $entityType = 'company';
        $entityId = Uuid::uuid7();

        $john = $this->createUser(1, 'john');
        $existingMention = Mention::create(
            Uuid::uuid7(),
            $commentId,
            1,
            $mentionerUserId,
            $entityType,
            $entityId,
            Carbon::now(),
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findByUsername')
            ->with('john')
            ->willReturn($john);

        $this->mentionRepository
            ->expects($this->once())
            ->method('findByCommentAndUser')
            ->willReturn($existingMention);

        $this->mentionRepository
            ->expects($this->never())
            ->method('save');

        $mentions = $this->mentionService->parseAndCreateMentions(
            $content,
            $commentId,
            $mentionerUserId,
            $entityType,
            $entityId,
        );

        $this->assertCount(0, $mentions);
    }

    public function test_handles_nonexistent_users(): void
    {
        $content = 'Hello @nonexistent, please review this.';
        $commentId = Uuid::uuid7();
        $mentionerUserId = 999;
        $entityType = 'company';
        $entityId = Uuid::uuid7();

        $this->userRepository
            ->expects($this->once())
            ->method('findByUsername')
            ->with('nonexistent')
            ->willReturn(null);

        $this->logger
            ->expects($this->once())
            ->method('warning');

        $this->mentionRepository
            ->expects($this->never())
            ->method('save');

        $mentions = $this->mentionService->parseAndCreateMentions(
            $content,
            $commentId,
            $mentionerUserId,
            $entityType,
            $entityId,
        );

        $this->assertCount(0, $mentions);
    }

    public function test_marks_mention_as_read(): void
    {
        $mentionId = Uuid::uuid7();
        $userId = 1;

        $mention = Mention::create(
            $mentionId,
            Uuid::uuid7(),
            $userId,
            2,
            'company',
            Uuid::uuid7(),
            Carbon::now(),
        );

        $this->mentionRepository
            ->expects($this->once())
            ->method('findByUuid')
            ->with($mentionId)
            ->willReturn($mention);

        $this->mentionRepository
            ->expects($this->once())
            ->method('save');

        $this->mentionService->markAsRead($mentionId, $userId);
    }

    public function test_prevents_other_users_from_marking_as_read(): void
    {
        $mentionId = Uuid::uuid7();
        $userId = 1;
        $otherUserId = 2;

        $mention = Mention::create(
            $mentionId,
            Uuid::uuid7(),
            $otherUserId,
            3,
            'company',
            Uuid::uuid7(),
            Carbon::now(),
        );

        $this->mentionRepository
            ->expects($this->once())
            ->method('findByUuid')
            ->with($mentionId)
            ->willReturn($mention);

        $this->mentionRepository
            ->expects($this->never())
            ->method('save');

        $this->mentionService->markAsRead($mentionId, $userId);
    }

    public function test_gets_mention_stats(): void
    {
        $userId = 1;

        $this->mentionRepository
            ->expects($this->once())
            ->method('countByMentionedUser')
            ->with($userId)
            ->willReturn(10);

        $this->mentionRepository
            ->expects($this->once())
            ->method('countUnreadByMentionedUser')
            ->with($userId)
            ->willReturn(3);

        $this->mentionRepository
            ->expects($this->once())
            ->method('countByMentionedUserSince')
            ->with($userId, $this->isInstanceOf(Carbon::class))
            ->willReturn(5);

        $stats = $this->mentionService->getMentionStats($userId);

        $this->assertEquals([
            'total_mentions' => 10,
            'unread_mentions' => 3,
            'mentions_this_week' => 5,
        ], $stats);
    }

    private function createUser(int $id, string $username, ?UuidInterface $uuid = null): UserInterface
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('id')->willReturn($uuid ?? Uuid::fromInteger((string) $id));
        $user->method('intId')->willReturn($id);
        $user->method('username')->willReturn($username);
        $user->method('hasOptedOutOfMentions')->willReturn(false);
        $user->method('hasOptedOutOfMentionEmails')->willReturn(false);

        return $user;
    }

    private function createMockUser(int $id, string $username): UserInterface
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('id')->willReturn(Uuid::fromInteger((string) $id));
        $user->method('intId')->willReturn($id);
        $user->method('username')->willReturn($username);
        $user->method('hasOptedOutOfMentions')->willReturn(false);
        $user->method('hasOptedOutOfMentionEmails')->willReturn(false);

        return $user;
    }

    private function createOptedOutUser(int $id, string $username): UserInterface
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('id')->willReturn(Uuid::fromInteger((string) $id));
        $user->method('intId')->willReturn($id);
        $user->method('username')->willReturn($username);
        $user->method('hasOptedOutOfMentions')->willReturn(true);
        $user->method('hasOptedOutOfMentionEmails')->willReturn(false);

        return $user;
    }
}
