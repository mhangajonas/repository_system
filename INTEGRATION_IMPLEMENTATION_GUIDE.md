# Frontend-Backend Integration: Implementation Guide
**URMS System - Practical Code Examples**

---

## PART A: BACKEND - JSON RESPONSE SETUP

### A.1 Create API Response Trait (Optional but Recommended)

**File**: `app/Traits/ApiResponse.php`

```php
<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponse
{
    /**
     * Success response
     */
    protected function success($data = null, $message = 'Operation successful', $status = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    /**
     * Error response
     */
    protected function error($message = 'Operation failed', $errors = null, $status = 400)
    {
        $response = [
            'success' => false,
            'error' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Paginated response
     */
    protected function paginate($paginator, $message = 'Data retrieved successfully')
    {
        return response()->json([
            'success' => true,
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'message' => $message,
        ]);
    }
}
```

### A.2 Update RepositoryController to Return JSON

**File**: `app/Http/Controllers/RepositoryController.php` (add at top)

```php
<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse; // Add this
use Illuminate\Http\Request;
// ... other imports

class RepositoryController extends Controller
{
    use ApiResponse; // Add this
    
    // ... existing methods modified below
}
```

### A.3 Example Endpoint Modifications

#### GET /api/repositories (Public Search)
```php
public function search(Request $request)
{
    $query = $request->input('query');
    $type = $request->input('type');
    $year = $request->input('year');
    $department = $request->input('department');

    $documents = Repository::where('status', 'approved')
        ->whereIn('access_level', ['Open-Access', 'Institution-Only'])
        ->when($query, function($q) use ($query) {
            return $q->where('title', 'like', "%$query%")
                     ->orWhereRaw('FIND_IN_SET(?, keywords)', [$query]);
        })
        ->when($type, function($q) use ($type) {
            return $q->where('document_type', $type);
        })
        ->when($year, function($q) use ($year) {
            return $q->where('year', $year);
        })
        ->when($department, function($q) use ($department) {
            return $q->where('department', $department);
        })
        ->paginate(10);

    return $this->paginate($documents, 'Published documents retrieved');
}
```

