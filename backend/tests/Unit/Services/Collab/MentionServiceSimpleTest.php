<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Collab;

use App\Interfaces\Domain\User\UserInterface;
use App\Interfaces\Repositories\MentionRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\RealtimeNotificationServiceInterface;
use App\Services\Collab\MentionService;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class MentionServiceSimpleTest extends TestCase
{
    private MentionService $mentionService;

    private UserRepositoryInterface $userRepository;

    private MentionRepositoryInterface $mentionRepository;

    private RealtimeNotificationServiceInterface $notificationService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->mentionRepository = $this->createMock(MentionRepositoryInterface::class);
        $this->notificationService = $this->createMock(RealtimeNotificationServiceInterface::class);

        $this->mentionService = new MentionService(
            $this->mentionRepository,
            $this->userRepository,
            $this->notificationService,
            $this->createMock(\Psr\Log\LoggerInterface::class)
        );
    }

    public function test_extracts_mentions_from_content(): void
    {
        $content = 'Hello @john and @jane, please review this.';
        $commentId = Uuid::uuid7();
        $mentionerUserId = 999;
        $entityType = 'company';
        $entityId = Uuid::uuid7();

        $john = $this->createMock(UserInterface::class);
        $john->method('id')->willReturn(Uuid::fromInteger('1'));
        $john->method('intId')->willReturn(1);
        $john->method('username')->willReturn('john');
        $john->method('hasOptedOutOfMentions')->willReturn(false);
        $john->method('hasOptedOutOfMentionEmails')->willReturn(false);

        $jane = $this->createMock(UserInterface::class);
        $jane->method('id')->willReturn(Uuid::fromInteger('2'));
        $jane->method('intId')->willReturn(2);
        $jane->method('username')->willReturn('jane');
        $jane->method('hasOptedOutOfMentions')->willReturn(false);
        $jane->method('hasOptedOutOfMentionEmails')->willReturn(false);

        $this->userRepository
            ->expects($this->exactly(2))
            ->method('findByUsername')
            ->willReturnMap([
                ['john', $john],
                ['jane', $jane],
            ]);

        $mentionerUser = $this->createMock(\App\Interfaces\Domain\User\UserInterface::class);
        $mentionerUser->method('id')->willReturn(Uuid::fromInteger((string) $mentionerUserId));
        $mentionerUser->method('intId')->willReturn($mentionerUserId);
        $mentionerUser->method('username')->willReturn('mentioner');
        $mentionerUser->method('hasOptedOutOfMentions')->willReturn(false);
        $mentionerUser->method('hasOptedOutOfMentionEmails')->willReturn(false);

        $this->userRepository
            ->expects($this->exactly(4)) // 2 for mentioned users + 2 for mentioner users
            ->method('findByIntId')
            ->willReturnCallback(function ($id) use ($john, $jane, $mentionerUser) {
                // Assuming john has id 1, jane has id 2, mentioner has id 999
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
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $mentions);
    }
}
