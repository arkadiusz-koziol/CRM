Tasks Backlog
<!--GLOBAL_LOCK: free-->
## TASK: TSK-010
title: "Backend: Admin Panel – Car List View #10"
branch: "feature/tsk-010-car-list-view"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "APPROVED"
last_update: "2025-01-27T11:30:00+00:00"
lock: "free"
checksum: ""

Acceptance Criteria
- [x] Display all cars
- [x] Show Name and Registration number
- [x] Filtering by name and registration number
- [x] Sorting by name and registration number
Work Notes (by dev)
**Commits:**
- feat: implement Car list endpoint with pagination and search
- feat: add Car resource for JSON:API response format
- feat: create comprehensive test suite for Car list functionality
- refactor: update Car model, DTO, and repository with strict typing

**Files Changed:**
- `app/Interfaces/Repositories/CarRepositoryInterface.php` - Added list methods
- `app/Repositories/CarRepository.php` - Implemented pagination and search
- `app/Services/CarService.php` - Added list service methods
- `app/Http/Controllers/Admin/Cars/ListCarController.php` - New list controller
- `app/Http/Resources/CarResource.php` - New JSON:API resource
- `routes/api.php` - Added list route
- `database/factories/CarFactory.php` - New factory for testing
- `tests/Feature/Admin/Car/CarListTest.php` - Feature tests
- `tests/Unit/Services/CarServiceTest.php` - Service unit tests
- `tests/Unit/Repositories/CarRepositoryTest.php` - Repository unit tests

**Tests:**
- 15 feature tests covering pagination, search, permissions, edge cases
- 6 unit tests for CarService
- 10 unit tests for CarRepository
- All tests follow project patterns and include proper mocking

**Technical Notes:**
- Implemented pagination with configurable page size (1-100 limit)
- Search functionality works on both name and registration_number fields
- Proper error handling with logging
- Follows JSON:API specification for responses
- All code follows strict typing requirements (declare(strict_types=1))
- Uses final classes and proper dependency injection
- Includes comprehensive OpenAPI documentation
- Soft deletes are properly handled (excluded from results)

**FIXES APPLIED (after reviewer feedback):**
- **Repository Pattern**: Created `EloquentRepository` base class and updated `CarRepository` to extend it
- **DTO Factory**: Changed `CarDtoFactory::fromRequest()` to `CarDtoFactory::fromArray()` method
- **Controller Response**: Updated `ListCarController` to use `CarListResource` wrapper instead of raw arrays
- **Method Naming**: Renamed conflicting methods (`create` → `createCar`, `findAll` → `findAllCars`)
- **Architecture Compliance**: All changes now follow project architecture rules
Review Notes (by reviewer)
**APPROVED** - All architectural violations have been successfully addressed:

✅ **Repository Pattern**: `CarRepository` now properly extends `EloquentRepository` base class
✅ **DTO Factory**: `CarDtoFactory::fromArray()` method implemented correctly per rule #124-125
✅ **Controller Response**: `ListCarController` now uses `CarListResource` wrapper as required by rule #132
✅ **Resource Usage**: `CarListResource` properly implemented and used in controller per rule #136-138
✅ **Base Repository**: `EloquentRepository` base class created and properly implemented
✅ **Method Naming**: Conflicting methods renamed (`create` → `createCar`, `findAll` → `findAllCars`)
✅ **Architecture Compliance**: All changes now follow project architecture rules
✅ **Functional Requirements**: All Acceptance Criteria met with comprehensive test coverage

**LGTM** - Implementation is now fully compliant with all architectural rules and ready for production.
---
## TASK: TSK-011
title: "Backend: Admin Panel – Edit Car #11"
branch: "feature/tsk-011-edit-car"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "TODO"
last_update: "2025-10-17T18:30:00+02:00"
lock: "free"
checksum: ""

Acceptance Criteria
- [ ] Editable fields: Name, Description (nullable), Registration number
- [ ] Pre-filled form with existing data
Work Notes (by dev)
*(wypełni dev)*
Review Notes (by reviewer)
*(wypełni reviewer)*
---
## TASK: TSK-012
title: "Backend: Admin Panel – Delete Car #12"
branch: "feature/tsk-012-delete-car"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "TODO"
last_update: "2025-10-17T18:30:00+02:00"
lock: "free"
checksum: ""

