<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Collab;

use App\Interfaces\Repositories\MentionRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\RealtimeNotificationServiceInterface;
use App\Services\Collab\MentionService;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class MentionServiceDebugTest extends TestCase
{
    public function test_debug_parse_mentions(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $mentionRepository = $this->createMock(MentionRepositoryInterface::class);
        $notificationService = $this->createMock(RealtimeNotificationServiceInterface::class);
        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);

        $mentionService = new MentionService(
            $mentionRepository,
            $userRepository,
            $notificationService,
            $logger
        );

        $content = 'Hello @john, please review this.';
        $commentId = Uuid::uuid7();
        $mentionerUserId = 999;
        $entityType = 'company';
        $entityId = Uuid::uuid7();

        // Set up a user that will be found
        $john = $this->createMock(\App\Interfaces\Domain\User\UserInterface::class);
        $john->method('id')->willReturn(Uuid::fromInteger('1'));
        $john->method('intId')->willReturn(1);
        $john->method('username')->willReturn('john');
        $john->method('hasOptedOutOfMentions')->willReturn(false);
        $john->method('hasOptedOutOfMentionEmails')->willReturn(false);

        $userRepository->method('findByUsername')->willReturn($john);
        $userRepository->method('findByIntId')->willReturn($john);
        $mentionRepository->method('findByCommentAndUser')->willReturn(null);
        $mentionRepository->method('save');
        $notificationService->method('sendMentionNotification');
        $notificationService->method('sendMentionEmail');

        // Don't set up any expectations, just call the method
        $mentions = $mentionService->parseAndCreateMentions(
            $content,
            $commentId,
            $mentionerUserId,
            $entityType,
            $entityId,
        );

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $mentions);
        $this->assertCount(1, $mentions); // Should be 1 because user is found
    }
}
