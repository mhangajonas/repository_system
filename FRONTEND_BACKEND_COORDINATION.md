# Frontend-Backend Coordination Guide
**URMS (University Repository Management System)**

---

## Overview
This document establishes clear coordination between the **React Frontend** (React v18 + TypeScript + Vite) and the **Laravel Backend** (Laravel 11 + PHP 8+).

**Frontend Location**: `/tmp/urms-react-version` (GitHub: Frankiexxiv/urms-react-version)  
**Backend Location**: `/opt/lampp/htdocs/repository_system` (Laravel Project)

---

## 1. SYSTEM ARCHITECTURE

### Frontend Stack
- **Framework**: React 18 + React Router 7
- **Language**: TypeScript
- **Styling**: Tailwind CSS
- **Package Manager**: pnpm/npm

### Backend Stack
- **Framework**: Laravel 11
- **Language**: PHP 8+
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Sanctum (via auth.php routes)
- **File Storage**: Local disk (storage/app/public/documents)

### Communication Protocol
- **Method**: HTTP/HTTPS
- **Format**: JSON
- **Authentication**: Session-based cookies + CSRF tokens (Laravel built-in)

---

## 2. DATA MODELS & INTERFACES

### Frontend Document Interface
```typescript
interface Document {
  id: string;
  accessionNo: string;
  title: string;
  authors: string[];
  supervisor: string;
  department: string;
  degree: string;
  year: number;
  type: DocType; // "Thesis" | "Dissertation" | "Research Paper" | "Past Exam"
  status: DocStatus; // "pending_supervisor" | "pending_library" | "approved" | "revision_requested" | "rejected"
  accessLevel: AccessLevel; // "open" | "institution" | "restricted"
  keywords: string[];
  abstract: string;
  fileSize: string;
  uploadDate: string;
  downloads: number;
  supervisorComment?: string;
  librarianNote?: string;
}

interface User {
  id: string;
  name: string;
  email: string;
  regId: string;
  role: string; // "student" | "admin" | "supervisor" | "librarian"
  avatar: string;
  joinDate: string;
}

interface AuditLog {
  id: string;
  userId: string;
  userName: string;
  actionType: "DELETE_DOCUMENT" | "SUPERVISOR_ACTION" | "RESTORE_ACTION" | "DELETE_USER" | "SYSTEM_CONFIG" | "BACKUP_CREATED";
  description: string;
  timestamp: string;
  recoverable: boolean;
  targetId?: string;
}
```

### Backend Repository Model
```php
class Repository extends Model {
    protected $fillable = [
        'user_id',
        'title',
        'abstract',
        'authors',
        'supervisor',
        'department',
        'year',
        'degree_programme',
        'keywords',
        'document_type',
        'file_path',
        'status', // pending_supervisor, pending_library, approved, revision_requested, rejected
        'access_level', // Open-Access, Institution-Only, Restricted-Access
        'accession_number',
        'comments',
    ];
    
    public function user() { return $this->belongsTo(User::class); }
    public function downloads() { return $this->hasMany(DownloadLog::class); }
}

class User extends Authenticatable {
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // admin, librarian, supervisor, student
        'department',
        'reg_number',
    ];
}

class ActivityLog extends Model {
    protected $fillable = [
        'user_id',
        'action', // DELETE_DOCUMENT, SUPERVISOR_ACTION, etc.
        'description',
    ];
}

class DownloadLog extends Model {
    protected $fillable = [
        'repository_id',
        'user_id',
        'ip_address',
        'user_agent',
        'downloader_name',
    ];
}
```

---

## 3. API ENDPOINTS MAPPING

### 3.1 PUBLIC ENDPOINTS (NO AUTH REQUIRED)

#### Search & Discover Documents (FR-3.1 to FR-3.5)
```
GET /
Route: public.search
Frontend Component: PublicSearch.tsx
Description: Search published documents with filters

Expected Parameters:
  ?query=string (search term)
  ?type=string (doc type filter)
  ?year=string (year filter)
  ?department=string (optional)

Backend Implementation: RepositoryController::search()
Returns: View with PUBLISHED documents (status='approved' & access_level in ['Open-Access', 'Institution-Only'])

Frontend Mock Data Source: DOCUMENTS.filter(d => d.status === "approved")
```

