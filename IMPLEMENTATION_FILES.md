# Frontend-Backend Integration: File-by-File Action Plan

## BACKEND: Files to Create/Modify

### ✏️ CREATE: `app/Traits/ApiResponse.php`
**Purpose**: Standardize JSON responses across all endpoints

**Action**: Create this new file  
**Lines**: ~80 lines of PHP utility code  
**Priority**: 🔴 CRITICAL

```
Backend Root
  └─ app/
      └─ Traits/
          └─ ApiResponse.php (NEW)
```

### ✏️ CREATE: `routes/api.php`
**Purpose**: Separate API routes from web routes

**Action**: Create new file with all API endpoints  
**Lines**: ~60 lines  
**Priority**: 🔴 CRITICAL

```
Backend Root
  └─ routes/
      └─ api.php (NEW)
```

### ✏️ MODIFY: `app/Http/Controllers/RepositoryController.php`
**Purpose**: Add `use ApiResponse;` and update methods to return JSON

**Action**: 
1. Add `use App\Traits\ApiResponse;` at top
2. Convert 15+ methods to use traits (return $this->success(), $this->error())
3. Examples provided in INTEGRATION_IMPLEMENTATION_GUIDE.md Part A

**Lines**: Change ~200 lines  
**Priority**: 🔴 CRITICAL

```
Backend Root
  └─ app/
      └─ Http/
          └─ Controllers/
              └─ RepositoryController.php (MODIFY)
```

### ✏️ MODIFY: `config/cors.php`
**Purpose**: Enable CORS for frontend origin

**Action**: Update 'allowed_origins' array
```php
'allowed_origins' => ['http://localhost:5173', 'http://localhost:3000'],
```

**Lines**: Change 1 line  
**Priority**: 🟡 IMPORTANT

```
Backend Root
  └─ config/
      └─ cors.php (MODIFY)
```

### ✏️ MODIFY: `.env`
**Purpose**: Configure for development

**Action**: Ensure these are set:
```
APP_DEBUG=true
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000
FILESYSTEM_DISK=public
```

**Lines**: Add/Update 3 lines  
**Priority**: 🟡 IMPORTANT

```
Backend Root
  └─ .env (MODIFY)
```

---

## FRONTEND: Files to Create/Modify

### ✏️ CREATE: `src/services/api.ts`
**Purpose**: HTTP client for all backend API calls

**Action**: Create new file with ApiClient class  
**Lines**: ~300 lines of TypeScript  
**Priority**: 🔴 CRITICAL

```
Frontend Root
  └─ src/
      └─ services/
          └─ api.ts (NEW)
```

**Exports**:
- `ApiClient` class
- `api` singleton instance
- `ApiResponse<T>` interface
- `PaginatedResponse<T>` interface

### ✏️ CREATE: `src/hooks/useApi.ts`
**Purpose**: React hook for data fetching with loading/error/data states

**Action**: Create new file with useApi hook  
**Lines**: ~50 lines of TypeScript  
**Priority**: 🔴 CRITICAL

```
Frontend Root
  └─ src/
      └─ hooks/
          └─ useApi.ts (NEW)
```

**Exports**:
- `useApi<T>` hook
- `UseApiState<T>` interface

### ✏️ MODIFY: `src/context/RoleContext.tsx`
**Purpose**: Replace mock user with real API authentication

**Action**: 
1. Replace static mock user with fetch from `/api/user`
2. Add `login(email, password)` method
3. Add `logout()` method
4. Use `useEffect` to fetch current user on mount

**Lines**: Rewrite ~60 lines  
**Priority**: 🔴 CRITICAL

```
Frontend Root
  └─ src/
      └─ context/
          └─ RoleContext.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/public/PublicSearch.tsx`
**Purpose**: Replace mock data with real API calls

**Action**: 
1. Remove: `import { DOCUMENTS } from "@/data/mockData"`
2. Add: `import { api } from "@/services/api"`
3. Add: `useEffect` to call `api.searchDocuments(query, type, year)`
4. Replace: Static `PUBLISHED` filter with API response
5. Add: loading/error states

**Lines**: Change ~50 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ public/
              └─ PublicSearch.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/student/UploadWizard.tsx`
**Purpose**: Connect form submission to API

**Action**: 
1. Replace: Mock form submission with `api.uploadDocument(formData)`
2. Add: Error handling
3. Add: Show response accession number

**Lines**: Change ~40 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ student/
              └─ UploadWizard.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/student/StudentDashboard.tsx`
**Purpose**: Fetch real user documents

**Action**: 
1. Remove: Mock DOCUMENTS
2. Add: Fetch from API based on user.id
3. Replace: Static stats with real document counts