#### POST /api/repositories (Create Document)
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'abstract' => 'required|string',
        'authors' => 'required|string',
        'supervisor' => 'required|string',
        'department' => 'required|string',
        'year' => 'required|integer|min:2000|max:' . date('Y'),
        'degree_programme' => 'required|string',
        'keywords' => 'required|string',
        'document_type' => 'required|in:Thesis,Dissertation,Research Paper,Past Exam',
        'file' => 'required|mimes:pdf|max:51200',
    ]);

    try {
        $filePath = $request->file('file')->store('documents/' . date('Y'), 'public');

        // Generate accession number
        $year = date('Y');
        $count = Repository::whereYear('created_at', $year)->count();
        $accessionNumber = "URMS/{$year}/" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        $repository = Repository::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'authors' => $validated['authors'],
            'supervisor' => $validated['supervisor'],
            'department' => $validated['department'],
            'year' => $validated['year'],
            'degree_programme' => $validated['degree_programme'],
            'keywords' => $validated['keywords'],
            'document_type' => $validated['document_type'],
            'file_path' => $filePath,
            'accession_number' => $accessionNumber,
            'status' => 'pending_supervisor',
        ]);

        // Send notification to supervisor
        // $supervisor = User::where('name', 'like', "%{$validated['supervisor']}%")->first();
        // if ($supervisor) {
        //     $supervisor->notify(new DocumentStatusUpdated($repository));
        // }

        return $this->success([
            'id' => $repository->id,
            'accession_number' => $accessionNumber,
            'status' => 'pending_supervisor',
        ], 'Document uploaded successfully', 201);

    } catch (\Exception $e) {
        return $this->error('Failed to upload document: ' . $e->getMessage(), null, 500);
    }
}
```

#### GET /api/repositories/{id} (View Document)
```php
public function show($id)
{
    $repository = Repository::with('user', 'downloads')->findOrFail($id);

    // Check access
    if ($repository->status !== 'approved') {
        if (!Auth::check() || Auth::id() !== $repository->user_id) {
            abort(403, 'Access denied');
        }
    }

    return $this->success([
        'id' => $repository->id,
        'accession_number' => $repository->accession_number,
        'title' => $repository->title,
        'authors' => explode(',', $repository->authors),
        'supervisor' => $repository->supervisor,
        'department' => $repository->department,
        'year' => $repository->year,
        'degree_programme' => $repository->degree_programme,
        'keywords' => explode(',', $repository->keywords),
        'document_type' => $repository->document_type,
        'status' => $repository->status,
        'access_level' => $repository->access_level,
        'abstract' => $repository->abstract,
        'comments' => $repository->comments,
        'uploaded_by' => $repository->user->name,
        'uploaded_date' => $repository->created_at->toDateString(),
        'downloads' => $repository->downloads_count ?? 0,
    ], 'Document retrieved successfully');
}
```

#### POST /api/repositories/{id}/download (Log & Serve Download)
```php
public function download($id)
{
    $repository = Repository::findOrFail($id);

    // Verify access
    if ($repository->access_level === 'Restricted-Access' && !Auth::check()) {
        return $this->error('Authentication required', null, 401);
    }
    if ($repository->status !== 'approved' && (!Auth::check() || Auth::id() !== $repository->user_id)) {
        return $this->error('Access denied', null, 403);
    }

    try {
        // Log the download
        DownloadLog::create([
            'repository_id' => $repository->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'downloader_name' => Auth::user()?->name ?? 'Anonymous',
        ]);

        // Stream file
        return Storage::disk('public')->download($repository->file_path);

    } catch (\Exception $e) {
        return $this->error('Download failed: ' . $e->getMessage(), null, 500);
    }
}
```

#### POST /api/supervisor/review/{id} (Supervisor Action)
```php
public function supervisorAction(Request $request, $id)
{
    $validated = $request->validate([
        'action' => 'required|in:approve,revision,reject',
        'comments' => 'nullable|string|max:1000',
    ]);

    $repository = Repository::findOrFail($id);
    $user = Auth::user();

    // Verify supervisor assignment
    if (!str_contains($repository->supervisor, $user->name)) {
        return $this->error('Unauthorized', null, 403);
    }

    try {
        $statusMap = [
            'approve' => 'pending_library',
            'revision' => 'revision_requested',
            'reject' => 'rejected',
        ];

        $repository->update([
            'status' => $statusMap[$validated['action']],
            'comments' => $validated['comments'],
        ]);

        // Log action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'SUPERVISOR_ACTION',
            'description' => "{$user->name} ({$validated['action']}) document: {$repository->title}",
        ]);

        // Send notification (optional)
        // $repository->user->notify(new DocumentStatusUpdated($repository));

        return $this->success([
            'id' => $repository->id,
            'status' => $repository->status,
        ], "Document $validated[action]ed successfully");

    } catch (\Exception $e) {
        return $this->error('Action failed: ' . $e->getMessage(), null, 500);
    }
}
```

---

## PART B: FRONTEND - API CLIENT & INTEGRATION

### B.1 Create API Service Layer

**File**: `src/services/api.ts`

```typescript
import { Document, User } from "@/data/mockData";

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000";

export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  message?: string;
  error?: string;
  errors?: Record<string, string[]>;
}

export interface PaginatedResponse<T> {
  success: boolean;
  data: T[];
  pagination: {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
    from: number;
    to: number;
  };
  message?: string;
}

class ApiClient {
  private baseUrl: string;

  constructor(baseUrl: string = API_BASE_URL) {
    this.baseUrl = baseUrl;
  }

  private getHeaders(includeContentType = true): HeadersInit {
    const headers: HeadersInit = {
      Accept: "application/json",
    };

    if (includeContentType) {
      headers["Content-Type"] = "application/json";
    }

    // Add CSRF token if available
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
    if (csrfToken) {
      headers["X-CSRF-Token"] = csrfToken;
    }

    return headers;
  }

