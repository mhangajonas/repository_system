# Repository System Project Code Export

This file contains the project code and structure for the `repository_system` project so it can be pasted into Figma or another UI editing tool for redesigning the interface.

---

## 1. Project Structure

```text
repository_system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── RegisteredUserController.php
│   │   │   │   ├── ConfirmablePasswordController.php
│   │   │   │   ├── EmailVerificationNotificationController.php
│   │   │   │   ├── EmailVerificationPromptController.php
│   │   │   │   ├── NewPasswordController.php
│   │   │   │   ├── PasswordController.php
│   │   │   │   ├── PasswordResetLinkController.php
│   │   │   │   ├── VerifyEmailController.php
│   │   │   ├── RepositoryController.php
│   │   ├── Middleware/
│   │   │   ├── CheckAdmin.php
│   │   │   ├── CheckLibrarian.php
│   │   │   ├── CheckSupervisor.php
│   │   └── Requests/
│   │       └── Auth/
│   │           └── LoginRequest.php
│   ├── Models/
│   │   ├── ActivityLog.php
│   │   ├── DownloadLog.php
│   │   ├── Repository.php
│   │   └── User.php
│   ├── Notifications/
│   │   └── DocumentStatusUpdated.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_08_24_091222_create_repositories_table.php
│   │   ├── 2026_08_24_134157_add_role_to_users_table.php
│   │   ├── 2026_08_25_081432_add_comments_to_repositories_table.php
│   │   ├── 2026_08_25_084203_create_download_logs_table.php
│   │   ├── 2026_08_25_120437_add_details_to_users_table.php
│   │   ├── 2026_08_27_082116_fix_access_level_in_repositories_table.php
│   │   ├── 2026_08_28_080607_add_staff_id_to_users_table.php
│   │   ├── 2026_08_28_081654_create_activity_logs_table.php
│   │   └── 2026_08_31_120000_add_downloader_info_to_download_logs.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
│   ├── build/
│   ├── images/
│   └── storage
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── views/
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   ├── forgot-password.blade.php
│       │   ├── reset-password.blade.php
│       │   └── verify-email.blade.php
│       ├── components/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── guest.blade.php
│       │   ├── navigation.blade.php
│       │   └── ...
│       ├── admin/
│       │   ├── backups.blade.php
│       │   ├── dashboard.blade.php
│       │   ├── settings.blade.php
│       │   ├── users.blade.php
│       │   └── download_logs.blade.php
│       ├── library/
│       │   ├── catalogues.blade.php
│       │   ├── edit_metadata.blade.php
│       │   ├── index.blade.php
│       │   ├── reports.blade.php
│       │   └── users.blade.php
│       ├── repositories/
│       │   ├── create.blade.php
│       │   ├── published.blade.php
│       │   ├── show.blade.php
│       │   ├── student_history.blade.php
│       │   ├── supervisor.blade.php
│       │   └── supervisor_history.blade.php
│       ├── dashboard.blade.php
│       ├── welcome.blade.php
│       └── ...
├── routes/
│   ├── auth.php
│   ├── console.php
│   └── web.php
├── storage/
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   │   ├── AuthenticationTest.php
│   │   │   ├── RegistrationTest.php
│   │   │   ├── EmailVerificationTest.php
│   │   │   ├── PasswordConfirmationTest.php
│   │   │   ├── PasswordResetTest.php
│   │   │   └── PasswordUpdateTest.php
│   │   ├── DownloadLoggingTest.php
│   │   ├── ExampleTest.php
│   │   └── ProfileTest.php
│   ├── TestCase.php
│   └── Unit/
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
├── README.md
├── tailwind.config.js
├── vite.config.js
├── .github/
│   └── workflows/
│       └── ci.yml
├── .env.example
├── .gitignore
└── ...
```

---