#### Retrieve Single Document
```
GET /repository/{id}
Route: repositories.show
Frontend Component: DocumentDetail.tsx
Description: Get full details of a specific document

Backend Implementation: RepositoryController::show($id)
Returns: Document detail view with:
  - Full metadata
  - Download links (if accessible)
  - Related documents
  - Citation formats
  - Download history (if admin)
```

#### Download Document
```
GET /repository/{id}/download
Route: repositories.download
Frontend Component: DocumentDetail.tsx (download button)
Description: Trigger file download & log the action

Backend Implementation: RepositoryController::download($id)
Actions:
  1. Verify access level (Open-Access, Institution-Only, or Restricted-Access)
  2. Create DownloadLog entry
  3. Stream PDF file
  4. Increment download counter

Response: Binary PDF stream
```

---

### 3.2 AUTHENTICATED ENDPOINTS (REQUIRES LOGIN)

#### Get Dashboard (Role-Based Redirect)
```
GET /dashboard
Route: dashboard
Middleware: auth, verified
Frontend Component: StudentDashboard.tsx (after login)
Description: Redirect to role-specific dashboard

Backend Implementation: RepositoryController::dashboard()
Logic:
  - If admin → redirect to admin.dashboard
  - If librarian → redirect to library.index
  - If supervisor → redirect to supervisor.index
  - If student → show student dashboard with pending & published docs

Frontend Expectation:
  - Fetches user data from session/context
  - Uses RoleContext to determine current user's role
```

#### Get User Profile
```
GET /profile
Route: profile.edit
Frontend Component: Profile settings (in app-wide layout)
Description: Retrieve authenticated user info

Backend Implementation: ProfileController::edit()
Returns: User profile data including:
  - name, email, role, department, reg_number
  - avatar, joinDate, etc.
```

#### Update User Profile
```
PATCH /profile
Route: profile.update
Description: Update user profile information

Backend Implementation: ProfileController::update(Request $request)
Expected Request Body:
  {
    "name": "string",
    "email": "string",
    "department": "string",
    "password": "string (optional)"
  }

Validation: email must be unique, password min 8 chars
```

#### Delete User Account
```
DELETE /profile
Route: profile.destroy
Description: Soft-delete user account

Backend Implementation: ProfileController::destroy()
```

---

### 3.3 STUDENT ROUTES

#### Create/Upload Document Form
```
GET /repositories/create
Route: repositories.create
Frontend Component: UploadWizard.tsx
Description: Display document upload wizard

Backend Implementation: RepositoryController::create()
Returns: Form view with:
  - Dropdown lists (departments, supervisors, degree programmes, doc types)
  - Form template
```

#### Store Uploaded Document
```
POST /repositories
Route: repositories.store
Frontend Component: UploadWizard.tsx (form submit)
Description: Save document metadata & file

Backend Implementation: RepositoryController::store(Request $request)

Request Body (multipart/form-data):
{
  "title": "string (required, max 255)",
  "abstract": "string (required)",
  "authors": "string (CSV: John Doe, Jane Smith)",
  "supervisor": "string (required, supervisor name)",
  "department": "string (required)",
  "year": "number (required)",
  "degree_programme": "string (required)",
  "keywords": "string (CSV: keyword1, keyword2)",
  "document_type": "Thesis|Dissertation|Research Paper|Past Exam",
  "file": "File (PDF only, max 50MB)"
}

Validation:
  - All fields required except keywords
  - PDF file only
  - Max 50MB file size

Response: 
  - Redirect to /dashboard with success message
  - Document status set to 'pending_supervisor'
  - Accession number auto-generated (URMS/YYYY/XXXX)

Database Changes:
  - INSERT into repositories table
  - File stored in storage/app/public/documents/{year}/{auto_id}.pdf
```

#### Retrieve Student's Submissions History
```
GET /student/history
Route: student.history
Frontend Component: StudentHistory.tsx
Description: Show all student's submitted documents

Backend Implementation: RepositoryController::studentHistory()
Returns: Paginated list of documents with status:
  - approved
  - revision_requested
  - rejected
  (Excludes pending_supervisor & pending_library - those are current submissions)

Frontend Display: Table with filters/sort by date, status
```

#### Delete Student's Document (Soft Delete)
```
DELETE /repositories/{id}
Route: repositories.destroy
Frontend Component: StudentHistory.tsx (delete button)
Description: Soft-delete a document

Backend Implementation: RepositoryController::studentDestroy($id)
Logic:
  - Verify user owns the document (or is admin)
  - Create ActivityLog: "DELETE_DOCUMENT"
  - Soft delete (mark in DB, file preserved)

Response: Redirect back with success message
```

