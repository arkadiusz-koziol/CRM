<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (string) $user->id === (string) $userId;
});

Broadcast::channel('entity.{entityType}.{entityId}', function ($user, $entityType, $entityId) {
    // Check if user has access to the entity based on type
    return match ($entityType) {
        'company' => $user->can('company.view') && $this->hasAccessToCompany($user, $entityId),
        'contact' => $user->can('contact.view') && $this->hasAccessToContact($user, $entityId),
        'opportunity' => $user->can('opportunity.view') && $this->hasAccessToOpportunity($user, $entityId),
        'task' => $user->can('task.view') && $this->hasAccessToTask($user, $entityId),
        default => false,
    };
});

Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    return $user->teams()->where('team_id', $teamId)->exists();
});

Broadcast::channel('admin', function ($user) {
    return $user->hasRole('admin');
});

// Helper methods for entity access checks
function hasAccessToCompany($user, $companyId)
{
    // Check if user is assigned to the company or has admin role
    return $user->companies()->where('company_id', $companyId)->exists() || 
           $user->hasRole('admin');
}

function hasAccessToContact($user, $contactId)
{
    // Check if user is the owner of the contact or has admin role
    return $user->contacts()->where('contact_id', $contactId)->exists() || 
           $user->hasRole('admin');
}

function hasAccessToOpportunity($user, $opportunityId)
{
    // Check if user is the owner of the opportunity or has admin role
    return $user->opportunities()->where('opportunity_id', $opportunityId)->exists() || 
           $user->hasRole('admin');
}

function hasAccessToTask($user, $taskId)
{
    // Check if user is assigned to the task or is the creator
    return $user->tasks()->where('task_id', $taskId)->exists() || 
           $user->createdTasks()->where('task_id', $taskId)->exists() || 
           $user->hasRole('admin');
}
