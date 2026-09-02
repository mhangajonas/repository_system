# URMS System Architecture Diagram

## System Overview

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          URMS COMPLETE SYSTEM                               │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│         FRONTEND (React + TypeScript)     │
│         http://localhost:5173             │
├──────────────────────────────────────────┤
│                                          │
│  ┌──────────────────────────────────┐   │
│  │  UI Components                   │   │
│  │  - PublicSearch                  │   │
│  │  - UploadWizard                  │   │
│  │  - StudentDashboard              │   │
│  │  - SupervisorReview              │   │
│  │  - LibrarianQueue                │   │
│  │  - AdminDashboard                │   │
│  └──────────────────────────────────┘   │
│           ↕ (uses)                      │
│  ┌──────────────────────────────────┐   │
│  │  API Client Layer                │   │
│  │  - src/services/api.ts           │   │
│  │  - src/hooks/useApi.ts           │   │
│  │  - RoleContext.tsx               │   │
│  └──────────────────────────────────┘   │
│           ↕ (HTTP calls)                │
│  ┌──────────────────────────────────┐   │
│  │  HTTP (JSON)                     │   │
│  │  - CORS enabled                  │   │
│  │  - Credentials: include          │   │
│  │  - CSRF token handled            │   │
│  └──────────────────────────────────┘   │
│                                          │
└──────────────────────────────────────────┘
                    ↨ HTTP/HTTPS
        ┌───────────────────────────────────┐
        │    Internet / Network              │
        └───────────────────────────────────┘
                    ↨