---

### 3.4 INSTITUTIONAL REPOSITORY (ALL AUTHENTICATED USERS)

#### Published Works Catalogue
```
GET /institutional-repository
Route: repositories.published
Frontend Component: Not in current React code (needs implementation)
Description: Show all published documents accessible to institution

Backend Implementation: RepositoryController::publishedWorks()
Access Logic:
  - Student/Supervisor: Can see approved documents with access_level in ['Open-Access', 'Institution-Only']
  - Librarian/Admin: Can see all approved + pending_library documents

Database Query:
  SELECT * FROM repositories 
  WHERE status='approved' AND access_level IN ('Open-Access', 'Institution-Only')
  ORDER BY created_at DESC
```

---

### 3.5 SUPERVISOR ROUTES (Middleware: CheckSupervisor)

#### Supervisor Review Queue
```
GET /supervisor/review
Route: supervisor.index
Frontend Component: SupervisorReview.tsx
Description: List documents awaiting this supervisor's review

Backend Implementation: RepositoryController::supervisorIndex()
Filters: 
  - status = 'pending_supervisor'
  - supervisor name matches logged-in supervisor
  
Returns:
  [
    {
      id, title, authors, abstract, department,
      year, type, fileSize, uploadDate,
      status: "pending_supervisor",
      student_name, student_email
    },
    ...
  ]

Frontend Expected:
  - Table with document list
  - Action buttons: Approve, Request Revision, Reject
  - Comments field for feedback
```

#### Supervisor Action on Document
```
POST /supervisor/review/{id}
Route: supervisor.action
Frontend Component: SupervisorReview.tsx (action button click)
Description: Approve, request revision, or reject document

Backend Implementation: RepositoryController::supervisorAction(Request $request, $id)

Request Body:
{
  "action": "approve" | "revision" | "reject",
  "comments": "string (optional, max 1000)"
}

Actions:
  "approve" → status = "pending_library"
  "revision" → status = "revision_requested"
  "reject" → status = "rejected"

Side Effects:
  1. Update repository.status & repository.comments
  2. Send notification to student via DocumentStatusUpdated
  3. Log action in activity_logs table
```

#### Supervisor Document History
```
GET /supervisor/history
Route: supervisor.history
Frontend Component: SupervisorHistory.tsx
Description: All documents this supervisor has reviewed (with soft-deleted ones)

Backend Implementation: RepositoryController::supervisorHistory()
Query: Include soft-deleted documents using withTrashed()
Returns: Paginated list of supervisor's reviews
```

#### Supervisor Delete from Backup
```
DELETE /supervisor/backups/destroy/{id}
Route: supervisor.backups.destroy
Description: Supervisor can delete soft-deleted backup copies

Backend Implementation: RepositoryController::destroyFromLog($id)
```

---

### 3.6 LIBRARIAN ROUTES (Middleware: CheckLibrarian)

#### Library Review Queue
```
GET /library/review
Route: library.index
Frontend Component: LibrarianQueue.tsx
Description: Documents pending librarian cataloging

Backend Implementation: RepositoryController::libraryIndex()
Filters: status = 'pending_library'
Returns: List of documents with student info, supervisor notes
```

#### Librarian Action
```
POST /library/review/{id}
Route: library.action
Frontend Component: LibrarianQueue.tsx
Description: Approve or request revision at library stage

Backend Implementation: RepositoryController::libraryAction(Request $request, $id)

Request Body:
{
  "action": "approve" | "revision",
  "comments": "string (optional)"
}

Actions:
  "approve" → status = "approved", call ActivityLog
  "revision" → status = "revision_requested"
```

#### Edit Document Metadata
```
GET /library/repository/{id}/edit
Route: library.repositories.edit
Frontend Component: LibrarianCatalogues.tsx (edit dialog)
Description: Form to edit cataloging metadata

Backend Implementation: RepositoryController::editMetadata($id)
```

#### Update Metadata & Set Access Level
```
PUT /library/repository/{id}
Route: library.repositories.update
Frontend Component: LibrarianCatalogues.tsx (form submit)
Description: Save updated metadata & access level

Backend Implementation: RepositoryController::updateMetadata(Request $request, $id)

Request Body:
{
  "title": "string",
  "abstract": "string",
  "keywords": "string (CSV)",
  "accession_number": "string (URMS/YYYY/XXXX format)",
  "access_level": "Open-Access" | "Institution-Only" | "Restricted-Access"
}

Database: UPDATE repositories SET ... WHERE id=$id
```