  async request<T>(
    endpoint: string,
    method: "GET" | "POST" | "PUT" | "DELETE" = "GET",
    data?: any,
    isFormData = false
  ): Promise<ApiResponse<T>> {
    const url = `${this.baseUrl}${endpoint}`;
    const options: RequestInit = {
      method,
      credentials: "include", // Include cookies for session auth
      headers: this.getHeaders(!isFormData),
    };

    if (data && (method === "POST" || method === "PUT")) {
      if (isFormData) {
        options.body = data; // FormData
      } else {
        options.body = JSON.stringify(data);
      }
    }

    try {
      const response = await fetch(url, options);
      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.error || `HTTP ${response.status}`);
      }

      return result;
    } catch (error) {
      console.error(`API Error [${method} ${endpoint}]:`, error);
      throw error;
    }
  }

  // === PUBLIC ENDPOINTS ===
  
  async searchDocuments(
    query?: string,
    type?: string,
    year?: string,
    department?: string,
    page = 1
  ): Promise<PaginatedResponse<Document>> {
    const params = new URLSearchParams({
      ...(query && { query }),
      ...(type && type !== "All Types" && { type }),
      ...(year && year !== "All Years" && { year }),
      ...(department && { department }),
      page: page.toString(),
    });

    return this.request(`/api/repositories?${params}`);
  }

  async getDocument(id: string): Promise<ApiResponse<Document>> {
    return this.request(`/api/repositories/${id}`);
  }

  async downloadDocument(id: string): Promise<Response> {
    const response = await fetch(
      `${this.baseUrl}/api/repositories/${id}/download`,
      {
        credentials: "include",
      }
    );
    if (!response.ok) throw new Error("Download failed");
    return response;
  }

  // === STUDENT ENDPOINTS ===

  async uploadDocument(
    formData: FormData
  ): Promise<ApiResponse<{ id: number; accession_number: string; status: string }>> {
    return this.request("/api/repositories", "POST", formData, true);
  }

  async getStudentHistory(page = 1): Promise<PaginatedResponse<Document>> {
    return this.request(`/api/student/history?page=${page}`);
  }

  async deleteDocument(id: number): Promise<ApiResponse<null>> {
    return this.request(`/api/repositories/${id}`, "DELETE");
  }

  // === SUPERVISOR ENDPOINTS ===

  async getSupervisorQueue(page = 1): Promise<PaginatedResponse<Document>> {
    return this.request(`/api/supervisor/review?page=${page}`);
  }

  async submitSupervisorReview(
    id: number,
    action: "approve" | "revision" | "reject",
    comments?: string
  ): Promise<ApiResponse<{ id: number; status: string }>> {
    return this.request(`/api/supervisor/review/${id}`, "POST", {
      action,
      comments,
    });
  }

  async getSupervisorHistory(page = 1): Promise<PaginatedResponse<Document>> {
    return this.request(`/api/supervisor/history?page=${page}`);
  }

  // === LIBRARIAN ENDPOINTS ===

  async getLibraryQueue(page = 1): Promise<PaginatedResponse<Document>> {
    return this.request(`/api/library/review?page=${page}`);
  }

  async submitLibraryReview(
    id: number,
    action: "approve" | "revision",
    comments?: string
  ): Promise<ApiResponse<{ id: number; status: string }>> {
    return this.request(`/api/library/review/${id}`, "POST", {
      action,
      comments,
    });
  }

  async updateMetadata(
    id: number,
    metadata: any
  ): Promise<ApiResponse<Document>> {
    return this.request(`/api/library/repository/${id}`, "PUT", metadata);
  }

  async getLibrarianReports(): Promise<ApiResponse<any>> {
    return this.request(`/api/library/reports`);
  }

  async getDownloadLogs(page = 1): Promise<PaginatedResponse<any>> {
    return this.request(`/api/download-logs?page=${page}`);
  }

  // === ADMIN ENDPOINTS ===

  async getAdminDashboard(): Promise<ApiResponse<any>> {
    return this.request(`/api/admin/dashboard`);
  }

  async getUsers(page = 1): Promise<PaginatedResponse<User>> {
    return this.request(`/api/admin/users?page=${page}`);
  }

  async updateUserRole(userId: number, role: string): Promise<ApiResponse<User>> {
    return this.request(`/api/admin/users/${userId}/role`, "PUT", { role });
  }

  async deleteUser(userId: number): Promise<ApiResponse<null>> {
    return this.request(`/api/admin/users/${userId}`, "DELETE");
  }

  async getSystemSettings(): Promise<ApiResponse<any>> {
    return this.request(`/api/admin/settings`);
  }

  async updateSystemSettings(settings: any): Promise<ApiResponse<any>> {
    return this.request(`/api/admin/settings`, "POST", settings);
  }

  async getBackups(page = 1): Promise<PaginatedResponse<any>> {
    return this.request(`/api/admin/backups?page=${page}`);
  }

  async createBackup(): Promise<ApiResponse<any>> {
    return this.request(`/api/admin/backups/create`, "POST");
  }

  async restoreBackup(id: number): Promise<ApiResponse<null>> {
    return this.request(`/api/admin/backups/restore/${id}`, "POST");
  }

  async deleteBackup(id: number): Promise<ApiResponse<null>> {
    return this.request(`/api/admin/backups/destroy/${id}`, "DELETE");
  }
}

