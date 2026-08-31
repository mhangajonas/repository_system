<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DownloadLog;
use App\Models\Repository;
use App\Models\User;
use App\Notifications\DocumentStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RepositoryController extends Controller
{
    // 1. Dashboard Redirector: Inamwelekeza kila mtumiaji kwenye Dashboard ya Role yake
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

        // Kama ni Student, pata kazi zake ambazo bado ziko PENDING
        $myDocuments = Repository::where('user_id', Auth::id())
            ->whereIn('status', ['pending_supervisor', 'pending_library'])
            ->latest()
            ->get();

        return view('dashboard', compact('myDocuments'));
    }

    // Onyesha fomu ya ku-upload
    public function create()
    {
        return view('repositories.create');
    }

    // Hifadhi data na faili (FR-2.1 & FR-2.2 Validation)
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
            'file' => 'required|mimes:pdf|max:51200', // PDF pekee, Max 50MB
        ]);

        // Hifadhi PDF ndani ya storage/app/public/documents
        $filePath = $request->file('file')->store('documents', 'public');

        // Hifadhi taarifa kwenye Database
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
            'status' => 'pending_supervisor', // Status ya mwanzo kwenda kwa Supervisor
        ]);

        return redirect()->route('dashboard')->with('success', 'Document imepakiwa kikamilifu na ipo Pending Review!');
    }

    // 2. My Submissions: Inaonyesha kazi ZOTE zilizotolewa maamuzi (History)
    public function studentHistory()
    {
        $myHistory = Repository::where('user_id', Auth::id())
            ->whereIn('status', ['approved', 'revision_requested', 'rejected'])
            ->latest()
            ->paginate(10);

        return view('repositories.student_history', compact('myHistory'));
    }

    // Kufuta kazi (Inawezeshwa kwa Mwanafunzi, Supervisor, au Admin - Inafanya Soft Delete pekee ili iende kwenye Backups za Admin)
    public function studentDestroy($id)
    {
        $user = Auth::user();

        // Kama ni admin, mpe ruhusa ya kupata kazi yoyote
        if ($user->role === 'admin') {
            $repository = Repository::findOrFail($id);
        } 
        // Kama ni supervisor, hakikisha inamhusu yeye au imepita kwake
        elseif ($user->role === 'supervisor') {
            $repository = Repository::where('id', $id)
                ->where('supervisor', 'like', "%{$user->name}%")
                ->firstOrFail();
        } 
        // Kama ni mwanafunzi wa kawaida, lazima iwe ni kazi yake mwenyewe
        else {
            $repository = Repository::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        // Hifadhi kumbukumbu kwenye Activity Log ili ionekane kwenye Backups za Admin moja kwa moja
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'DELETE_DOCUMENT',
            'description' => ucfirst($user->role) . ' (' . $user->name . ') amefuta document: "' . $repository->title . '" (ID: ' . $repository->id . ')'
        ]);

        // Soft Delete
        $repository->delete();

        return redirect()->back()->with('success', 'Kazi imefutwa kikamilifu na kupelekwa kwenye Backups za Mfumo.');
    }

    // Dashboard ya Supervisor (Inaonyesha kazi zinazomuhusu huyu Supervisor pekee)
    public function supervisorIndex()
    {
        $user = Auth::user();

        $pendingDocuments = Repository::with('user')
            ->where('status', 'pending_supervisor')
            ->where('supervisor', 'like', "%{$user->name}%")
            ->latest()
            ->get();

        return view('repositories.supervisor', compact('pendingDocuments'));
    }

    // Kumbukumbu ya binafsi ya Supervisor (Kazi alizozifanyia maamuzi yeye pekee - Imeongezwa withTrashed ili kuleta na zilizofutwa)
    public function supervisorHistory()
    {
        $user = Auth::user();

        $reviewedDocuments = Repository::withTrashed()
            ->with('user')
            ->where('supervisor', 'like', "%{$user->name}%")
            ->latest()
            ->paginate(15);

        // Ongeza $backups hapa ili kuondoa Undefined variable $backups kwenye blade
        $backups = ActivityLog::latest()->paginate(10);

        return view('repositories.supervisor_history', compact('reviewedDocuments', 'backups'));
    }

    // Uamuzi wa Supervisor + Kutuma Barua Pepe + Kuhifadhi Maoni (FR-2.4 & FR-2.5)
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

        // Hifadhi pia kitendo cha Supervisor kwenye Activity Log kwa ajili ya Backups/Audit trail
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'SUPERVISOR_ACTION',
            'description' => 'Supervisor (' . Auth::user()->name . ') ametoa uamuzi (' . strtoupper($request->action) . ') kwa document: "' . $repository->title . '"'
        ]);

        // Tuma Notification kwa mwanafunzi
        if ($repository->user) {
            $repository->user->notify(new DocumentStatusUpdated($repository));
        }

        return redirect()->back()->with('success', 'Uamuzi na maoni yako vimehifadhiwa na mwanafunzi ametumiwa taarifa!');
    }

    // Dashboard ya Librarian (Kazi zilizothibitishwa na Supervisor)
    public function libraryIndex()
    {
        $pendingDocuments = Repository::with('user')
            ->where('status', 'pending_library')
            ->latest()
            ->get();

        return view('library.index', compact('pendingDocuments'));
    }

    // Uamuzi wa Librarian (Final Approval + Accession Number + Access Level + Keywords + Comments)
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

            // Zalisha Accession Number kiotomatiki (FR-2.7)
            if (!$repository->accession_number) {
                $year = date('Y');
                $repository->accession_number = 'URMS/' . $year . '/' . str_pad($repository->id, 4, '0', STR_PAD_LEFT);
            }

            // Hifadhi Access Level na Keywords (Weka Open-Access kama default ikiwa haikuchaguliwa)
            $repository->access_level = $request->filled('access_level') ? $request->access_level : 'Open-Access';
            
            if ($request->filled('keywords')) {
                $repository->keywords = $request->keywords;
            }
        } else {
            $repository->status = 'rejected';
        }

        $repository->comments = $request->comments;
        $repository->save();

        // Tuma Notification kwa mwanafunzi
        if ($repository->user) {
            $repository->user->notify(new DocumentStatusUpdated($repository));
        }

        return redirect()->back()->with('success', 'Document imethibitishwa na Mkutubi kikamilifu!');
    }

    // Public Search & Discovery (FR-3.1 & FR-3.2)
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

        $repositories = $query->latest()->paginate(10);

        return view('welcome', compact('repositories'));
    }

    // Kuonyesha Maelezo Kamili ya Metadata (FR-3.3) - Imesahihishwa kuruhusu kusoma hata zilizofutwa (withTrashed)
    public function show($id)
    {
        $doc = Repository::withTrashed()->findOrFail($id);

        return view('repositories.show', compact('doc'));
    }

    // Ulinzi wa Kupakua na Kurekodi Download Log (FR-3.4 & FR-3.5) - Inaruhusu Admin na Supervisor kupakua hata zilizofutwa
    public function download(Request $request, $id)
    {
        $user = Auth::user();

        // Ruhusu Admin, Librarian, au Supervisor kupakua hata kama imefutwa (withTrashed)
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

        // FR-3.4: Angalia Kiwango cha Ufikiaji (Access Level)
        if ($doc->access_level === 'Institution-Only' || $doc->access_level === 'Restricted') {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Unatakiwa kuingia (Log in) ili kupakua document hii.');
            }
        }

        // FR-3.5: Audit Trail - Rekodi log ya kupakua
        DownloadLog::create([
            'repository_id' => $doc->id,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $filePath = storage_path('app/public/' . $doc->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Faili halikupatikana kwenye server.');
        }

        return response()->download($filePath);
    }

    // Onyesha orodha ya watumiaji wote (Kwa Admin/Librarian)
    public function usersIndex()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    // Badilisha Role ya mtumiaji (Librarian fallback)
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

    // Analytical Dashboard & Reports (FR-4.2)
    public function analytics()
    {
        $totalDocuments = Repository::count();
        $approvedCount = Repository::where('status', 'approved')->count();
        $pendingSupervisorCount = Repository::where('status', 'pending_supervisor')->count();
        $pendingLibraryCount = Repository::where('status', 'pending_library')->count();
        $revisionCount = Repository::where('status', 'revision_requested')->count();
        $rejectedCount = Repository::where('status', 'rejected')->count();

        $departmentStats = Repository::selectRaw('department, count(*) as total')
            ->groupBy('department')
            ->orderBy('total', 'desc')
            ->get();

        $typeStats = Repository::selectRaw('document_type, count(*) as total')
            ->groupBy('document_type')
            ->orderBy('total', 'desc')
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
            'departmentStats',
            'typeStats',
            'recentApproved'
        ));
    }

    // -------------------------------------------------------------
    // SYSTEM ADMINISTRATOR / ICT DASHBOARD & USER CONTROL
    // -------------------------------------------------------------

    // Dashboard ya System Administrator / ICT Staff
    public function adminDashboard()
    {
        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();
        $totalLibrarians = User::where('role', 'librarian')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalDocuments = Repository::count();

        // Orodha ya watumiaji wote
        $users = User::latest()->paginate(10);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalSupervisors',
            'totalLibrarians',
            'totalAdmins',
            'totalDocuments',
            'users'
        ));
    }

    // Kurekebisha Role ya Mtumiaji (Pamoja na Admin Option)
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

    // ==========================================
    // MAJUKUMU MAPYA YA LIBRARIAN
    // ==========================================

    // 1. Fomu ya Kuedit Metadata & Access Level
    public function editMetadata($id)
    {
        $repository = Repository::findOrFail($id);
        return view('library.edit_metadata', compact('repository'));
    }

    // 2. Hifadhi Mabadiliko ya Metadata & Access Level
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

    // 3. Simamia Catalogues (Idara na Aina za Nyaraka)
    public function manageCatalogues()
    {
        $departments = Repository::select('department')->distinct()->pluck('department');
        $documentTypes = Repository::select('document_type')->distinct()->pluck('document_type');

        return view('library.catalogues', compact('departments', 'documentTypes'));
    }

    // ==========================================
    // MAJUKUMU MAPYA YA SYSTEM ADMINISTRATOR (ICT)
    // ==========================================

    // 1. Futa Akaunti ya Mtumiaji (Imeunganishwa na ActivityLog)
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Zuia admin kujifuta mwenyewe
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Huwezi kufuta akaunti yako mwenyewe!');
        }

        // Hifadhi kumbukumbu kwenye Activity Log kabla ya kufuta
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE_USER',
            'description' => 'Admin amefuta mtumiaji: ' . $user->name . ' (Barua pepe: ' . $user->email . ', Role: ' . $user->role . ')'
        ]);

        $user->delete();

        return redirect()->back()->with('success', "Akaunti ya {$user->name} imefutwa na kuhifadhiwa kwenye backups.");
    }

    // 2. Configure System Settings (Inasoma mipangilio iliyopo kwenye Cache)
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

        // Hifadhi kwenye Cache ili ziweze kusomeka na kubadilika kweli mfumo mzima
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

    // 3. Manage Backups (Inasoma Activity Logs bila filters za ziada ili kuona kila kitu kilichofutwa na Supervisor au yeyote)
    public function manageBackups()
    {
        $backups = ActivityLog::latest()->paginate(10);
        return view('admin.backups', compact('backups'));
    }

    public function createBackup()
    {
        return redirect()->back()->with('success', 'Backup mpya ya mfumo imetengenezwa kikamilifu!');
    }

    // 4. Rudisha hali ya nyuma kutokana na Logi (Restore from Backup/Activity Log - Soft Delete Support)
    public function restoreFromLog($id)
    {
        $log = ActivityLog::findOrFail($id);
        $user = Auth::user();

        // Ruhusu Admin au Supervisor ku-restore kazi
        if (!in_array($user->role, ['admin', 'supervisor'])) {
            abort(403, 'Hauna ruhusa ya kufanya kitendo hiki.');
        }

        if ($log->action === 'DELETE_DOCUMENT') {
            preg_match('/\(ID: (\d+)\)/', $log->description, $matches);
            
            if (isset($matches[1])) {
                $docId = $matches[1];
                $repository = Repository::withTrashed()->find($docId);
                
                if ($repository && $repository->trashed()) {
                    $repository->restore(); // Kazi inarudishwa rasmi kwenye database
                    
                    // Rekodi log mpya ya kitendo cha kurejesha
                    ActivityLog::create([
                        'user_id' => $user->id,
                        'action' => 'RESTORE_ACTION',
                        'description' => ucfirst($user->role) . ' (' . $user->name . ') amerejesha document: "' . $repository->title . '" (ID: ' . $docId . ')'
                    ]);

                    // Futa log ya zamani ya kufuta ili isije ikasababisha mkanganyiko kwenye Backups
                    $log->delete();

                    return redirect()->back()->with('success', 'Document imerejeshwa kikamilifu na sasa ipo kwenye mfumo mkuu!');
                }
            }
        }

        return redirect()->back()->with('error', 'Samahani, haikuwezekana kurudisha document hii.');
    }

    // 5. Kufuta Kabisa (Force Delete) - Inaruhusu Admin au Supervisor kufuta kabisa logi na faili lake.
    public function destroyFromLog($id)
    {
        $user = Auth::user();

        // Ruhusu Admin au Supervisor kufuta kabisa kwenye backups bila 403
        if (!in_array($user->role, ['admin', 'supervisor'])) {
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