#### Manage Catalogues
```
GET /library/catalogues
Route: library.catalogues
Frontend Component: LibrarianCatalogues.tsx
Description: View & manage all catalogued documents

Backend Implementation: RepositoryController::manageCatalogues()
Returns: All approved documents with full metadata & statistics
```

#### Analytical Dashboard & Reports
```
GET /library/reports
Route: library.reports
Frontend Component: LibrarianReports.tsx
Description: Generate analytics & reports

Backend Implementation: RepositoryController::analytics()
Metrics:
  - Total documents approved this month/year
  - Most-downloaded documents
  - Downloads by department
  - Approval times (avg, min, max)
  - Documents by access level
  
Returns: JSON with statistical data for charting
```

#### Download Logs
```
GET /download-logs
Route: download.logs
Frontend Component: (not in React code yet - needs implementation)
Description: View download history & analytics

Backend Implementation: RepositoryController::downloadLogs()
Returns: Paginated list from download_logs table
Columns: repository_id, user_id, ip_address, downloader_name, timestamp
```

---

### 3.7 ADMIN ROUTES (Middleware: CheckAdmin)

#### Admin Dashboard
```
GET /admin/dashboard
Route: admin.dashboard
Frontend Component: AdminDashboard.tsx
Description: System overview & admin controls

Backend Implementation: RepositoryController::adminDashboard()
Returns:
{
  "total_users": number,
  "total_documents": number,
  "pending_reviews": number,
  "total_downloads": number,
  "users_by_role": { admin, librarian, supervisor, student },
  "documents_by_status": { approved, pending_supervisor, pending_library, revision_requested, rejected },
  "recent_activity": [...ActivityLogs],
  "system_settings": {...current_settings}
}
```

#### Manage Users - List
```
GET /admin/users
Route: admin.users.index
Frontend Component: AdminUsers.tsx
Description: List all system users

Backend Implementation: RepositoryController::usersIndex()
Returns: Paginated list of users with:
  - id, name, email, role, department, reg_number
  - created_at, last_login, status
```

#### Update User Role
```
PUT /admin/users/{id}/role
Route: admin.users.updateRole
Frontend Component: AdminUsers.tsx (role dropdown)
Description: Change user's role

Backend Implementation: RepositoryController::updateRoleByAdmin(Request $request, $id)

Request Body:
{
  "role": "student" | "supervisor" | "librarian" | "admin"
}

Validation: Role must be one of allowed values
Database: UPDATE users SET role=$role WHERE id=$id
Log: ActivityLog entry creating for audit trail
```

#### Delete User
```
DELETE /admin/users/{id}
Route: admin.users.delete
Frontend Component: AdminUsers.tsx (delete button)
Description: Soft-delete user account

Backend Implementation: RepositoryController::deleteUser($id)
Logic:
  - Check user is not the last admin
  - Soft delete user
  - Keep associated documents & logs for audit
```

#### System Settings
```
GET /admin/settings
Route: admin.settings
Frontend Component: AdminSettings.tsx
Description: Retrieve current system configuration

Backend Implementation: RepositoryController::systemSettings()
Returns:
{
  "institution_name": "string",
  "max_file_size_mb": number,
  "allowed_doc_types": [...],
  "accessLevels": [...],
  "email_notifications_enabled": boolean,
  "backup_frequency": "daily|weekly|monthly"
}

Note: Settings likely stored in .env or database config table
```

#### Save System Settings
```
POST /admin/settings
POST /admin/settings/save
POST /admin/settings/update
Route: admin.settings, admin.settings.save, admin.save_settings
Frontend Component: AdminSettings.tsx (form submit)
Description: Update system configuration

Backend Implementation: RepositoryController::saveSettings(Request $request)

Request Body:
{
  "institution_name": "string",
  "max_file_size_mb": number,
  "backup_frequency": "daily|weekly|monthly",
  "email_notifications_enabled": boolean
}

Note: Multiple route aliases exist - standardize on single endpoint
```

#### Backup Management - List
```
GET /admin/backups
Route: admin.backups
Frontend Component: AdminBackups.tsx
Description: View available backups

Backend Implementation: RepositoryController::manageBackups()
Returns: List from activity_logs where action includes DELETE
Columns:
  - id, user_id, action, description, timestamp
  - targetId (soft-deleted document id)
  - recoverable (boolean)
```

