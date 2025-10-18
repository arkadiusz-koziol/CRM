# Real-time Notifications Contract

## Overview

This document defines the contract for real-time notifications using WebSockets and Redis Pub/Sub.

## Channels

### User Channels
- **Pattern**: `user.{userId}`
- **Access**: Private channel for specific user
- **Authorization**: User must match the channel user ID

### Entity Channels
- **Pattern**: `entity.{entityType}.{entityId}`
- **Access**: Private channel for entity observers
- **Authorization**: User must have access to the entity based on type and permissions

### Team Channels
- **Pattern**: `team.{teamId}`
- **Access**: Private channel for team members
- **Authorization**: User must be a member of the team

### Admin Channels
- **Pattern**: `admin`
- **Access**: Private channel for administrators
- **Authorization**: User must have admin role

## Events

### Task Assigned
- **Event**: `task.assigned`
- **Channels**: `user.{assignedToUserId}`
- **Payload**:
  ```json
  {
    "task_id": "uuid",
    "assigned_to_user_id": "uuid",
    "assigned_by_user_id": "uuid",
    "task_title": "string",
    "task_description": "string",
    "due_date": "ISO8601 string | null",
    "timestamp": "ISO8601 string"
  }
  ```

### Comment Added
- **Event**: `comment.added`
- **Channels**: `entity.{entityType}.{entityId}`, `user.{observerId}` (for each observer)
- **Payload**:
  ```json
  {
    "comment_id": "uuid",
    "entity_type": "string",
    "entity_id": "uuid",
    "author_id": "uuid",
    "author_name": "string",
    "comment": "string",
    "timestamp": "ISO8601 string"
  }
  ```

### Status Changed
- **Event**: `status.changed`
- **Channels**: `entity.{entityType}.{entityId}`, `user.{observerId}` (for each observer)
- **Payload**:
  ```json
  {
    "entity_type": "string",
    "entity_id": "uuid",
    "old_status": "string",
    "new_status": "string",
    "changed_by_user_id": "uuid",
    "changed_by_name": "string",
    "timestamp": "ISO8601 string"
  }
  ```

### Opportunity Stage Changed
- **Event**: `opportunity.stage.changed`
- **Channels**: `entity.opportunity.{opportunityId}`, `user.{observerId}` (for each observer)
- **Payload**:
  ```json
  {
    "opportunity_id": "uuid",
    "old_stage_id": "uuid",
    "new_stage_id": "uuid",
    "old_stage_name": "string",
    "new_stage_name": "string",
    "changed_by_user_id": "uuid",
    "changed_by_name": "string",
    "timestamp": "ISO8601 string"
  }
  ```

## Rate Limiting

- **Limit**: 100 notifications per minute per user
- **Strategy**: Exponential backoff on rate limit exceeded
- **Headers**: Rate limit information included in response headers

## Reconnection Strategy

- **Strategy**: Exponential backoff
- **Max Attempts**: 5
- **Base Delay**: 1000ms
- **Max Delay**: 30000ms
- **Jitter**: Random delay variation to prevent thundering herd

## Error Handling

### Connection Errors
- **Timeout**: 30 seconds
- **Retry**: Automatic with exponential backoff
- **Fallback**: Email/SMS notifications for critical events

### Authentication Errors
- **401**: Invalid or expired token
- **403**: Insufficient permissions for channel
- **Response**: Clear error message with suggested action

## Security

### Channel Authorization
- All channels require proper authentication
- Entity access is validated based on user permissions
- Admin channels require admin role

### Rate Limiting
- Per-user rate limiting to prevent abuse
- Global rate limiting for system protection
- Graceful degradation on rate limit exceeded

### Data Privacy
- Only authorized users receive notifications
- Sensitive data is not included in payloads
- User IDs are used instead of personal information

## Implementation Notes

### Frontend Integration
```javascript
// Laravel Echo setup
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            Authorization: `Bearer ${token}`,
        },
    },
});

// Listen to user notifications
window.Echo.private(`user.${userId}`)
    .listen('task.assigned', (e) => {
        console.log('Task assigned:', e);
    });

// Listen to entity notifications
window.Echo.private(`entity.${entityType}.${entityId}`)
    .listen('comment.added', (e) => {
        console.log('Comment added:', e);
    });
```

### Backend Integration
```php
// Dispatch notification
$notificationService->notifyTaskAssigned(
    $taskId,
    $assignedToUserId,
    $assignedByUserId,
    $taskTitle,
    $taskDescription,
    $dueDate
);
```

## Testing

### Unit Tests
- Event dispatching
- Channel authorization
- Rate limiting
- Error handling

### Integration Tests
- End-to-end notification flow
- Channel access validation
- Rate limit enforcement
- Reconnection behavior

### Performance Tests
- High-volume notification handling
- Channel scalability
- Rate limit effectiveness
- Memory usage under load