export const api = new ApiClient();
```

### B.2 Create useApi Hook

**File**: `src/hooks/useApi.ts`

```typescript
import { useState, useEffect } from "react";

interface UseApiState<T> {
  data: T | null;
  loading: boolean;
  error: string | null;
}

export function useApi<T>(fetchFn: () => Promise<any>, dependencies: any[] = []): UseApiState<T> {
  const [state, setState] = useState<UseApiState<T>>({
    data: null,
    loading: true,
    error: null,
  });

  useEffect(() => {
    let isMounted = true;

    const loadData = async () => {
      try {
        setState((s) => ({ ...s, loading: true, error: null }));
        const result = await fetchFn();

        if (isMounted) {
          setState({
            data: result.data || result,
            loading: false,
            error: null,
          });
        }
      } catch (err) {
        if (isMounted) {
          setState({
            data: null,
            loading: false,
            error: err instanceof Error ? err.message : "An error occurred",
          });
        }
      }
    };

    loadData();

    return () => {
      isMounted = false;
    };
  }, dependencies);

  return state;
}
```

### B.3 Update RoleContext for Real Auth

**File**: `src/context/RoleContext.tsx` (Updated)

```typescript
import React, { createContext, useContext, useState, useEffect } from "react";
import { User } from "@/data/mockData";

interface RoleContextType {
  user: User | null;
  role: string | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
}

const RoleContext = createContext<RoleContextType | undefined>(undefined);