#### Create Backup
```
POST /admin/backups/create
POST /admin/backups/generate
Route: admin.create_backup, admin.backups.create
Frontend Component: AdminBackups.tsx (Create button)
Description: Trigger manual backup

Backend Implementation: RepositoryController::createBackup()
Actions:
  1. Export all repositories to SQL dump
  2. Zip all files in storage/app/public/documents
  3. Store backup metadata in activity_logs or backups table
  4. Return backup_id & timestamp

Note: Multiple route aliases - standardize on one
```

#### Restore from Backup
```
POST /admin/backups/restore/{id}
Route: admin.backups.restore
Frontend Component: AdminBackups.tsx (Restore button)
Description: Restore soft-deleted document

Backend Implementation: RepositoryController::restoreFromLog($id)
Logic:
  1. Find soft-deleted repository by id
  2. Call restore() (Eloquent SoftDeletes)
  3. Update status if needed
  4. Create ActivityLog: "RESTORE_ACTION"
```

#### Destroy Backup Permanently
```
DELETE /admin/backups/destroy/{id}
Route: admin.backups.destroy
Frontend Component: AdminBackups.tsx (Permanent Delete)
Description: Permanently delete soft-deleted document

Backend Implementation: RepositoryController::destroyFromLog($id)
Logic:
  1. Find soft-deleted repository
  2. forceDelete() (Eloquent hard delete)
  3. Delete associated file from storage
  4. Create ActivityLog: "DELETE_DOCUMENT" (permanent)
```

---

## 4. COMMUNICATION REQUIREMENTS

### 4.1 Request Headers (Frontend → Backend)

```javascript
// Standard headers frontend should send:
{
  "Content-Type": "application/json", // or multipart/form-data for file uploads
  "Accept": "application/json",
  "X-CSRF-Token": "{csrf_token}", // From Laravel meta tag in HTML
  "X-Requested-With": "XMLHttpRequest"
}

// Cookies auto-sent (session cookie, CSRF token)
```

### 4.2 Response Format (Backend → Frontend)

**Success Response (2xx)**
```json
{
  "success": true,
  "data": { /* resource data */ },
  "message": "Operation completed successfully"
}
```

**Error Response (4xx/5xx)**
```json
{
  "success": false,
  "error": "Error message",
  "errors": {
    "field_name": ["validation error message"],
    "another_field": ["error 1", "error 2"]
  },
  "status": 400
}
```

### 4.3 Pagination Format

```json
{
  "data": [...],
  "current_page": 1,
  "per_page": 10,
  "total": 150,
  "last_page": 15,
  "from": 1,
  "to": 10,
  "links": {
    "first": "...",
    "last": "...",
    "next": "...",
    "prev": null
  }
}
```

---

## 5. AUTHENTICATION FLOW

### Login Process
1. Frontend POST to `/login` (Laravel auth.php) with email/password
2. Backend validates & creates session
3. Backend returns user data + CSRF token
4. Frontend stores in React context (RoleContext)
5. All subsequent requests include session cookie

### Session Management
- **Session Driver**: Laravel config/session.php (likely 'cookie' or 'database')
- **CSRF Token**: Get from `<meta name="csrf-token">` in initial page load
- **Token Refresh**: Can be refreshed per-request or per-session

### Logout Process
1. Frontend POST to `/logout`
2. Backend destroys session
3. Frontend clears React context
4. Frontend redirects to login page

---

## 6. MISSING IMPLEMENTATIONS (Frontend ↔ Backend Gaps)

### Frontend Missing API Service Layer
**File Needed**: `src/services/api.ts`
```typescript
// API client with base URL, auth handling, error handling
export const api = {
  get(path: string) { },
  post(path: string, data: any) { },
  put(path: string, data: any) { },
  delete(path: string) { },
  upload(path: string, formData: FormData) { }
};
```

### Frontend Missing hooks
**File Needed**: `src/hooks/useApi.ts`
```typescript
// Fetch data & handle loading/error states
export const useApi = (path: string) => {
  // return { data, loading, error, refetch }
};
```

### Frontend Missing context API handlers
**Update File**: `src/context/RoleContext.tsx`
- Store real user data from backend
- Handle login/logout
- Store authentication token

### Backend Missing API Response Standardization
**Current**: Returns views (HTML)
**Needed**: Return JSON for React frontend
- Update RepositoryController to check `Accept: application/json`
- Return JSON instead of view() where appropriate
- Use Laravel's response()->json() or jsonify responses

