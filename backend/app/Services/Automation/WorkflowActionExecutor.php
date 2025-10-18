<?php

declare(strict_types=1);

namespace App\Services\Automation;

use App\Enums\Automation\WorkflowActionType;
use Psr\Log\LoggerInterface;

final class WorkflowActionExecutor
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function execute(array $action): void
    {
        $type = $action['type'] ?? '';
        $config = $action['config'] ?? [];

        if (! WorkflowActionType::tryFrom($type)) {
            $this->logger->warning('Unknown workflow action type', ['type' => $type]);

            return;
        }

        match (WorkflowActionType::from($type)) {
            WorkflowActionType::CREATE_TASK => $this->executeCreateTask($config),
            WorkflowActionType::SEND_EMAIL => $this->executeSendEmail($config),
            WorkflowActionType::SEND_SMS => $this->executeSendSms($config),
            WorkflowActionType::ADD_TAG => $this->executeAddTag($config),
            WorkflowActionType::ASSIGN_OWNER => $this->executeAssignOwner($config),
        };
    }

    private function executeCreateTask(array $config): void
    {
        $title = $config['title'] ?? 'Workflow Generated Task';
        $description = $config['description'] ?? '';
        $assigneeId = $config['assignee_id'] ?? null;
        $dueDate = $config['due_date'] ?? null;

        $this->logger->info('Creating task via workflow', [
            'title' => $title,
            'assignee_id' => $assigneeId,
            'due_date' => $dueDate,
        ]);

        // In a real implementation, you would create the task in the database
        // For now, we'll just log the action
    }

    private function executeSendEmail(array $config): void
    {
        $to = $config['to'] ?? '';
        $subject = $config['subject'] ?? 'Workflow Notification';
        $body = $config['body'] ?? '';

        $this->logger->info('Sending email via workflow', [
            'to' => $to,
            'subject' => $subject,
        ]);

        // In a real implementation, you would send the email
        // For now, we'll just log the action
    }

    private function executeSendSms(array $config): void
    {
        $to = $config['to'] ?? '';
        $message = $config['message'] ?? 'Workflow Notification';

        $this->logger->info('Sending SMS via workflow', [
            'to' => $to,
            'message' => $message,
        ]);

        // In a real implementation, you would send the SMS
        // For now, we'll just log the action
    }

    private function executeAddTag(array $config): void
    {
        $entityType = $config['entity_type'] ?? '';
        $entityId = $config['entity_id'] ?? '';
        $tag = $config['tag'] ?? '';

        $this->logger->info('Adding tag via workflow', [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'tag' => $tag,
        ]);

        // In a real implementation, you would add the tag to the entity
        // For now, we'll just log the action
    }

    private function executeAssignOwner(array $config): void
    {
        $entityType = $config['entity_type'] ?? '';
        $entityId = $config['entity_id'] ?? '';
        $ownerId = $config['owner_id'] ?? '';

        $this->logger->info('Assigning owner via workflow', [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'owner_id' => $ownerId,
        ]);

        // In a real implementation, you would assign the owner to the entity
        // For now, we'll just log the action
    }
}