Acceptance Criteria
- [ ] Remove specified car from database
- [ ] After deletion refresh the list
- [ ] Show success or error notification
Work Notes (by dev)
*(wypełni dev)*
Review Notes (by reviewer)
*(wypełni reviewer)*
---
## TASK: TSK-013
title: "Backend: Admin Panel – Show Car Details #13"
branch: "feature/tsk-013-show-car-details"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "TODO"
last_update: "2025-10-17T18:30:00+02:00"
lock: "free"
checksum: ""

Acceptance Criteria
- [ ] Endpoint to show single car details
- [ ] Displayed fields: Name, Description, Registration number
Work Notes (by dev)
*(wypełni dev)*
Review Notes (by reviewer)
*(wypełni reviewer)*
---
## TASK: TSK-014
title: "Backend: Admin Panel – Create Training #14"
branch: "feature/tsk-014-create-training"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "TODO"
last_update: "2025-10-17T18:30:00+02:00"
lock: "free"
checksum: ""

Acceptance Criteria
- [ ] Fields: Title/Name, Description (optional), Category
- [ ] Support file attachments (.pptx, .pdf)
- [ ] User assignment: All, By role, Manually
- [ ] Validate required fields
Work Notes (by dev)
*(wypełni dev)*
Review Notes (by reviewer)
*(wypełni reviewer)*
---
## TASK: TSK-015
title: "Backend: Admin Panel – Edit Training #15"
branch: "feature/tsk-015-edit-training"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "TODO"
last_update: "2025-10-17T18:30:00+02:00"
lock: "free"
checksum: ""

Acceptance Criteria
- [ ] Pre-filled fields from creation
- [ ] Allow changing Title, Description, Category
- [ ] Allow add/remove files
- [ ] Update user assignment
Work Notes (by dev)
*(wypełni dev)*
Review Notes (by reviewer)
*(wypełni reviewer)*
---
## TASK: TSK-016
title: "Backend: Admin Panel – Delete Training #16"
branch: "feature/tsk-016-delete-training"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "TODO"
last_update: "2025-10-17T18:30:00+02:00"
lock: "free"
checksum: ""

Acceptance Criteria
- [ ] Endpoint to delete training
- [ ] Remove training and associated relations (files, user links)
Work Notes (by dev)
*(wypełni dev)*
Review Notes (by reviewer)
*(wypełni reviewer)*
---
## TASK: TSK-017
title: "Backend: Admin Panel – Assign Files to Training #17"
branch: "feature/tsk-017-assign-files-training"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "TODO"
last_update: "2025-10-17T18:30:00+02:00"
lock: "free"
checksum: ""

Acceptance Criteria
- [ ] Support file upload (.pptx, .pdf, etc.)
- [ ] Save relation between training and files
- [ ] Allow multiple file uploads
Work Notes (by dev)
*(wypełni dev)*
Review Notes (by reviewer)
*(wypełni reviewer)*
---
## TASK: TSK-018
title: "Backend: Admin Panel – Training Categories #18"
branch: "feature/tsk-018-training-categories"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "TODO"
last_update: "2025-10-17T18:30:00+02:00"
lock: "free"
checksum: ""

Acceptance Criteria
- [ ] Create trainings_categories table (id, name, created_at, updated_at)
- [ ] Each training must belong to a category
Work Notes (by dev)
*(wypełni dev)*
Review Notes (by reviewer)
*(wypełni reviewer)*
---
## TASK: TSK-019
title: "Backend: Admin Panel – Users assignation to trainings #19"
branch: "feature/tsk-019-users-assignation-trainings"
assignee: "cursor-dev"
reviewer: "openai-reviewer"
status: "TODO"
last_update: "2025-10-17T18:30:00+02:00"
lock: "free"
checksum: ""

Acceptance Criteria
- [ ] Create training_user table (id, training_id, user_id, created_at, updated_at)
- [ ] Support assignment: All users, By role, Manually selected
Work Notes (by dev)
*(wypełni dev)*
Review Notes (by reviewer)
*(wypełni reviewer)*
---