### Backend Missing CORS Configuration
**File Needed** or **Update**: `config/cors.php`
```php
'allowed_origins' => ['http://localhost:5173', 'http://localhost:3000'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

### Backend Missing API Routes
**Option 1**: Create separate `routes/api.php` for JSON endpoints
**Option 2**: Update `routes/web.php` to detect Content-Type and return JSON

### Data Transformation Layer
**Need**: Transform between Frontend interfaces and Backend models
- Frontend `Document` → Backend `Repository`
- Frontend `accessLevel: "open"` → Backend `access_level: "Open-Access"`

---

## 7. DATABASE STATE EXPECTED

### Current Status
Users table must have:
- id, name, email, password, role, department, reg_number, created_at, updated_at

Repositories table must have:
- id, user_id, title, abstract, authors, supervisor, department, year, degree_programme
- keywords, document_type, file_path, status, access_level, accession_number, comments
- created_at, updated_at, deleted_at (soft deletes)

DownloadLogs table must track:
- id, repository_id, user_id, ip_address, user_agent, downloader_name, created_at

ActivityLogs table must track:
- id, user_id, action, description, created_at

---

## 8. INTEGRATION CHECKLIST

### Backend Tasks
- [ ] Create/update `routes/api.php` with JSON endpoints (or modify web.php)
- [ ] Update RepositoryController methods to return JSON responses
- [ ] Implement response standardization across all endpoints
- [ ] Add CORS configuration if frontend runs on different origin
- [ ] Verify database migrations are all applied
- [ ] Test all endpoints with Postman/Insomnia

### Frontend Tasks
- [ ] Create `src/services/api.ts` (HTTP client with auth)
- [ ] Create `src/hooks/useApi.ts` (data fetching hook)
- [ ] Update `src/context/RoleContext.tsx` to fetch real user data
- [ ] Replace mock data imports with API calls
- [ ] Update all page components (PublicSearch, StudentDashboard, etc.) to use real data
- [ ] Add form submission handlers to post data to backend
- [ ] Implement error handling & loading states
- [ ] Add authentication UI (login/logout)
- [ ] Test integration end-to-end

### Deployment
- [ ] Configure CORS for production domains
- [ ] Set up HTTPS
- [ ] Configure database backups
- [ ] Set up monitoring & logging
- [ ] Document API for future developers

---

## 9. EXAMPLE FLOW: STUDENT UPLOADS DOCUMENT

### Frontend Flow
1. User lands on `/repositories/create` (UploadWizard.tsx)
2. Form inputs: title, authors, supervisor, department, year, degree, type, keywords, abstract, file
3. User submits form → handleSubmit() calls API
4. Frontend POST to `/repositories` with FormData
5. Show upload progress & success message
6. Redirect to `/dashboard`

### Backend Flow
1. POST /repositories → RepositoryController::store()
2. Validate all inputs
3. Store PDF file → storage/app/public/documents/yyyy/id.pdf
4. Generate accession_number (URMS/2026/0042)
5. INSERT into repositories (status='pending_supervisor')
6. Log action in activity_logs
7. Send notification to assigned supervisor
8. Return success response + document ID

### Frontend Receives
```json
{
  "success": true,
  "data": {
    "id": 42,
    "accession_number": "URMS/2026/0042",
    "status": "pending_supervisor",
    "message": "Document submitted successfully"
  }
}
```

---

## 10. NEXT STEPS

### Immediate (Week 1)
1. Backend: Create JSON API response layer
2. Frontend: Build API service layer
3. Frontend: Create authentication context
4. Test login flow end-to-end

### Short Term (Week 2-3)
1. Integrate public search endpoint
2. Integrate student submission flow
3. Integrate supervisor review flow
4. Implement real-time notifications

### Medium Term (Month 1)
1. Integrate librarian cataloging
2. Integrate admin user management
3. Implement backup/restore functionality
4. Add advanced analytics/reports

### Long Term
1. Performance optimization
2. Caching strategies
3. Advanced search (Elasticsearch)
4. API versioning & documentation

---

## Notes
- All file paths are relative to Laravel public directory for asset serving
- Document PDFs stored with soft deletes for audit trail
- All modifications logged in activity_logs table
- Frontend stored in separate GitHub repo - coordinate with version control
- Use environment variables for API base URL (different per environment)

---

**Document Version**: 1.0  
**Last Updated**: 2026-09-01  
**Maintained By**: Development Team