**Lines**: Change ~30 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ student/
              └─ StudentDashboard.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/student/StudentHistory.tsx`
**Purpose**: Fetch real student history

**Action**: 
1. Call: `api.getStudentHistory(page)`
2. Remove: Mock data
3. Implement: Pagination

**Lines**: Change ~40 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ student/
              └─ StudentHistory.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/supervisor/SupervisorReview.tsx`
**Purpose**: Fetch supervisor queue & handle approvals

**Action**: 
1. Call: `api.getSupervisorQueue()`
2. On approve/reject: `api.submitSupervisorReview(id, action, comments)`
3. Remove: Mock data

**Lines**: Change ~50 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ supervisor/
              └─ SupervisorReview.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/supervisor/SupervisorHistory.tsx`
**Purpose**: Fetch supervisor's action history

**Action**: 
1. Call: `api.getSupervisorHistory()`
2. Remove: Mock data

**Lines**: Change ~20 lines  
**Priority**: 🟢 NICE-TO-HAVE

```
Frontend Root
  └─ src/
      └─ pages/
          └─ supervisor/
              └─ SupervisorHistory.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/librarian/LibrarianQueue.tsx`
**Purpose**: Fetch library queue & handle cataloging

**Action**: 
1. Call: `api.getLibraryQueue()`
2. On approve: `api.submitLibraryReview(id, 'approve')`
3. Remove: Mock data

**Lines**: Change ~50 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ librarian/
              └─ LibrarianQueue.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/librarian/LibrarianCatalogues.tsx`
**Purpose**: Edit document metadata & access levels

**Action**: 
1. On save: `api.updateMetadata(id, { accession_number, access_level, ... })`
2. Remove: Mock data

**Lines**: Change ~40 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ librarian/
              └─ LibrarianCatalogues.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/librarian/LibrarianReports.tsx`
**Purpose**: Fetch analytics & reports

**Action**: 
1. Call: `api.getLibrarianReports()`
2. Remove: Mock data

**Lines**: Change ~30 lines  
**Priority**: 🟢 NICE-TO-HAVE

```
Frontend Root
  └─ src/
      └─ pages/
          └─ librarian/
              └─ LibrarianReports.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/admin/AdminDashboard.tsx`
**Purpose**: Fetch admin stats

**Action**: 
1. Call: `api.getAdminDashboard()`
2. Remove: Mock data

**Lines**: Change ~40 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ admin/
              └─ AdminDashboard.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/admin/AdminUsers.tsx`
**Purpose**: Fetch users & manage roles

**Action**: 
1. Call: `api.getUsers(page)`
2. On role change: `api.updateUserRole(userId, newRole)`
3. On delete: `api.deleteUser(userId)`
4. Remove: Mock data

**Lines**: Change ~60 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ admin/
              └─ AdminUsers.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/admin/AdminSettings.tsx`
**Purpose**: Fetch & save system settings

**Action**: 
1. Load: `api.getSystemSettings()`
2. Save: `api.updateSystemSettings(settings)`
3. Remove: Mock data

**Lines**: Change ~40 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ admin/
              └─ AdminSettings.tsx (MODIFY)
```

### ✏️ MODIFY: `src/pages/admin/AdminBackups.tsx`
**Purpose**: Manage backups & restores

**Action**: 
1. Load: `api.getBackups(page)`
2. Create: `api.createBackup()`
3. Restore: `api.restoreBackup(id)`
4. Delete: `api.deleteBackup(id)`
5. Remove: Mock data

**Lines**: Change ~60 lines  
**Priority**: 🟡 IMPORTANT

```
Frontend Root
  └─ src/
      └─ pages/
          └─ admin/
              └─ AdminBackups.tsx (MODIFY)
```

### ✏️ CREATE: `.env.local`
**Purpose**: Backend API URL for development

**Action**: Create new file
```env
VITE_API_URL=http://localhost:8000
VITE_APP_NAME=URMS
VITE_APP_DEBUG=true
```

**Lines**: 3 lines  
**Priority**: 🔴 CRITICAL

```
Frontend Root
  └─ .env.local (NEW)
```

---

## Summary Table