export function RoleProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Fetch current user from backend
    async function fetchUser() {
      try {
        const response = await fetch("http://localhost:8000/api/user", {
          credentials: "include",
        });
        if (response.ok) {
          const data = await response.json();
          setUser(data.data);
        }
      } catch (error) {
        console.error("Failed to fetch user:", error);
      } finally {
        setLoading(false);
      }
    }

    fetchUser();
  }, []);

  const login = async (email: string, password: string) => {
    try {
      const response = await fetch("http://localhost:8000/login", {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      if (!response.ok) throw new Error("Login failed");

      const data = await response.json();
      setUser(data.user);
    } catch (error) {
      console.error("Login error:", error);
      throw error;
    }
  };

  const logout = async () => {
    try {
      await fetch("http://localhost:8000/logout", {
        method: "POST",
        credentials: "include",
      });
      setUser(null);
    } catch (error) {
      console.error("Logout error:", error);
    }
  };

  return (
    <RoleContext.Provider
      value={{
        user,
        role: user?.role || null,
        loading,
        login,
        logout,
      }}
    >
      {children}
    </RoleContext.Provider>
  );
}

export function useRole() {
  const context = useContext(RoleContext);
  if (!context) {
    throw new Error("useRole must be used within RoleProvider");
  }
  return context;
}
```

### B.4 Update PublicSearch Component

**File**: `src/pages/public/PublicSearch.tsx` (Partially Updated)

```typescript
import { useState, useEffect } from "react";
import { Link } from "react-router";
import { api } from "@/services/api";
import { Document } from "@/data/mockData";
// ... other imports

export default function PublicSearch() {
  const [query, setQuery] = useState("");
  const [type, setType] = useState("All Types");
  const [year, setYear] = useState("All Years");
  const [documents, setDocuments] = useState<Document[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const search = async () => {
      try {
        setLoading(true);
        setError(null);
        
        const result = await api.searchDocuments(
          query,
          type === "All Types" ? undefined : type,
          year === "All Years" ? undefined : year
        );

        setDocuments(result.data || []);
      } catch (err) {
        setError(err instanceof Error ? err.message : "Search failed");
      } finally {
        setLoading(false);
      }
    };

    const debounce = setTimeout(search, 300);
    return () => clearTimeout(debounce);
  }, [query, type, year]);

  return (
    <div className="min-h-screen bg-slate-50">
      {/* ... hero section unchanged ... */}

      {/* Results */}
      <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-16 py-6 sm:py-10">
        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-4">
            {error}
          </div>
        )}

        {loading ? (
          <div className="text-center py-12">
            <p className="text-slate-600">Searching...</p>
          </div>
        ) : documents.length === 0 ? (
          <div className="text-center py-12">
            <p className="text-slate-600">No documents found</p>
          </div>
        ) : (
          <div className="space-y-4">
            {documents.map((doc) => (
              <Link
                key={doc.id}
                to={`/repository/${doc.id}`}
                className="block bg-white rounded-lg border border-slate-200 p-4 hover:shadow-md transition-shadow"
              >
                <h3 className="font-semibold text-slate-900">{doc.title}</h3>
                <p className="text-sm text-slate-600 mt-1">{doc.authors.join(", ")}</p>
                <div className="mt-2 flex gap-2 flex-wrap">
                  <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                    {doc.document_type}
                  </span>
                  <span className="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded">
                    {doc.year}
                  </span>
                </div>
              </Link>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
```

### B.5 Update UploadWizard Component

**File**: `src/pages/student/UploadWizard.tsx` (Updated handleSubmit)

```typescript
async function handleSubmit(e: React.FormEvent) {
  e.preventDefault();
  
  if (!file) {
    alert("Please select a file");
    return;
  }

  try {
    setSubmitted(true);
    
    const formData = new FormData();
    formData.append("title", form.title);
    formData.append("abstract", form.abstract);
    formData.append("authors", form.authors);
    formData.append("supervisor", form.supervisor);
    formData.append("department", form.department);
    formData.append("year", form.year);
    formData.append("degree_programme", form.degree);
    formData.append("keywords", keywords.join(","));
    formData.append("document_type", form.docType);
    formData.append("file", file);

    const response = await api.uploadDocument(formData);

    if (response.success) {
      setTimeout(() => navigate("/dashboard"), 1500);
    } else {
      alert("Upload failed: " + response.error);
      setSubmitted(false);
    }
  } catch (error) {
    console.error("Upload error:", error);
    alert("Upload failed");
    setSubmitted(false);
  }
}
```

### B.6 Environment Configuration

**File**: `.env.local` (Frontend)

```env
VITE_API_URL=http://localhost:8000
VITE_APP_NAME=URMS
```

---

## PART C: BACKEND - CORS & API ROUTES

### C.1 Enable CORS

**File**: `config/cors.php` (Update)

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:3000',
        'http://127.0.0.1:5173',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### C.2 Create Dedicated API Routes

**File**: `routes/api.php` (Create New)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RepositoryController;
use App\Http\Middleware\CheckSupervisor;
use App\Http\Middleware\CheckLibrarian;
use App\Http\Middleware\CheckAdmin;

// Public endpoints
Route::get('/repositories', [RepositoryController::class, 'search']);
Route::get('/repositories/{id}', [RepositoryController::class, 'show']);
Route::post('/repositories/{id}/download', [RepositoryController::class, 'download']);

// Auth required
Route::middleware('auth')->group(function () {
    // Student
    Route::post('/repositories', [RepositoryController::class, 'store']);
    Route::get('/student/history', [RepositoryController::class, 'studentHistory']);
    Route::delete('/repositories/{id}', [RepositoryController::class, 'studentDestroy']);

    // Supervisor
    Route::middleware(CheckSupervisor::class)->group(function () {
        Route::get('/supervisor/review', [RepositoryController::class, 'supervisorIndex']);
        Route::post('/supervisor/review/{id}', [RepositoryController::class, 'supervisorAction']);
        Route::get('/supervisor/history', [RepositoryController::class, 'supervisorHistory']);
    });

    // Librarian
    Route::middleware(CheckLibrarian::class)->group(function () {
        Route::get('/library/review', [RepositoryController::class, 'libraryIndex']);
        Route::post('/library/review/{id}', [RepositoryController::class, 'libraryAction']);
        Route::get('/library/reports', [RepositoryController::class, 'analytics']);
        Route::get('/download-logs', [RepositoryController::class, 'downloadLogs']);
    });

    // Admin
    Route::middleware(CheckAdmin::class)->group(function () {
        Route::get('/admin/dashboard', [RepositoryController::class, 'adminDashboard']);
        Route::get('/admin/users', [RepositoryController::class, 'usersIndex']);
        Route::put('/admin/users/{id}/role', [RepositoryController::class, 'updateRoleByAdmin']);
        Route::delete('/admin/users/{id}', [RepositoryController::class, 'deleteUser']);
        Route::get('/admin/settings', [RepositoryController::class, 'systemSettings']);
        Route::post('/admin/settings', [RepositoryController::class, 'saveSettings']);
        Route::get('/admin/backups', [RepositoryController::class, 'manageBackups']);
        Route::post('/admin/backups/create', [RepositoryController::class, 'createBackup']);
        Route::post('/admin/backups/restore/{id}', [RepositoryController::class, 'restoreFromLog']);
        Route::delete('/admin/backups/destroy/{id}', [RepositoryController::class, 'destroyFromLog']);
    });
});

// Current user endpoint
Route::middleware('auth')->get('/user', function() {
    return response()->json(['data' => Auth::user()]);
});
```

---

## PART D: RUNNING & TESTING

### Start Backend (Laravel)
```bash
cd /opt/lampp/htdocs/repository_system
php artisan serve --host=localhost --port=8000
```

### Start Frontend (React)
```bash
cd /tmp/urms-react-version
npm install  # or pnpm install
npm run dev
```

### Test with Postman/Insomnia
1. Import collection of endpoints from `routes/api.php`
2. Set base URL to `http://localhost:8000`
3. Include `Accept: application/json` header
4. Test endpoints with sample data

### Browser Testing
1. Open `http://localhost:5173` (React frontend)
2. Try public search → should call backend API
3. Try login → should authenticate + store session
4. Try upload → should POST to backend + get `accession_number` back

---

## PART E: Common Issues & Solutions

### Issue 1: CORS Errors
**Solution**: Ensure `config/cors.php` allows frontend origin

### Issue 2: 419 CSRF Token Mismatch
**Solution**: Ensure meta csrf-token is set & included in headers

### Issue 3: 401 Unauthorized on Protected Routes
**Solution**: Check session cookie is being sent (credentials: "include" in fetch)

### Issue 4: File Upload Not Working
**Solution**: Use FormData, not JSON; set Content-Type to multipart/form-data

### Issue 5: Data Not Matching Frontend Interface
**Solution**: Transform responses in API service layer before returning to components

---

**Document Version**: 1.0  
**Status**: Ready for Implementation  
**Estimated Integration Time**: 3-5 days