┌──────────────────────────────────────────────────────────────────┐
│         BACKEND (Laravel + PHP)                                  │
│         http://localhost:8000                                    │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  API Routing Layer (routes/api.php)                        │ │
│  │  ┌──────────────────┬──────────────────┬──────────────┐   │ │
│  │  │ Public Routes    │ Auth Routes      │ Admin Routes │   │ │
│  │  │ - Search         │ - Upload         │ - Users      │   │ │
│  │  │ - View Docs      │ - Student History│ - Settings   │   │ │
│  │  │ - Download       │ - Supervisor Rev │ - Backups    │   │ │
│  │  └──────────────────┴──────────────────┴──────────────┘   │ │
│  └────────────────────────────────────────────────────────────┘ │
│                         ↕                                        │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Controllers (app/Http/Controllers/)                       │ │
│  │  + ApiResponse Trait (JSON responses)                      │ │
│  │                                                            │ │
│  │  - RepositoryController                                   │ │
│  │  - ProfileController                                      │ │
│  └────────────────────────────────────────────────────────────┘ │
│                         ↕                                        │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Models & Relationships                                    │ │
│  │                                                            │ │
│  │  Repository ←→ User ←→ DownloadLog                        │ │
│  │                 ↓                                          │ │
│  │            ActivityLog                                    │ │
│  └────────────────────────────────────────────────────────────┘ │
│                         ↕                                        │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  Database Layer (MySQL/MariaDB)                            │ │
│  │                                                            │ │
│  │  Tables:                                                  │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │ │
│  │  │   users      │  │  repositories│  │download_logs │   │ │
│  │  ├──────────────┤  ├──────────────┤  ├──────────────┤   │ │
│  │  │ id (PK)      │  │ id (PK)      │  │ id (PK)      │   │ │
│  │  │ name         │  │ user_id (FK) │  │ repo_id (FK) │   │ │
│  │  │ email        │  │ title        │  │ user_id (FK) │   │ │
│  │  │ password     │  │ status       │  │ ip_address   │   │ │
│  │  │ role         │  │ access_level │  │ timestamp    │   │ │
│  │  │ department   │  │ file_path    │  └──────────────┘   │ │
│  │  │ reg_number   │  │ deleted_at   │                     │ │
│  │  └──────────────┘  └──────────────┘  ┌──────────────┐   │ │
│  │                                       │activity_logs │   │ │
│  │                                       ├──────────────┤   │ │
│  │                                       │ id (PK)      │   │ │
│  │                                       │ user_id (FK) │   │ │
│  │                                       │ action       │   │ │
│  │                                       │ description  │   │ │
│  │                                       │ created_at   │   │ │
│  │                                       └──────────────┘   │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  File Storage Layer                                        │ │
│  │  storage/app/public/documents/YYYY/{id}.pdf              │ │
│  │                                                            │ │
│  │  - 5-50MB PDFs                                            │ │
│  │  - Indexed by year                                        │ │
│  │  - Soft deletes for backup recovery                       │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│         AUTHENTICATION & SESSIONS                                │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  1. User Login                                                 │
│     Frontend: POST /login → {email, password}                 │
│                    ↓                                           │
│     Backend: Auth via Laravel's built-in Gate                 │
│                    ↓                                           │
│     Session: Store in HttpOnly cookie (CSRF protected)        │
│                    ↓                                           │
│     Frontend: Store in RoleContext via useEffect              │
│                                                                │
│  2. Authenticated Requests                                    │
│     Frontend: Fetch with credentials: "include"              │
│                    ↓                                           │
│     Backend: Middleware checks session & role                 │
│                    ↓                                           │
│     Response: JSON data (protected by CORS)                   │
│                                                                │
│  3. Logout                                                     │
│     Frontend: POST /logout                                    │
│                    ↓                                           │
│     Backend: Destroy session                                  │
│                    ↓                                           │
│     Frontend: Clear RoleContext                               │
│                                                                │
└──────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│         DATA FLOW: DOCUMENT UPLOAD EXAMPLE                       │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  FRONTEND                          BACKEND                      │
│  ─────────                          ───────                      │
│                                                                  │
│  1. User fills form                                             │
│     ↓                                                            │
│  2. User selects PDF                                            │
│     ↓                                                            │
│  3. Click "Submit"                                              │
│     ↓                                                            │
│  4. handleSubmit()                                              │
│     ├─ Create FormData                                          │
│     ├─ Call api.uploadDocument()                                │
│     └──→ POST /api/repositories ───────────────────────→        │
│                                            RepositoryController│
│                                            ├─ Validate input   │
│                                            ├─ Store PDF file   │
│                                            ├─ Generate accession│
│                                            ├─ Save to DB       │
│                                            ├─ Log action       │
│     ←───────────────────────────────────── └─ Return JSON      │
│  5. Parse response                                              │
│     ├─ Get accession_number                                     │
│     ├─ Get status: "pending_supervisor"                        │
│     └─ Get id                                                   │
│  6. Show success msg                                            │
│     ↓                                                            │
│  7. Redirect to dashboard                                       │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│         WORKFLOW: DOCUMENT APPROVAL PIPELINE                     │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  STUDENT                SUPERVISOR              LIBRARIAN       │
│  ───────────            ──────────              ─────────       │
│      │                      │                       │           │
│      ├─ Uploads             │                       │           │
│      │   Document           │                       │           │
│      │                      │                       │           │
│      ├─────────────────────→ Queue                  │           │
│      │                  "pending_supervisor"       │           │
│      │                      │                       │           │
│      │                      ├─ Reviews              │           │
│      │                      ├─ Approves/Rejects    │           │
│      │                      │  changes status       │           │
│      │                      │                       │           │
│      │                      ├──────────────────────→ Queue       │
│      │                                    "pending_library"    │
│      │                                         │                │
│      │                                    ├─ Catalogs         │
│      │                                    ├─ Sets access level│
│      │                                    ├─ Publishes        │
│      │                                         │                │
│      │                                         ├─ "approved"  │
│      │                                         │                │
│      │                                    Database updates       │
│      │                                    File moves to public  │
│      │                                    Activity log created   │
│      │                                                           │
│      └──── Notification Sent (optional)                         │
│           (on status changes)                                   │
│                                                                  │
│  DOCUMENT STATUS FLOW:                                         │
│  ┌─────────────────────────────────────────────────────────┐  │
│  │                                                         │  │
│  │  pending_supervisor → approved → pending_library       │  │
│  │        ↓                  ↓                    ↓        │  │
│  │   revision_requested  rejected          revision_     │  │
│  │                                           requested    │  │
│  │                                               ↓        │  │
│  │                                            approved    │  │
│  │                                                         │  │
│  └─────────────────────────────────────────────────────────┘  │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│         ROLE-BASED ACCESS CONTROL                                │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ROLE          CAN ACCESS                  ENDPOINTS            │
│  ────          ──────────                  ─────────            │
│                                                                  │
│  Student       - Own documents             POST /repositories  │
│                - Public/Institution docs   GET  /student/...   │
│                - Submit new docs           DELETE /...          │
│                                                                  │
│  Supervisor    - Assigned queue            GET  /supervisor/..│
│                - Own review history        POST /supervisor/..│
│                - Public/Institution docs                       │
│                                                                  │
│  Librarian     - Pending library queue     GET  /library/...  │
│                - Edit metadata             POST /library/...  │
│                - View reports              PUT  /library/...  │
│                - Download logs             GET  /reports, ...  │
│                                                                  │
│  Admin         - All endpoints              All /admin/...    │
│                - Manage users                                  │
│                - System settings                               │
│                - Backups & restore                             │
│                - View activity logs                            │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘

```

## Request/Response Cycle

```
┌─────────────────────────────────────────────────────────────────┐
│  HTTP REQUEST                            HTTP RESPONSE          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Frontend                                Backend                │
│  ────────                                ───────                │
│                                                                 │
│  const headers = {                       if (request.wantJson()│
│    'Content-Type': 'application/json'   {                      │
│    'Accept': 'application/json',        return response()      │
│    'X-CSRF-Token': token,               .json([               │
│    'X-Requested-With': 'XMLHttpRequest' 'success' => true,    │
│  }                                       'data' => [...],      │
│                                          'message' => '...'    │
│  fetch(url, {                           ])                    │
│    method: 'POST',                      }                     │
│    credentials: 'include',              else {                │
│    headers: headers,                    return view(...)      │
│    body: JSON.stringify(data)           }                     │
│  })                                                             │
│    .then(res => res.json())                                     │
│    .then(data => {                                              │
│      if (data.success) {                                        │
│        // Handle success                                        │
│        setData(data.data)                                       │
│      } else {                                                   │
│        // Handle error                                          │
│        setError(data.error)                                     │
│      }                                                          │
│    })                                                           │
│    .catch(err => {                                              │
│      // Network error                                           │
│      console.error(err)                                         │
│    })                                                           │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## File Upload Flow

```
┌──────────────────────────────────────────────────────────────────┐
│  FILE UPLOAD DETAILED PROCESS                                    │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Frontend                                Backend                 │
│  ────────                                │──────                 │
│                                          │                      │
│  1. User selects PDF                     │                      │
│     └─ file: File object                 │                      │
│                                          │                      │
│  2. Create FormData()                    │                      │
│     ├─ Title: "..."                      │                      │
│     ├─ Authors: "..."                    │                      │
│     ├─ Keywords: "k1,k2,k3"              │                      │
│     └─ File: Blob                        │                      │
│                                          │                      │
│  3. POST /api/repositories               │                      │
│     ├─ Content-Type: multipart/form-data │                      │
│     ├─ X-CSRF-Token: {token}             │                      │
│     └─ credentials: "include"            │                      │
│        ─────────────────────────────────→ RepositoryController  │
│                                          │ ::store()            │
│                                          │                      │
│                                          │ 4. Validate input    │
│                                          │    └─ PDF only       │
│                                          │    └─ Max 50MB       │
│                                          │                      │
│                                          │ 5. Store file        │
│                                          │    └─ Path: storage/ │
│                                          │       app/public/    │
│                                          │       documents/     │
│                                          │       2026/id.pdf    │
│                                          │                      │
│                                          │ 6. Generate accession│
│                                          │    └─ URMS/2026/0042 │
│                                          │                      │
│                                          │ 7. INSERT to DB      │
│                                          │    └─ repositories   │
│                                          │       table          │
│                                          │                      │
│                                          │ 8. Log action        │
│                                          │    └─ activity_logs  │
│                                          │                      │
│     ← ─────────────────────────────────  │ 9. Return JSON       │
│     {                                    │    {                 │
│       "success": true,                   │      "success": true,│
│       "data": {                          │      "data": {       │
│         "id": 42,                        │        "id": 42,     │
│         "accession_number":              │        "accession..":│
│           "URMS/2026/0042",              │          "URMS/2026..│
│         "status": "pending_supervisor"   │        "status": "...│
│       },                                 │      },              │
│       "message": "Document uploaded..."  │      "message": "..."│
│     }                                    │    }                 │
│                                          │                      │
│  10. Frontend receives response          │                      │
│      ├─ Extract accessionNo             │                      │
│      ├─ Show success toast               │                      │
│      └─ Redirect to /dashboard           │                      │
│                                          │                      │
│  11. Display in StudentDashboard        │                      │
│      └─ Status: "pending_supervisor"    │                      │
│                                          │                      │
└──────────────────────────────────────────────────────────────────┘
```

## Database Relationships

```
users (1) ──────── (Many) repositories
  │                        └─── (Many) download_logs
  │
  └──────────── (Many) activity_logs
       (logs all user actions)

repositories  (1) ──────── (Many) download_logs
    └─── (Soft Delete) tracked in activity_logs
    └─── (Status changes) logged in activity_logs
```

## Environment Setup

```
┌──────────────────────────────┬──────────────────────────────┐
│  BACKEND (.env)              │  FRONTEND (.env.local)       │
├──────────────────────────────┼──────────────────────────────┤
│  APP_NAME=URMS               │  VITE_API_URL=http://...    │
│  APP_ENV=local               │  VITE_APP_NAME=URMS         │
│  APP_URL=http://localhost... │                              │
│  DB_HOST=127.0.0.1           │                              │
│  DB_DATABASE=repository_sys  │                              │
│  FILESYSTEM_DISK=public      │                              │
│  CORS_ALLOWED_ORIGINS=...    │                              │
└──────────────────────────────┴──────────────────────────────┘
```

---

## Key Takeaways

1. **Frontend** (React) handles UI & sends HTTP requests
2. **Backend** (Laravel) processes requests & manages database
3. **Communication** happens via JSON over HTTP with CSRF protection
4. **Authentication** uses session cookies (HttpOnly + SameSite)
5. **Authorization** enforced via role-based middleware
6. **Data** transformed between frontend interfaces & backend models
7. **Files** stored in storage/ directory with soft deletes for audit
8. **Logging** tracks all actions in activity_logs table
9. **Error handling** returns structured JSON error responses
10. **Deployment** requires both services running & properly configured