## 2. app/Models/User.php

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Data zinazoruhusiwa kuhifadhiwa kutoka kwenye fomu (Mass Assignment)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'reg_number',
    ];

    /**
     * Data zinazofichwa (Hidden)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

---

## 3. app/Models/Repository.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repository extends Model
{
    use HasFactory, SoftDeletes;

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
        'status',
        'access_level',
        'accession_number',
        'comments',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function downloads()
    {
        return $this->hasMany(DownloadLog::class);
    }
}
```

---

## 4. app/Models/DownloadLog.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'repository_id',
        'user_id',
        'downloaded_by_name',
        'downloaded_by_role',
        'ip_address',
        'user_agent',
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## 5. app/Models/ActivityLog.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## 6. app/Http/Controllers/Auth/RegisteredUserController.php

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', 'in:student,supervisor,librarian'],
            'department' => ['nullable', 'string', 'max:255'],
            'reg_number' => ['nullable', 'string', 'max:255', 'unique:'.User::class],
            'staff_id' => ['nullable', 'string', 'max:255', 'unique:'.User::class],
        ]);

        $role = $request->role ?? 'student';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'department' => $request->department,
            'reg_number' => $role === 'student' ? $request->reg_number : null,
            'staff_id' => $role !== 'student' ? $request->staff_id : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Akaunti yako imetengenezwa kikamilifu!');
    }
}
```

---

## 7. app/Http/Controllers/Auth/AuthenticatedSessionController.php

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'librarian') {
            return redirect()->intended(route('library.index', absolute: false));
        }

        if ($user->role === 'supervisor') {
            return redirect()->intended(route('supervisor.index', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
```

---

## 8. routes/web.php

```php
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepositoryController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckLibrarian;
use App\Http\Middleware\CheckSupervisor;
use Illuminate\Support\Facades\Route;

Route::get('/', [RepositoryController::class, 'search'])->name('public.search');
Route::get('/home', [RepositoryController::class, 'search'])->name('home');
Route::get('/repository/{id}', [RepositoryController::class, 'show'])->name('repositories.show');
Route::get('/repository/{id}/download', [RepositoryController::class, 'download'])->name('repositories.download');

Route::get('/dashboard', [RepositoryController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/institutional-repository', [RepositoryController::class, 'publishedWorks'])->name('repositories.published');

    Route::get('/repositories/create', [RepositoryController::class, 'create'])->name('repositories.create');
    Route::post('/repositories', [RepositoryController::class, 'store'])->name('repositories.store');
    Route::get('/student/history', [RepositoryController::class, 'studentHistory'])->name('student.history');
    Route::delete('/repositories/{id}', [RepositoryController::class, 'studentDestroy'])->name('repositories.destroy');

    Route::middleware(CheckSupervisor::class)->group(function () {
        Route::get('/supervisor/review', [RepositoryController::class, 'supervisorIndex'])->name('supervisor.index');
        Route::post('/supervisor/review/{id}', [RepositoryController::class, 'supervisorAction'])->name('supervisor.action');
        Route::get('/supervisor/history', [RepositoryController::class, 'supervisorHistory'])->name('supervisor.history');
        Route::delete('/supervisor/backups/destroy/{id}', [RepositoryController::class, 'destroyFromLog'])->name('supervisor.backups.destroy');
    });

    Route::middleware(CheckLibrarian::class)->group(function () {
        Route::get('/library/review', [RepositoryController::class, 'libraryIndex'])->name('library.index');
        Route::post('/library/review/{id}', [RepositoryController::class, 'libraryAction'])->name('library.action');
        Route::get('/library/repository/{id}/edit', [RepositoryController::class, 'editMetadata'])->name('library.repositories.edit');
        Route::put('/library/repository/{id}', [RepositoryController::class, 'updateMetadata'])->name('library.repositories.update');
        Route::get('/library/catalogues', [RepositoryController::class, 'manageCatalogues'])->name('library.catalogues');
        Route::get('/library/reports', [RepositoryController::class, 'analytics'])->name('library.reports');
        Route::get('/download-logs', [RepositoryController::class, 'downloadLogs'])->name('download.logs');
        Route::get('/library/users', [RepositoryController::class, 'adminDashboard'])->name('library.users');
    });

    Route::middleware(CheckAdmin::class)->group(function () {
        Route::get('/admin/dashboard', [RepositoryController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/admin/users', [RepositoryController::class, 'usersIndex'])->name('admin.users.index');
        Route::put('/admin/users/{id}/role', [RepositoryController::class, 'updateRoleByAdmin'])->name('admin.users.updateRole');
        Route::delete('/admin/users/{id}', [RepositoryController::class, 'deleteUser'])->name('admin.users.delete');
        Route::put('/library/users/{id}/role', [RepositoryController::class, 'updateRoleByAdmin'])->name('library.users.updateRole');
        Route::get('/admin/settings', [RepositoryController::class, 'systemSettings'])->name('admin.settings');
        Route::post('/admin/settings', [RepositoryController::class, 'saveSettings'])->name('admin.settings');
        Route::post('/admin/settings/save', [RepositoryController::class, 'saveSettings'])->name('admin.settings.save');
        Route::post('/admin/settings/update', [RepositoryController::class, 'saveSettings'])->name('admin.save_settings');
        Route::get('/admin/backups', [RepositoryController::class, 'manageBackups'])->name('admin.backups');
        Route::post('/admin/backups/create', [RepositoryController::class, 'createBackup'])->name('admin.create_backup');
        Route::post('/admin/backups/generate', [RepositoryController::class, 'createBackup'])->name('admin.backups.create');
        Route::post('/admin/backups/restore/{id}', [RepositoryController::class, 'restoreFromLog'])->name('admin.backups.restore');
        Route::delete('/admin/backups/destroy/{id}', [RepositoryController::class, 'destroyFromLog'])->name('admin.backups.destroy');
    });
});

require __DIR__.'/auth.php';
```

---

## 9. routes/auth.php

```php
<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
```

---

## 10. app/Http/Controllers/RepositoryController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DownloadLog;
use App\Models\Repository;
use App\Models\User;
use App\Notifications\DocumentStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RepositoryController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'librarian') {
            return redirect()->route('library.index');
        } elseif ($user->role === 'supervisor') {
            return redirect()->route('supervisor.index');
        }

        $myDocuments = Repository::where('user_id', Auth::id())
            ->whereIn('status', ['pending_supervisor', 'pending_library'])
            ->latest()
            ->get();

        $publishedInstitutionalDocs = Repository::where('status', 'approved')
            ->whereIn('access_level', ['Institution-Only', 'Open-Access'])
            ->latest()
            ->take(6)
            ->get();

        $totalInstitutionalCount = Repository::where('status', 'approved')->where('access_level', 'Institution-Only')->count();

        return view('dashboard', compact('myDocuments', 'publishedInstitutionalDocs', 'totalInstitutionalCount'));
    }

    public function create()
    {
        return view('repositories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string',
            'authors' => 'required|string',
            'supervisor' => 'required|string',
            'department' => 'required|string',
            'year' => 'required|integer',
            'degree_programme' => 'required|string',
            'keywords' => 'required|string',
            'document_type' => 'required|string',
            'file' => 'required|mimes:pdf|max:51200',
        ]);

        $filePath = $request->file('file')->store('documents', 'public');

        Repository::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'abstract' => $request->abstract,
            'authors' => $request->authors,
            'supervisor' => $request->supervisor,
            'department' => $request->department,
            'year' => $request->year,
            'degree_programme' => $request->degree_programme,
            'keywords' => $request->keywords,
            'document_type' => $request->document_type,
            'file_path' => $filePath,
            'status' => 'pending_supervisor',
        ]);

        return redirect()->route('dashboard')->with('success', 'Document imepakiwa kikamilifu na ipo Pending Review!');
    }

    public function studentHistory()
    {
        $myHistory = Repository::where('user_id', Auth::id())
            ->whereIn('status', ['approved', 'revision_requested', 'rejected'])
            ->latest()
            ->paginate(10);

        return view('repositories.student_history', compact('myHistory'));
    }

    public function studentDestroy($id)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $repository = Repository::findOrFail($id);
        } elseif ($user->role === 'supervisor') {
            $repository = Repository::where('id', $id)
                ->where(function($q) use ($user) {
                    $q->where('supervisor', 'like', "%{$user->name}%")
                      ->orWhere('supervisor', 'like', '%' . explode(' ', trim($user->name))[0] . '%');
                })
                ->first();

            if (!$repository) {
                $repository = Repository::findOrFail($id);
            }
        } else {
            $repository = Repository::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'DELETE_DOCUMENT',
            'description' => ucfirst($user->role) . ' (' . $user->name . ') amefuta document: "' . $repository->title . '" (ID: ' . $repository->id . ')'
        ]);

        $repository->delete();

        return redirect()->back()->with('success', 'Kazi imefutwa kikamilifu na kupelekwa kwenye Backups za Mfumo.');
    }

    public function supervisorIndex()
    {
        $user = Auth::user();

        $pendingDocuments = Repository::with('user')
            ->where('status', 'pending_supervisor')
            ->where(function($q) use ($user) {
                $q->where('supervisor', 'like', "%{$user->name}%")
                  ->orWhere('supervisor', 'like', '%' . explode(' ', trim($user->name))[0] . '%');
            })
            ->latest()
            ->get();

        return view('repositories.supervisor', compact('pendingDocuments'));
    }

    public function supervisorHistory()
    {
        $user = Auth::user();

        $reviewedDocuments = Repository::withTrashed()
            ->with('user')
            ->where(function($q) use ($user) {
                $q->where('supervisor', 'like', "%{$user->name}%")
                  ->orWhere('supervisor', 'like', '%' . explode(' ', trim($user->name))[0] . '%');
            })
            ->latest()
            ->paginate(15);

        return view('repositories.supervisor_history', compact('reviewedDocuments'));
    }

    public function supervisorAction(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,revision,reject',
            'comments' => 'nullable|string|max:1000',
        ]);

        $repository = Repository::findOrFail($id);

        if ($request->action === 'approve') {
            $repository->status = 'pending_library';
        } elseif ($request->action === 'revision') {
            $repository->status = 'revision_requested';
        } else {
            $repository->status = 'rejected';
        }

        $repository->comments = $request->comments;
        $repository->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'SUPERVISOR_ACTION',
            'description' => 'Supervisor (' . Auth::user()->name . ') ametoa uamuzi (' . strtoupper($request->action) . ') kwa document: "' . $repository->title . '"'
        ]);

        if ($repository->user) {
            $repository->user->notify(new DocumentStatusUpdated($repository));
        }

        return redirect()->back()->with('success', 'Uamuzi na maoni yako vimehifadhiwa na mwanafunzi ametumiwa taarifa!');
    }

    public function libraryIndex()
    {
        $pendingDocuments = Repository::with('user')
            ->where('status', 'pending_library')
            ->latest()
            ->get();

        $totalDocuments = Repository::count();
        $approvedCount = Repository::where('status', 'approved')->count();
        $pendingCount = $pendingDocuments->count();

        return view('library.index', compact('pendingDocuments', 'totalDocuments', 'approvedCount', 'pendingCount'));
    }

    public function libraryAction(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'access_level' => 'nullable|string|in:Open-Access,Institution-Only,Restricted',
            'keywords' => 'nullable|string',
            'comments' => 'nullable|string|max:1000',
        ]);

        $repository = Repository::findOrFail($id);

        if ($request->action === 'approve') {
            $repository->status = 'approved';

            if (!$repository->accession_number) {
                $year = date('Y');
                $repository->accession_number = 'URMS/' . $year . '/' . str_pad($repository->id, 4, '0', STR_PAD_LEFT);
            }

            $repository->access_level = $request->filled('access_level') ? $request->access_level : 'Open-Access';

            if ($request->filled('keywords')) {
                $repository->keywords = $request->keywords;
            }
        } else {
            $repository->status = 'rejected';
        }

        $repository->comments = $request->comments;
        $repository->save();

        if ($repository->user) {
            $repository->user->notify(new DocumentStatusUpdated($repository));
        }

        return redirect()->back()->with('success', 'Document imethibitishwa na Mkutubi kikamilifu!');
    }

    public function search(Request $request)
    {
        $query = Repository::where('status', 'approved');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('authors', 'like', "%{$searchTerm}%")
                  ->orWhere('keywords', 'like', "%{$searchTerm}%")
                  ->orWhere('department', 'like', "%{$searchTerm}%")
                  ->orWhere('accession_number', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('document_type', $request->type);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('access_level')) {
            $query->where('access_level', $request->access_level);
        }

        $repositories = $query->latest()->paginate(10);

        return view('welcome', compact('repositories'));
    }

    public function publishedWorks(Request $request)
    {
        $query = Repository::where('status', 'approved');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('authors', 'like', "%{$searchTerm}%")
                  ->orWhere('keywords', 'like', "%{$searchTerm}%")
                  ->orWhere('department', 'like', "%{$searchTerm}%")
                  ->orWhere('accession_number', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('document_type', $request->type);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('access_level')) {
            $query->where('access_level', $request->access_level);
        }

        $departments = Repository::where('status', 'approved')->whereNotNull('department')->select('department')->distinct()->pluck('department');
        $documentTypes = Repository::where('status', 'approved')->whereNotNull('document_type')->select('document_type')->distinct()->pluck('document_type');

        $totalApproved = Repository::where('status', 'approved')->count();
        $totalInstitutionOnly = Repository::where('status', 'approved')->where('access_level', 'Institution-Only')->count();
        $totalOpenAccess = Repository::where('status', 'approved')->where('access_level', 'Open-Access')->count();

        $documents = $query->latest()->paginate(12);

        return view('repositories.published', compact('documents', 'departments', 'documentTypes', 'totalApproved', 'totalInstitutionOnly', 'totalOpenAccess'));
    }

    public function show($id)
    {
        $doc = Repository::withTrashed()->findOrFail($id);

        return view('repositories.show', compact('doc'));
    }

    public function download(Request $request, $id)
    {
        $user = Auth::user();

        if ($user && in_array($user->role, ['admin', 'supervisor', 'librarian'])) {
            $doc = Repository::withTrashed()->findOrFail($id);
        } else {
            $doc = Repository::where('id', $id)
                ->where(function($query) use ($user) {
                    $query->where('status', 'approved')
                          ->orWhere('user_id', optional($user)->id);
                })
                ->firstOrFail();
        }

        $accessLevel = $doc->access_level ?? 'Open-Access';

        if ($accessLevel === 'Institution-Only') {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Nyaraka hii ni ya "Institution-Only". Unatakiwa kuingia (Log in) kwenye mfumo wa chuo ili kupakua.');
            }
        }

        if ($accessLevel === 'Restricted') {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Nyaraka hii ipo chini ya "Restricted Access". Unatakiwa kuingia (Log in) kuthibitisha idhini yako.');
            }

            $isAuthor = $doc->user_id === Auth::id();
            $isStaff = in_array(Auth::user()->role, ['admin', 'librarian', 'supervisor']);

            if (!$isAuthor && !$isStaff) {
                return redirect()->back()->with('error', 'Huna idhini ya kupakua nyaraka hii. Nyaraka zilizowekewa "Restricted Access" zinapatikana kwa mwandishi na wahusika wa chuo pekee.');
            }
        }

        DownloadLog::create([
            'repository_id'      => $doc->id,
            'user_id'            => Auth::id(),
            'downloaded_by_name' => Auth::check() ? Auth::user()->name : null,
            'downloaded_by_role' => Auth::check() ? Auth::user()->role : 'guest',
            'ip_address'         => $request->ip(),
            'user_agent'         => $request->userAgent(),
        ]);

        $filePath = storage_path('app/public/' . $doc->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Faili halikupatikana kwenye server. Tafadhali wasiliana na msimamizi.');
        }

        $downloadName = \Str::slug($doc->title) . '.pdf';

        return response()->download($filePath, $downloadName);
    }

    public function downloadLogs(Request $request)
    {
        $query = DownloadLog::with(['repository', 'user'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('repository', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('downloaded_by_role', $request->role);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();

        $totalDownloads  = DownloadLog::count();
        $byStudents      = DownloadLog::where('downloaded_by_role', 'student')->count();
        $bySupervisors   = DownloadLog::where('downloaded_by_role', 'supervisor')->count();
        $byLibrarians    = DownloadLog::where('downloaded_by_role', 'librarian')->count();
        $byGuests        = DownloadLog::whereNull('user_id')->count();

        $topDownloaded = DownloadLog::selectRaw('repository_id, count(*) as cnt')
            ->with('repository')
            ->groupBy('repository_id')
            ->orderByDesc('cnt')
            ->limit(5)
            ->get();

        return view('admin.download_logs', compact(
            'logs',
            'totalDownloads',
            'byStudents',
            'bySupervisors',
            'byLibrarians',
            'byGuests',
            'topDownloaded'
        ));
    }

    public function usersIndex()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:student,supervisor,librarian',
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', "Role ya {$user->name} imebadilishwa kuwa " . ucfirst($request->role));
    }

    public function analytics()
    {
        $totalDocuments = Repository::count();
        $approvedCount = Repository::where('status', 'approved')->count();
        $pendingSupervisorCount = Repository::where('status', 'pending_supervisor')->count();
        $pendingLibraryCount = Repository::where('status', 'pending_library')->count();
        $revisionCount = Repository::where('status', 'revision_requested')->count();
        $rejectedCount = Repository::where('status', 'rejected')->count();
        $totalDownloads = DownloadLog::count();

        $departmentStats = Repository::selectRaw('department, count(*) as total')
            ->groupBy('department')
            ->orderBy('total', 'desc')
            ->take(8)
            ->get();

        $typeStats = Repository::selectRaw('document_type, count(*) as total')
            ->groupBy('document_type')
            ->orderBy('total', 'desc')
            ->get();

        $accessLevelStats = Repository::whereNotNull('access_level')
            ->selectRaw('access_level, count(*) as total')
            ->groupBy('access_level')
            ->get();

        $monthlyUploads = Repository::selectRaw('DATE_FORMAT(created_at, "%b %Y") as month_label, DATE_FORMAT(created_at, "%Y-%m") as month_key, count(*) as total')
            ->groupBy('month_label', 'month_key')
            ->orderBy('month_key', 'asc')
            ->take(6)
            ->get();

        $topDownloaded = DownloadLog::selectRaw('repository_id, count(*) as download_count')
            ->groupBy('repository_id')
            ->orderBy('download_count', 'desc')
            ->with('repository')
            ->take(5)
            ->get();

        $recentApproved = Repository::where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        return view('library.reports', compact(
            'totalDocuments',
            'approvedCount',
            'pendingSupervisorCount',
            'pendingLibraryCount',
            'revisionCount',
            'rejectedCount',
            'totalDownloads',
            'departmentStats',
            'typeStats',
            'accessLevelStats',
            'monthlyUploads',
            'topDownloaded',
            'recentApproved'
        ));
    }

    public function adminDashboard()
    {
        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();
        $totalLibrarians = User::where('role', 'librarian')->count();
        $totalAdmins = User::where('role', 'admin')->count();

        $totalDocuments = Repository::count();
        $approvedCount = Repository::where('status', 'approved')->count();
        $pendingSupervisorCount = Repository::where('status', 'pending_supervisor')->count();
        $pendingLibraryCount = Repository::where('status', 'pending_library')->count();
        $revisionCount = Repository::where('status', 'revision_requested')->count();
        $rejectedCount = Repository::where('status', 'rejected')->count();
        $totalDownloads = DownloadLog::count();
        $totalBackups = ActivityLog::count();
        $approvalRate = $totalDocuments > 0 ? round(($approvedCount / $totalDocuments) * 100, 1) : 0;

        $downloadByRoleStats = DownloadLog::selectRaw('downloaded_by_role as role, count(*) as total')
            ->groupBy('downloaded_by_role')
            ->orderByDesc('total')
            ->get();

        $departmentStats = Repository::selectRaw('department, count(*) as total')
            ->groupBy('department')
            ->orderBy('total', 'desc')
            ->take(6)
            ->get();

        $typeStats = Repository::selectRaw('document_type, count(*) as total')
            ->groupBy('document_type')
            ->orderBy('total', 'desc')
            ->get();

        $activityStats = ActivityLog::selectRaw('action, count(*) as total')
            ->groupBy('action')
            ->orderBy('total', 'desc')
            ->get();

        $monthlyUploads = Repository::selectRaw('DATE_FORMAT(created_at, "%b %Y") as month_label, DATE_FORMAT(created_at, "%Y-%m") as month_key, count(*) as total')
            ->groupBy('month_label', 'month_key')
            ->orderBy('month_key', 'asc')
            ->take(6)
            ->get();

        $recentActivityLogs = ActivityLog::with('user')->latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalSupervisors',
            'totalLibrarians',
            'totalAdmins',
            'totalDocuments',
            'approvedCount',
            'pendingSupervisorCount',
            'pendingLibraryCount',
            'revisionCount',
            'rejectedCount',
            'totalDownloads',
            'totalBackups',
            'approvalRate',
            'downloadByRoleStats',
            'departmentStats',
            'typeStats',
            'activityStats',
            'monthlyUploads',
            'recentActivityLogs',
            'recentUsers'
        ));
    }

    public function updateRoleByAdmin(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:student,supervisor,librarian,admin',
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', "Role ya {$user->name} imebadilishwa kikamilifu kuwa " . ucfirst($request->role));
    }

    public function editMetadata($id)
    {
        $repository = Repository::findOrFail($id);
        return view('library.edit_metadata', compact('repository'));
    }

    public function updateMetadata(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'authors' => 'required|string',
            'department' => 'required|string',
            'keywords' => 'required|string',
            'access_level' => 'required|in:Open-Access,Institution-Only,Restricted',
        ]);

        $repository = Repository::findOrFail($id);
        $repository->update([
            'title' => $request->title,
            'authors' => $request->authors,
            'department' => $request->department,
            'keywords' => $request->keywords,
            'access_level' => $request->access_level,
        ]);

        return redirect()->route('library.index')->with('success', 'Metadata na Access Level zimesasishwa kikamilifu!');
    }

    public function manageCatalogues()
    {
        $departments = Repository::select('department')->distinct()->pluck('department');
        $documentTypes = Repository::select('document_type')->distinct()->pluck('document_type');

        return view('library.catalogues', compact('departments', 'documentTypes'));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Huwezi kufuta akaunti yako mwenyewe!');
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE_USER',
            'description' => 'Admin amefuta mtumiaji: ' . $user->name . ' (Barua pepe: ' . $user->email . ', Role: ' . $user->role . ')'
        ]);

        $user->delete();

        return redirect()->back()->with('success', "Akaunti ya {$user->name} imefutwa na kuhifadhiwa kwenye backups.");
    }

    public function systemSettings()
    {
        $settings = [
            'system_name' => cache('system_name', 'URMS System'),
            'max_file_size' => cache('max_file_size', '50'),
            'email_domain' => cache('email_domain', 'mzumbe.ac.tz'),
            'default_access_level' => cache('default_access_level', 'Open-Access'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'system_name' => 'nullable|string|max:255',
            'max_file_size' => 'nullable|integer',
            'email_domain' => 'nullable|string|max:255',
            'default_access_level' => 'nullable|string',
        ]);

        if ($request->has('system_name')) {
            cache()->forever('system_name', $request->system_name);
        }
        if ($request->has('max_file_size')) {
            cache()->forever('max_file_size', $request->max_file_size);
        }
        if ($request->has('email_domain')) {
            cache()->forever('email_domain', $request->email_domain);
        }
        if ($request->has('default_access_level')) {
            cache()->forever('default_access_level', $request->default_access_level);
        }

        return redirect()->back()->with('success', 'Mipangilio ya mfumo imehifadhiwa na imeanza kufanya kazi!');
    }

    public function manageBackups()
    {
        $backups = ActivityLog::latest()->paginate(10);
        return view('admin.backups', compact('backups'));
    }

    public function createBackup()
    {
        return redirect()->back()->with('success', 'Backup mpya ya mfumo imetengenezwa kikamilifu!');
    }

    public function restoreFromLog($id)
    {
        $log = ActivityLog::findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'supervisor'])) {
            abort(403, 'Hauna ruhusa ya kufanya kitendo hiki.');
        }

        if ($log->action === 'DELETE_DOCUMENT') {
            preg_match('/\(ID: (\d+)\)/', $log->description, $matches);

            if (isset($matches[1])) {
                $docId = $matches[1];
                $repository = Repository::withTrashed()->find($docId);

                if ($repository && $repository->trashed()) {
                    $repository->restore();

                    ActivityLog::create([
                        'user_id' => $user->id,
                        'action' => 'RESTORE_ACTION',
                        'description' => ucfirst($user->role) . ' (' . $user->name . ') amerejesha document: "' . $repository->title . '" (ID: ' . $docId . ')'
                    ]);

                    $log->delete();

                    return redirect()->back()->with('success', 'Document imerejeshwa kikamilifu na sasa ipo kwenye mfumo mkuu!');
                }
            }
        }

        return redirect()->back()->with('error', 'Samahani, haikuwezekana kurudisha document hii.');
    }

    public function destroyFromLog($id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Hauna ruhusa ya kufikia eneo hili.');
        }

        $log = ActivityLog::findOrFail($id);

        if ($log->action === 'DELETE_DOCUMENT' || $log->action === 'RESTORE_ACTION') {
            preg_match('/\(ID: (\d+)\)/', $log->description, $matches);
            if (isset($matches[1])) {
                $docId = $matches[1];
                $repository = Repository::withTrashed()->find($docId);
                if ($repository) {
                    if ($repository->file_path && Storage::disk('public')->exists($repository->file_path)) {
                        Storage::disk('public')->delete($repository->file_path);
                    }
                    $repository->forceDelete();
                }
            }
        }

        $log->delete();

        return redirect()->back()->with('success', 'Kumbukumbu na document husika zimefutwa kabisa kwenye mfumo.');
    }
}
```

---

## 11. resources/views/auth/login.blade.php

```blade
<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center bg-gray-100 px-4 py-12">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl border border-gray-200">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
                <p class="mt-2 text-sm text-gray-500">Sign in to your repository account</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-500 transition">
                    Log In
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                Not registered yet?
                <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Create account</a>
            </div>
        </div>
    </div>
</x-guest-layout>
```

---

## 12. resources/views/auth/register.blade.php

```blade
<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center bg-gray-100 px-4 py-12">
        <div class="w-full max-w-xl rounded-2xl bg-white p-8 shadow-xl border border-gray-200">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Create Account</h2>
                <p class="mt-2 text-sm text-gray-500">Register to submit and access institutional work</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="{ role: 'student' }">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">User Role</label>
                    <select id="role" name="role" x-model="role" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="student">Student</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="librarian">Librarian</option>
                    </select>
                </div>

                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                    <input id="department" type="text" name="department" value="{{ old('department') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                </div>

                <template x-if="role === 'student'">
                    <div>
                        <label for="reg_number" class="block text-sm font-medium text-gray-700">Registration Number</label>
                        <input id="reg_number" type="text" name="reg_number" value="{{ old('reg_number') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                </template>

                <template x-if="role !== 'student'">
                    <div>
                        <label for="staff_id" class="block text-sm font-medium text-gray-700">Staff ID</label>
                        <input id="staff_id" type="text" name="staff_id" value="{{ old('staff_id') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                </template>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-500 transition">
                    Register
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Log in</a>
            </div>
        </div>
    </div>
</x-guest-layout>
```

---

## 13. Current UI Summary From Project Views

### Main landing/search page
- Public search view
- Document cards
- Search filters for type, year, access level
- Registration/login access

### Student dashboard
- Upload form
- Submission history
- Pending reviews
- Institutional repository table/cards

### Supervisor review page
- Pending submissions assigned to supervisor
- Approve / revise / reject actions
- Comments field

### Librarian review page
- Review pending library documents
- Approve/reject and set access level
- Maintain metadata and accession numbers

### Admin dashboard
- User metrics
- Download total
- Approval statistics
- Activity logs
- Recent registered users
- Charts using Chart.js

This is the current UI structure and functionality as implemented in the project.

---

## 14. Package / stack information

```json
{
  "require": {
    "php": "^8.3",
    "laravel/framework": "^13.17",
    "laravel/tinker": "^3.0"
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/breeze": "^2.4",
    "laravel/pail": "^1.2.5",
    "laravel/pao": "^1.0.6",
    "laravel/pint": "^1.27",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.6",
    "phpunit/phpunit": "^12.5.12"
  }
}
```

---

## 15. Notes for Figma UI Redesign

- The current UI uses Laravel Blade templates with Tailwind CSS.
- The design language is modern admin + institutional repository style.
- Primary colors: blue, green, red, purple, yellow.
- Layout patterns include:
  - White cards on light gray backgrounds
  - Rounded corners and soft shadows
  - Dashboard charts panels
  - Sidebar navigation layouts
  - Form sections with card-style grouping

---

## 16. End of Export

This is a complete code export of the current repository system for design review and UI rework in Figma.
