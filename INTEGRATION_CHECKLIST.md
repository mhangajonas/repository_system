# Frontend-Backend Coordination: Quick Reference Checklist

## PHASE 1: Backend Setup (1-2 days)

### Database & Migrations
- [ ] Run all migrations: `php artisan migrate`
- [ ] Verify tables exist: users, repositories, download_logs, activity_logs
- [ ] Check migrations folder for all required files
- [ ] Seed test data if needed: `php artisan db:seed`

### API Response Layer
- [ ] Create `app/Traits/ApiResponse.php` with success/error/paginate methods
- [ ] Add `use ApiResponse;` to RepositoryController
- [ ] Convert all controller methods to return JSON responses
- [ ] Test endpoints with Postman

### Configuration
- [ ] Update `config/cors.php` with frontend origins
- [ ] Create `routes/api.php` with all API endpoints
- [ ] Configure `config/filesystems.php` for document storage
- [ ] Verify `.env` has correct DB credentials & APP_URL

### Testing
- [ ] Test public search endpoint: GET /api/repositories
- [ ] Test upload endpoint: POST /api/repositories
- [ ] Test authentication flow
- [ ] Verify file storage path works

---

## PHASE 2: Frontend Setup (1-2 days)

### API Integration Layer
- [ ] Create `src/services/api.ts` with ApiClient class
- [ ] Create `src/hooks/useApi.ts` for data fetching
- [ ] Add `.env.local` with VITE_API_URL
- [ ] Test API client with simple GET request

### Authentication
- [ ] Update `src/context/RoleContext.tsx` to fetch real user
- [ ] Implement login function calling backend
- [ ] Implement logout function
- [ ] Store session/auth token properly

### Component Updates
- [ ] Update `src/pages/public/PublicSearch.tsx` to use api.searchDocuments()
- [ ] Update `src/pages/student/UploadWizard.tsx` to use api.uploadDocument()
- [ ] Update `src/pages/student/StudentDashboard.tsx` to fetch real data
- [ ] Update supervisor components to fetch real queue
- [ ] Update librarian components to fetch real queue
- [ ] Update admin dashboard to fetch real stats

### Testing
- [ ] Test public search (no auth required)
- [ ] Test login → fetch current user
- [ ] Test document upload
- [ ] Test supervisor review flow
- [ ] Test librarian actions

---

## PHASE 3: Integration & Testing (1-2 days)

### End-to-End Flows
- [ ] Public User: Search → View details → Request download
- [ ] Student: Login → Upload → View history → Delete
- [ ] Supervisor: View queue → Approve/Reject → View history
- [ ] Librarian: View queue → Approve → Edit metadata → View reports
- [ ] Admin: View users → Change role → View backups → Restore

### Error Handling
- [ ] Implement error boundaries in React
- [ ] Display validation errors from backend
- [ ] Handle network timeouts
- [ ] Handle permission/authorization errors