| Priority | Type | Location | Action | Lines |
|----------|------|----------|--------|-------|
| 🔴 | CREATE | Backend | `app/Traits/ApiResponse.php` | ~80 |
| 🔴 | CREATE | Backend | `routes/api.php` | ~60 |
| 🔴 | MODIFY | Backend | `app/Http/Controllers/RepositoryController.php` | ~200 |
| 🟡 | MODIFY | Backend | `config/cors.php` | 1 |
| 🟡 | MODIFY | Backend | `.env` | 3 |
| 🔴 | CREATE | Frontend | `src/services/api.ts` | ~300 |
| 🔴 | CREATE | Frontend | `src/hooks/useApi.ts` | ~50 |
| 🔴 | MODIFY | Frontend | `src/context/RoleContext.tsx` | ~60 |
| 🟡 | MODIFY | Frontend | `src/pages/public/PublicSearch.tsx` | ~50 |
| 🟡 | MODIFY | Frontend | `src/pages/student/UploadWizard.tsx` | ~40 |
| 🟡 | MODIFY | Frontend | `src/pages/student/StudentDashboard.tsx` | ~30 |
| 🟡 | MODIFY | Frontend | `src/pages/student/StudentHistory.tsx` | ~40 |
| 🟡 | MODIFY | Frontend | `src/pages/supervisor/SupervisorReview.tsx` | ~50 |
| 🟢 | MODIFY | Frontend | `src/pages/supervisor/SupervisorHistory.tsx` | ~20 |
| 🟡 | MODIFY | Frontend | `src/pages/librarian/LibrarianQueue.tsx` | ~50 |
| 🟡 | MODIFY | Frontend | `src/pages/librarian/LibrarianCatalogues.tsx` | ~40 |
| 🟢 | MODIFY | Frontend | `src/pages/librarian/LibrarianReports.tsx` | ~30 |
| 🟡 | MODIFY | Frontend | `src/pages/admin/AdminDashboard.tsx` | ~40 |
| 🟡 | MODIFY | Frontend | `src/pages/admin/AdminUsers.tsx` | ~60 |
| 🟡 | MODIFY | Frontend | `src/pages/admin/AdminSettings.tsx` | ~40 |
| 🟡 | MODIFY | Frontend | `src/pages/admin/AdminBackups.tsx` | ~60 |
| 🔴 | CREATE | Frontend | `.env.local` | 3 |

---

## Implementation Order (Recommended)

### Phase 1: Backend Prep (Day 1)
1. Create `app/Traits/ApiResponse.php`
2. Create `routes/api.php`
3. Update `config/cors.php`
4. Update `.env`

### Phase 2: Backend API Methods (Day 1-2)
1. Update `RepositoryController` methods (15+ endpoints)
2. Test each endpoint with Postman

### Phase 3: Frontend Infrastructure (Day 2)
1. Create `src/services/api.ts`
2. Create `src/hooks/useApi.ts`
3. Create `.env.local`
4. Test API client with simple endpoint

### Phase 4: Frontend Authentication (Day 3)
1. Update `src/context/RoleContext.tsx`
2. Test login/logout flow

### Phase 5: Frontend Components (Day 3-4)
1. Update public pages (PublicSearch, DocumentDetail)
2. Update student pages (Dashboard, Upload, History)
3. Update supervisor pages (Review, History)
4. Update librarian pages (Queue, Catalogues, Reports)
5. Update admin pages (Dashboard, Users, Settings, Backups)

### Phase 6: Testing & Polish (Day 5)
1. End-to-end testing of all flows
2. Error handling & edge cases
3. Performance optimization
4. Security review

---

## File Dependencies

```
api.ts (HTTP client)
  ↓
RoleContext.tsx (uses api for auth)
  ↓
All Page Components (use api + RoleContext)
  ↓
useApi.ts hook (used by components)
```

---

## Critical Files (Must Do First)

🔴 **MUST CREATE** before anything else:
1. `app/Traits/ApiResponse.php` (Backend)
2. `src/services/api.ts` (Frontend)

🔴 **MUST MODIFY** second:
1. `RepositoryController.php` (Backend - use ApiResponse)
2. `RoleContext.tsx` (Frontend - use api service)

🔴 **MUST TEST** third:
1. One backend endpoint with Postman
2. One frontend component calling that endpoint

---

## Common Errors to Avoid

❌ Don't modify components before API layer is ready  
❌ Don't forget to use ApiResponse trait in controller  
❌ Don't forget CORS config before testing  
❌ Don't hardcode URLs - use .env variables  
❌ Don't forget credentials: "include" in api.ts  
❌ Don't forget to handle loading states in components  

---

## Validate Installation

### After creating each file:
```bash
# Backend: Check PHP syntax
php -l app/Traits/ApiResponse.php

# Frontend: Check TypeScript
npx tsc --noEmit src/services/api.ts
```

### After modifying each file:
```bash
# Backend: No compile errors
php artisan tinker

# Frontend: Component still renders
npm run dev
```

---

**Total Time**: ~80 hours of code (~20-30 files to touch)  
**Start Date**: Immediately  
**Estimated Completion**: 5 working days

