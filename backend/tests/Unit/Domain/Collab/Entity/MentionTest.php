<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Collab\Entity;

use App\Domain\Collab\Entity\Mention;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class MentionTest extends TestCase
{
    public function test_can_create_mention(): void
    {
        $id = Uuid::uuid7();
        $commentId = Uuid::uuid7();
        $entityId = Uuid::uuid7();
        $createdAt = Carbon::now();

        $mention = Mention::create(
            $id,
            $commentId,
            1, // mentionedUserId
            2, // mentionerUserId
            'company',
            $entityId,
            $createdAt,
        );

        $this->assertTrue($mention->id()->equals($id));
        $this->assertTrue($mention->commentId()->equals($commentId));
        $this->assertEquals(1, $mention->mentionedUserId());
        $this->assertEquals(2, $mention->mentionerUserId());
        $this->assertEquals('company', $mention->entityType());
        $this->assertTrue($mention->entityId()->equals($entityId));
        $this->assertEquals($createdAt, $mention->createdAt());
        $this->assertNull($mention->notifiedAt());
        $this->assertNull($mention->readAt());
        $this->assertFalse($mention->isNotified());
        $this->assertFalse($mention->isRead());
    }

    public function test_can_mark_as_notified(): void
    {
        $mention = $this->createMention();
        $notifiedAt = Carbon::now();

        $notifiedMention = $mention->markAsNotified($notifiedAt);

        $this->assertTrue($notifiedMention->isNotified());
        $this->assertEquals($notifiedAt, $notifiedMention->notifiedAt());
        $this->assertFalse($notifiedMention->isRead());
    }

    public function test_can_mark_as_read(): void
    {
        $mention = $this->createMention();
        $readAt = Carbon::now();

        $readMention = $mention->markAsRead($readAt);

        $this->assertTrue($readMention->isRead());
        $this->assertEquals($readAt, $readMention->readAt());
    }

    public function test_can_mark_as_both_notified_and_read(): void
    {
        $mention = $this->createMention();
        $notifiedAt = Carbon::now()->subMinutes(5);
        $readAt = Carbon::now();

        $notifiedMention = $mention->markAsNotified($notifiedAt);
        $readMention = $notifiedMention->markAsRead($readAt);

        $this->assertTrue($readMention->isNotified());
        $this->assertTrue($readMention->isRead());
        $this->assertEquals($notifiedAt, $readMention->notifiedAt());
        $this->assertEquals($readAt, $readMention->readAt());
    }

    private function createMention(): Mention
    {
        return Mention::create(
            Uuid::uuid7(),
            Uuid::uuid7(),
            1,
            2,
            'company',
            Uuid::uuid7(),
            Carbon::now(),
        );
    }
}