### Performance
- [ ] Implement pagination (don't load all at once)
- [ ] Add loading indicators
- [ ] Add caching where appropriate (React Query/SWR optional)
- [ ] Optimize images & assets

### Security
- [ ] Enable HTTPS (production)
- [ ] Verify CSRF protection working
- [ ] Test authorization on all protected routes
- [ ] Sanitize user input
- [ ] Rate limit API endpoints

---

## PHASE 4: Deployment (1 day)

### Backend
- [ ] Set up production database
- [ ] Set .env for production (APP_DEBUG=false, etc.)
- [ ] Run migrations on production
- [ ] Set up file backups
- [ ] Configure logging & monitoring

### Frontend
- [ ] Build: `npm run build`
- [ ] Deploy to hosting (Vercel, Netlify, or static server)
- [ ] Update API_URL in .env.production
- [ ] Test against production backend

### Post-Deployment
- [ ] Smoke test all main flows
- [ ] Monitor error logs
- [ ] Get user feedback
- [ ] Plan bug fixes & improvements

---

## DATA MAPPING REFERENCE

### Frontend Document ← → Backend Repository

```
Frontend                          Backend
────────────────────────────────────────────────
id                        ←→      id
accessionNo               ←→      accession_number
title                     ←→      title
authors (array)           ←→      authors (CSV string → split)
supervisor                ←→      supervisor
department                ←→      department
degree                    ←→      degree_programme
year                      ←→      year
type (DocType)            ←→      document_type
status (DocStatus)        ←→      status
accessLevel               ←→      access_level
keywords (array)          ←→      keywords (CSV string → split)
abstract                  ←→      abstract
fileSize (string)         ←→      calculated from file_path
uploadDate                ←→      created_at
downloads                 ←→      COUNT(download_logs)
supervisorComment         ←→      comments (when action='revision')
librarianNote             ←→      comments (when action='revision' from librarian)
```

### Frontend User ← → Backend User

```
Frontend                          Backend
────────────────────────────────────────────────
id                        ←→      id
name                      ←→      name
email                     ←→      email
regId                     ←→      reg_number
role                      ←→      role (student|supervisor|librarian|admin)
avatar                    ←→      (not in DB - use initials or gravatar)
joinDate                  ←→      created_at
```

---

## API ENDPOINT SUMMARY

### PUBLIC (No Auth)
| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/repositories` | Search all published docs |
| GET | `/api/repositories/{id}` | View single document |
| POST | `/api/repositories/{id}/download` | Download & log |

### STUDENT
| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/api/repositories` | Upload document |
| GET | `/api/student/history` | View submissions |
| DELETE | `/api/repositories/{id}` | Delete document |

### SUPERVISOR
| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/supervisor/review` | Queue to review |
| POST | `/api/supervisor/review/{id}` | Approve/Reject/Request Rev |
| GET | `/api/supervisor/history` | View actions taken |

### LIBRARIAN
| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/library/review` | Queue to catalog |
| POST | `/api/library/review/{id}` | Approve/Request revision |
| PUT | `/api/library/repository/{id}` | Edit metadata |
| GET | `/api/library/reports` | Analytics |
| GET | `/api/download-logs` | Download history |

### ADMIN
| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/admin/dashboard` | Overview stats |
| GET | `/api/admin/users` | List all users |
| PUT | `/api/admin/users/{id}/role` | Change role |
| DELETE | `/api/admin/users/{id}` | Delete user |
| GET\|POST | `/api/admin/settings` | App configuration |
| GET | `/api/admin/backups` | List backups |
| POST | `/api/admin/backups/create` | Create backup |
| POST | `/api/admin/backups/restore/{id}` | Restore deleted doc |
| DELETE | `/api/admin/backups/destroy/{id}` | Permanent delete |

---

## TESTING CHECKLIST

### Manual Testing
- [ ] Can I search documents as anonymous user?
- [ ] Can I view a public document?
- [ ] Can I login with correct credentials?
- [ ] Can I upload a PDF as student?
- [ ] Does upload generate accession number?
- [ ] Can supervisor see only their assigned docs?
- [ ] Can librarian approve & update metadata?
- [ ] Can admin see all users & change roles?
- [ ] Can admin restore deleted documents?
- [ ] Does download logging work?
- [ ] Are errors handled gracefully?

### API Testing (Postman)
```
📌 Pre-request Script:
Set CSRF token: pm.environment.set("csrf_token", $('meta[name="csrf-token"]').attr("content"))

📌 Tests:
Verify response has {success, data, message}
Verify pagination includes {current_page, total}
Verify errors include {success: false, error, errors}
```

### Performance Testing
- [ ] Search response < 1 second
- [ ] Upload with 50MB file succeeds
- [ ] List documents with 1000+ items (paginated)
- [ ] No N+1 queries in API responses

### Security Testing
- [ ] Student can't view other student's docs (non-published)
- [ ] Supervisor can't approve non-assigned docs
- [ ] Admin password can't be changed without auth
- [ ] File upload restricted to PDF only
- [ ] SQL injection attempts fail safely

---

## KEY FILES TO CREATE/MODIFY

### Create
```
Backend:
  app/Traits/ApiResponse.php
  routes/api.php

Frontend:
  src/services/api.ts
  src/hooks/useApi.ts
  .env.local
```

### Modify
```
Backend:
  app/Http/Controllers/RepositoryController.php
  config/cors.php
  config/filesystems.php

Frontend:
  src/context/RoleContext.tsx
  src/pages/public/PublicSearch.tsx
  src/pages/student/UploadWizard.tsx
  src/pages/student/StudentDashboard.tsx
  src/pages/supervisor/SupervisorReview.tsx
  src/pages/librarian/LibrarianQueue.tsx
  src/pages/admin/AdminDashboard.tsx
  src/pages/admin/AdminUsers.tsx
```

---

## TROUBLESHOOTING QUICK GUIDE

| Issue | Cause | Solution |
|-------|-------|----------|
| 404 Not Found | Wrong route path | Check `/api/` prefix in routes/api.php |
| 419 CSRF Error | Missing token | Add csrf-token to fetch headers |
| 401 Unauthorized | No session cookie | Add `credentials: "include"` to fetch |
| CORS Error | Origin not allowed | Add to config/cors.php allowed_origins |
| File not saving | Storage path wrong | Check config/filesystems.php 'public' disk |
| Data mismatch | Type conversion error | Check api.ts transforms data before return |
| Pagination null | Query not paginated | Use paginate(10) not all() in query |
| Upload fails | File size exceeded | Check MAX_FILE_SIZE env var |
| Auth stuck | Cache issue | Clear cookies & localStorage in browser |

---

## ENVIRONMENT VARIABLES

### Backend (.env)
```env
APP_NAME=URMS
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=repository_system
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000

MAIL_MAILER=log
NOTIFICATION_MAIL_FROM=no-reply@urms.local
```

### Frontend (.env.local)
```env
VITE_API_URL=http://localhost:8000
VITE_APP_NAME=URMS
VITE_APP_DEBUG=true
```

---

## SUCCESS CRITERIA

✅ Backend returns JSON for all API endpoints  
✅ Frontend can search & view documents  
✅ Student can upload document successfully  
✅ Supervisor can review & approve documents  
✅ Librarian can catalog & set access levels  
✅ Admin can manage users & backups  
✅ All role-based access control working  
✅ Notifications sent on status changes (optional)  
✅ Download logging works  
✅ Error messages clear & actionable  

---

## RESOURCES

- **Laravel API Development**: https://laravel.com/docs/11.x/eloquent-api-resources
- **React Data Fetching**: https://react.dev/learn/synchronizing-with-effects
- **REST API Best Practices**: https://restfulapi.net/
- **CORS Explained**: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
- **Postman Tutorial**: https://learning.postman.com/docs/getting-started/overview/

---

**Updated**: September 1, 2026  
**Status**: Ready for Implementation  
**Contact**: Development Team
