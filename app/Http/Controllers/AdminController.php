<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Collector;
use App\Models\admin_log;
use App\Models\admin_timeinout;
use App\Models\admin_activity;
use App\Models\admin_audit_history;
use App\Models\waste_segregated;
use App\Models\amount_paid;
use App\Models\total_registered;
use App\Models\User;
use App\Models\Userinfo;
use App\Models\userfine;
use App\Models\collector_log;
use App\Models\collector_audit;
use App\Models\collector_audit_history;
use App\Models\pickup_request;
use App\Models\change_credential_request;
use App\Models\Purok;
use App\Models\Notification;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class AdminController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Helper method to paginate a collection
     */
    protected function paginateCollection($collection, $perPage = 10, $request = null)
    {
        $request = $request ?? request();
        $page = $request->get('page', 1);
        $total = $collection->count();
        $items = $collection->forPage($page, $perPage)->values();
        
        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    public function adminnlogin(){
        return view('admin.adminlogin');
    }
    
    public function showLoginForm()
    {
        return view('admin.adminlogin');
    }

    public function showDashboard()
    {
        // Query for admin activities (get collection)
        $adminActivities = admin_activity::take(5);

        // Fetch waste segregated data
        $wasteSegregated = waste_segregated::all()->first();
        
        // Get total amounts and users
        $totalamount = amount_paid::getTotal();
        $totalUsers = total_registered::getTotalCount();
        
        // Get chart data for user registrations
        $currentYear = date('Y');
        $currentMonth = 5; // May
        $adminchart = Userinfo::getChartData($currentYear, $currentMonth);
        
        // Build chart data for Google Charts
        $chartData = [['Day', 'Users']];
        for ($i = 1; $i <= 31; $i++) {
            $chartData[] = ["May $i", $adminchart[$i] ?? 0];
        }

        // Ensure variables exist even if empty
        if (!$adminActivities) {
            $adminActivities = collect([]);
        }

        $pageTitle = 'Admin Dashboard';
        return view('admin.admindashboard', compact(
            'adminActivities',
            'wasteSegregated',
            'totalamount',
            'totalUsers',
            'chartData',
            'pageTitle'
        ));
    }

    public function adminlogs(Request $request)
    {
        // Fetch all admin logs from Firebase
        $allLogs = admin_log::all();
        
        // Sort by most recent admin_timein first (descending)
        $sortedLogs = $allLogs->sortByDesc(function ($log) {
            $timeIn = $log->admin_timein ?? '';
            // Convert to timestamp for sorting, if empty use 0 (will sort to bottom)
            if (empty($timeIn)) {
                return 0;
            }
            return strtotime($timeIn);
        })->values();
        
        // Apply search filter if provided
        $search = $request->get('search');
        if ($search) {
            $sortedLogs = $sortedLogs->filter(function ($log) use ($search) {
                $lastName = strtolower($log->last_name ?? '');
                $username = strtolower($log->admin_username ?? '');
                $cellNumber = strtolower($log->cell_number ?? '');
                $searchLower = strtolower($search);
                return str_contains($lastName, $searchLower) || 
                       str_contains($username, $searchLower) ||
                       str_contains($cellNumber, $searchLower);
            })->values();
        }

        // Paginate the results (10 per page)
        $logs = $this->paginateCollection($sortedLogs, 10, $request);

        // Get total admins count
        $totalAdmins = (object)['total_admins' => Admin::count()];

        $pageTitle = 'Admin Logs';
        return view('adminlogs', compact('logs', 'pageTitle', 'totalAdmins'));
    }

    public function adminaudit(Request $request)
    {
        // Fetch all audit records from Firebase
        $allAudits = admin_audit_history::all();
        
        // Get all admins to build a lookup map by admin_id
        $admins = Admin::all();
        $adminMap = [];
        foreach ($admins as $admin) {
            $adminId = $admin->key ?? null;
            if ($adminId) {
                $adminMap[$adminId] = $admin;
            }
        }
        
        // Process audits: match admins by admin_id and enrich audit data
        $processedAudits = $allAudits->map(function ($audit) use ($adminMap) {
            // Get admin info using admin_id from the audit
            $auditAdminId = $audit->admin_id ?? null;
            $admin = $adminMap[$auditAdminId] ?? null;
            
            // Set firstname and lastname from admin, or N/A if not found
            if ($admin) {
                $audit->firstname = $admin->admin_fname ?? 'N/A';
                $audit->lastname = $admin->admin_lname ?? 'N/A';
            } else {
                $audit->firstname = 'N/A';
                $audit->lastname = 'N/A';
            }
            
            // Get action_performed and created_at from audit record
            $audit->action_performed = $audit->action_performed ?? 'N/A';
            $audit->performed_on = $audit->created_at ?? null;
            
            return $audit;
        })->filter(function ($audit) {
            // Only include audits that have an admin_id
            return isset($audit->admin_id) && $audit->admin_id;
        });
        
        // Sort by most recent created_at first (descending)
        $sortedAudits = $processedAudits->sortByDesc(function ($audit) {
            $createdAt = $audit->performed_on ?? '';
            // Convert to timestamp for sorting, if empty use 0 (will sort to bottom)
            if (empty($createdAt)) {
                return 0;
            }
            return strtotime($createdAt);
        })->values();
        
        // Note: Search filtering will be done client-side via live search

        // Paginate the results (10 per page)
        $audits = $this->paginateCollection($sortedAudits, 10, $request);

        // Get total count of audit records
        $totalAudits = $allAudits->count();

        $pageTitle = 'Admin Audit Trail';
        return view('adminaudit', compact('audits', 'pageTitle', 'totalAudits'));
    }

    public function adminlogin(Request $request)
    {
        $credentials = $request->validate([
            'admin_username' => 'required|string',
            'admin_password' => 'required|string',
        ]);

        $admin = Admin::findByUsername($credentials['admin_username']);

        if ($admin && $admin->verifyPassword($credentials['admin_password'])) {
            $request->session()->regenerate();
            session(['admin_id' => $admin->key]);

            // Log admin time-in - store the actual current timestamp
            // Use now() which respects the app timezone setting
            $logData = [
                'admin_id' => $admin->key,
                'admin_timein' => now()->toDateTimeString(), // Store real current time
                'admin_timeout' => null,
                'last_name' => $admin->admin_lname ?? '',
                'cell_number' => $admin->admin_cell ?? '',
                'admin_username' => $admin->admin_username ?? ''
            ];

            $log = admin_log::create($logData);

            session(['admin_log_id' => $log->key]);

            return redirect()->route('admin.admindashboard');
        }

        return back()->withErrors([
            'admin_username' => 'Invalid username or password.',
        ])->onlyInput('admin_username');
    }

    public function admindashboard(){
        // Get data for dashboard
        $adminActivities = admin_activity::take(5);
        
        // Calculate total waste disposed from pickup requests collected today
        $allPickupRequests = pickup_request::all();
        $currentDate = now()->toDateString(); // Format: Y-m-d
        $totalWasteDisposedToday = 0;
        
        foreach ($allPickupRequests as $request) {
            // Check if request is collected and collected_at is today
            if (isset($request->collected) && $request->collected === true) {
                $collectedAt = $request->collected_at ?? null;
                if ($collectedAt) {
                    try {
                        // Parse the collected_at date and compare with current date
                        $collectedDate = \Carbon\Carbon::parse($collectedAt)->toDateString();
                        if ($collectedDate === $currentDate) {
                            // Sum the sacks_dumped field
                            $sacksDumped = $request->sacks_dumped ?? 0;
                            $totalWasteDisposedToday += intval($sacksDumped);
                        }
                    } catch (\Exception $e) {
                        // Skip invalid dates
                        continue;
                    }
                }
            }
        }
        
        $wasteSegregated = (object)['total_waste_segregated' => $totalWasteDisposedToday];
        
        // Calculate total penalty payments from fines paid today
        $allFines = userfine::all();
        $currentDate = now()->toDateString(); // Format: Y-m-d
        $totalPaidToday = 0;
        
        foreach ($allFines as $fine) {
            // Check if fine is paid and paid_on is today
            if (isset($fine->paid) && $fine->paid === true) {
                $paidOn = $fine->paid_on ?? null;
                if ($paidOn) {
                    try {
                        // Parse the paid_on date and compare with current date
                        $paidDate = \Carbon\Carbon::parse($paidOn)->toDateString();
                        if ($paidDate === $currentDate) {
                            // Add the fine amount
                            $fineAmount = $fine->fines ?? 0;
                            $totalPaidToday += floatval($fineAmount);
                        }
                    } catch (\Exception $e) {
                        // Skip invalid dates
                        continue;
                    }
                }
            }
        }
        
        $totalamount = (object)['total_amount' => $totalPaidToday];
        $totalUsers = total_registered::getTotalCount();
        
        // Get chart data - all users registered in current month
        $allUsers = User::all();
        $currentYear = date('Y');
        $currentMonth = date('n'); // Current month (1-12)
        $monthName = date('F'); // Full month name (e.g., "January", "February")
        
        // Get number of days in current month
        $daysInMonth = date('t'); // Number of days in current month
        
        // Initialize day counts for current month
        $dayCounts = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dayCounts[$i] = 0;
        }
        
        // Count users registered in current month by day
        foreach ($allUsers as $user) {
            $createdAt = $user->created_at ?? null;
            if ($createdAt) {
                try {
                    $date = \Carbon\Carbon::parse($createdAt);
                    if ($date->year == $currentYear && $date->month == $currentMonth) {
                        $day = $date->day;
                        if (isset($dayCounts[$day])) {
                            $dayCounts[$day]++;
                        }
                    }
                } catch (\Exception $e) {
                    // Skip invalid dates
                    continue;
                }
            }
        }
        
        // Build chart data
        $chartData = [['Day', 'Users']];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $chartData[] = [$monthName . " $i", $dayCounts[$i] ?? 0];
        }

        // Ensure variables exist even if empty
        if (!$adminActivities) {
            $adminActivities = collect([]);
        }

        $pageTitle = 'Admin Dashboard';
        return view('admin.admindashboard', compact(
            'adminActivities',
            'wasteSegregated',
            'totalamount',
            'totalUsers',
            'chartData',
            'pageTitle'
        ));
    }

    public function logout(Request $request)
    {
        // Retrieve the admin_log_id from session
        $adminLogKey = session('admin_log_id');

        // Update the logout time in the admin_logs - store the actual current timestamp
        if ($adminLogKey) {
            $log = admin_log::find($adminLogKey);
            if ($log) {
                $log->admin_timeout = now()->toDateTimeString(); // Store real current time
                $log->save();
            }
        }

        // Clear session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to login page
        return redirect()->route('admin.login');
    }

    public function registerCollector(Request $request)
    {
        $request->validate([
            'coll_fname' => 'required|string|max:255',
            'coll_lname' => 'required|string|max:255',
            'collcell_num' => 'required|string|max:20',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        try {
            // Check if username already exists in Realtime Database
            $existing = Collector::findByUsername($request->username);
            if ($existing) {
                return back()->withErrors(['username' => 'Username already exists.'])->withInput();
            }

            // Check if email already exists in Firebase Auth
            $existingAuthUser = $this->firebase->getAuthUserByEmail($request->email);
            if ($existingAuthUser) {
                return back()->withErrors(['email' => 'Email already registered.'])->withInput();
            }

            // Get current admin from session
            $adminId = session('admin_id');
            if (!$adminId) {
                return back()->withErrors(['error' => 'Admin session not found'])->withInput();
            }

            // Create collector with Firebase Auth
            $collector = Collector::createCollector([
                'coll_fname' => $request->coll_fname,
                'coll_lname' => $request->coll_lname,
                'collcell_num' => $request->collcell_num,
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            // Get collector's firebase_uid
            $collectorUid = $collector->firebase_uid ?? null;

            // Create admin audit record
            admin_audit_history::create([
                'admin_id' => $adminId,
                'action' => 'registering',
                'action_performed' => 'Registered a collector',
                'uid' => $collectorUid,
                'created_at' => now()->toDateTimeString(),
            ]);

            return back()->with('register_success', true);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to register collector: ' . $e->getMessage()])->withInput();
        }
    }

    public function userfines(Request $request)
    {
        // Fetch all fines from Firebase
        $allFines = userfine::all();
        
        // Get all users to build a lookup map by uid
        $users = User::all();
        $userMap = [];
        foreach ($users as $user) {
            $uid = $user->uid ?? $user->key ?? null;
            if ($uid) {
                $userMap[$uid] = $user;
            }
        }
        
        // Process fines: match users by uid and enrich fine data
        $processedFines = $allFines->map(function ($fine) use ($userMap) {
            // Get user info using uid from the fine
            $fineUid = $fine->uid ?? null;
            $user = $userMap[$fineUid] ?? null;
            
            // Set firstname and lastname from user, or N/A if not found
            if ($user) {
                $fine->firstname = $user->user_fname ?? 'N/A';
                $fine->lastname = $user->user_lname ?? 'N/A';
                $fine->purok = $user->purok ?? 'N/A';
                $fine->household_number = $user->household_number ?? 'N/A';
            } else {
                $fine->firstname = 'N/A';
                $fine->lastname = 'N/A';
                $fine->purok = 'N/A';
                $fine->household_number = 'N/A';
            }
            
            // Get amount from "fines" field
            $fine->amount = $fine->fines ?? 0;
            
            // Get date from "created_at" field
            $fine->date = $fine->created_at ?? null;
            
            return $fine;
        })->filter(function ($fine) {
            // Only include fines that have a uid
            return isset($fine->uid) && $fine->uid;
        });

        // Calculate total paid and unpaid fines BEFORE filtering (for cards)
        $totalPaidFines = $processedFines->filter(function ($fine) {
            return isset($fine->paid) && $fine->paid === true;
        })->count();
        
        $totalUnpaidFines = $processedFines->filter(function ($fine) {
            return !isset($fine->paid) || $fine->paid === false || $fine->paid === null;
        })->count();
        
        // Apply payment status filter (default to unpaid)
        $paymentFilter = $request->get('payment_status', 'unpaid'); // Default to 'unpaid'
        if ($paymentFilter === 'paid') {
            // Show only paid fines
            $processedFines = $processedFines->filter(function ($fine) {
                return isset($fine->paid) && $fine->paid === true;
            })->values();
        } elseif ($paymentFilter === 'all') {
            // Show all fines (no filter)
            // Keep all fines as is
        } else {
            // Default: show unpaid fines (paid is not set or is false)
            $processedFines = $processedFines->filter(function ($fine) {
                return !isset($fine->paid) || $fine->paid === false || $fine->paid === null;
            })->values();
        }

        // Paginate the results (10 per page)
        // If filter is "all", don't paginate so live search can work on all data
        if ($paymentFilter === 'all') {
            // Return all fines without pagination for live search
            // Create a paginator-like object for compatibility
            $fines = $processedFines->values();
        } else {
            // Paginate for unpaid/paid filters
            $fines = $this->paginateCollection($processedFines, 10, $request);
        }

        $pageTitle = 'User Fines';
        return view('userfines', compact('fines', 'pageTitle', 'paymentFilter', 'totalPaidFines', 'totalUnpaidFines'));
    }

    public function deleteUserFine(Request $request)
    {
        $fineKey = $request->input('key');
        $fine = userfine::find($fineKey);
        
        if ($fine) {
            $fine->delete();
            return back()->with('success', 'Fine deleted successfully');
        }
        
        return back()->withErrors(['error' => 'Fine not found']);
    }

    public function getFineDetails($fineKey)
    {
        try {
            $fine = userfine::find($fineKey);
            
            if (!$fine) {
                return response()->json(['violation_details' => 'N/A']);
            }
            
            // Ensure key is set
            if (!$fine->key) {
                $fine->key = $fineKey;
            }
            
            // Get violation details from pickup_requests using request_id
            $requestId = $fine->request_id ?? null;
            $violationDetails = 'N/A';
            
            if ($requestId) {
                // Try to find the pickup request by request_id
                $pickupRequest = pickup_request::find($requestId);
                if ($pickupRequest) {
                    // Get violation details - check common field names
                    $violationDetails = $pickupRequest->violation_details ?? 
                                       $pickupRequest->details ?? 
                                       $pickupRequest->reason ?? 
                                       $pickupRequest->violation_reason ??
                                       'N/A';
                } else {
                    // If not found by key, try searching all pickup requests
                    $allRequests = pickup_request::all();
                    $matchingRequest = $allRequests->firstWhere('key', $requestId);
                    if ($matchingRequest) {
                        $violationDetails = $matchingRequest->violation_details ?? 
                                           $matchingRequest->details ?? 
                                           $matchingRequest->reason ?? 
                                           $matchingRequest->violation_reason ??
                                           'N/A';
                    }
                }
            }
            
            return response()->json(['violation_details' => $violationDetails]);
        } catch (\Exception $e) {
            return response()->json(['violation_details' => 'N/A', 'error' => $e->getMessage()]);
        }
    }

    public function markFineAsPaid(Request $request)
    {
        try {
            $fineKey = $request->input('fine_key');
            $fine = userfine::find($fineKey);
            
            if (!$fine) {
                return back()->withErrors(['error' => 'Fine not found']);
            }
            
            // Check if already paid
            if (isset($fine->paid) && $fine->paid) {
                return back()->withErrors(['error' => 'This fine is already marked as paid']);
            }
            
            // Get current admin from session
            $adminId = session('admin_id');
            if (!$adminId) {
                return back()->withErrors(['error' => 'Admin session not found']);
            }
            
            // Get user uid from fine before updating
            $userUid = $fine->uid ?? null;
            
            // Get fine amount
            $fineAmount = $fine->fines ?? 0;

            // Mark fine as paid
            $fine->paid = true;
            $fine->paid_on = now()->toDateTimeString();
            $fine->save();
            
            // Create admin audit record
            admin_audit_history::create([
                'admin_id' => $adminId,
                'action' => 'paid',
                'action_performed' => 'Marked a fine as paid',
                'uid' => $userUid,
                'created_at' => now()->toDateTimeString(),
            ]);

            // Create notification for the user
            if ($userUid) {
                Notification::create([
                    'message' => "Your fine of ₱" . number_format($fineAmount, 2) . " has been paid",
                    'recipient' => $userUid,
                    'created_at' => now()->toDateTimeString(),
                ]);
            }
            
            return back()->with('success', 'Fine marked as paid successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to mark fine as paid: ' . $e->getMessage()]);
        }
    }

    public function collectorlogs(Request $request)
    {
        // Fetch all collector logs from Firebase
        $allLogs = collector_log::all();
        
        // Fetch all collectors to build a lookup map by firebase_uid
        $collectors = Collector::all();
        $collectorMap = [];
        foreach ($collectors as $collector) {
            // Handle both cases: firebase_uid as field or as key
            $uid = $collector->firebase_uid ?? $collector->key ?? null;
            if ($uid) {
                $collectorMap[$uid] = $collector;
            }
        }
        
        // Process logs: match collectors by UID and enrich log data
        $processedLogs = $allLogs->map(function ($log) use ($collectorMap) {
            // Get collector info using collector_uid from the log
            $collectorUid = $log->collector_uid ?? null;
            $collector = $collectorMap[$collectorUid] ?? null;
            
            // Set firstname and lastname from collector, or N/A if not found
            if ($collector) {
                $log->firstname = $collector->coll_fname ?? 'N/A';
                $log->lastname = $collector->coll_lname ?? 'N/A';
            } else {
                $log->firstname = 'N/A';
                $log->lastname = 'N/A';
            }
            
            // Use collector_timein and collector_timeout from the log
            $log->time_in = $log->collector_timein ?? null;
            $log->time_out = $log->collector_timeout ?? null;
            
            return $log;
        })->filter(function ($log) {
            // Only include logs that have a collector_uid
            return isset($log->collector_uid) && $log->collector_uid;
        });
        
        // Sort by most recent collector_timein first (descending)
        $sortedLogs = $processedLogs->sortByDesc(function ($log) {
            $timeIn = $log->collector_timein ?? '';
            // Convert to timestamp for sorting, if empty use 0 (will sort to bottom)
            if (empty($timeIn)) {
                return 0;
            }
            return strtotime($timeIn);
        })->values();
        
        // Apply search filter if provided
        $search = $request->get('search');
        if ($search) {
            $sortedLogs = $sortedLogs->filter(function ($log) use ($search) {
                $firstName = strtolower($log->firstname ?? '');
                $lastName = strtolower($log->lastname ?? '');
                $searchLower = strtolower($search);
                return str_contains($firstName, $searchLower) || 
                       str_contains($lastName, $searchLower);
            })->values();
        }

        // Paginate the results (10 per page)
        $logs = $this->paginateCollection($sortedLogs, 10, $request);

        $pageTitle = 'Collector Logs';
        return view('collectorlogs', compact('logs', 'pageTitle'));
    }

    public function collectoraudit(Request $request)
    {
        // Fetch all audit records from Firebase
        $allAudits = collector_audit_history::all();
        
        // Get all collectors to build a lookup map
        $collectors = Collector::all();
        $collectorMap = [];
        foreach ($collectors as $collector) {
            // Map by key (collector's Firebase key)
            $collectorId = $collector->key ?? null;
            if ($collectorId) {
                $collectorMap[$collectorId] = $collector;
            }
            // Also map by firebase_uid
            $firebaseUid = $collector->firebase_uid ?? null;
            if ($firebaseUid && $firebaseUid !== $collectorId) {
                $collectorMap[$firebaseUid] = $collector;
            }
        }
        
        // Get all users to build a lookup map by uid
        $users = User::all();
        $userMap = [];
        foreach ($users as $user) {
            $uid = $user->uid ?? $user->key ?? null;
            if ($uid) {
                $userMap[$uid] = $user;
            }
        }
        
        // Process audits: match collectors and users, enrich audit data
        $processedAudits = $allAudits->map(function ($audit) use ($collectorMap, $userMap) {
            // Get collector info using collector_uid from the audit
            $auditCollectorUid = $audit->collector_uid ?? null;
            $collector = $collectorMap[$auditCollectorUid] ?? null;
            
            // Set collector firstname and lastname
            if ($collector) {
                $audit->collector_firstname = $collector->coll_fname ?? 'N/A';
                $audit->collector_lastname = $collector->coll_lname ?? 'N/A';
            } else {
                $audit->collector_firstname = 'N/A';
                $audit->collector_lastname = 'N/A';
            }
            
            // Get user info using user_uid from the audit
            $auditUserUid = $audit->user_uid ?? null;
            $user = $userMap[$auditUserUid] ?? null;
            
            // Set user firstname and lastname
            if ($user) {
                $audit->user_firstname = $user->user_fname ?? 'N/A';
                $audit->user_lastname = $user->user_lname ?? 'N/A';
            } else {
                $audit->user_firstname = 'N/A';
                $audit->user_lastname = 'N/A';
            }
            
            // For table display, show collector names (as it's collector audit)
            $audit->firstname = $audit->collector_firstname;
            $audit->lastname = $audit->collector_lastname;
            
            // Get action_performed and created_at from audit record
            $audit->action_performed = $audit->action_performed ?? 'N/A';
            $audit->performed_on = $audit->created_at ?? null;
            
            return $audit;
        });
        
        // Sort by most recent created_at first (descending)
        $sortedAudits = $processedAudits->sortByDesc(function ($audit) {
            $createdAt = $audit->performed_on ?? '';
            // Convert to timestamp for sorting, if empty use 0 (will sort to bottom)
            if (empty($createdAt)) {
                return 0;
            }
            return strtotime($createdAt);
        })->values();
        
        // Note: Search filtering will be done client-side via live search

        // Paginate the results (10 per page)
        $audits = $this->paginateCollection($sortedAudits, 10, $request);

        // Get total count of audit records
        $totalAudits = $allAudits->count();

        $pageTitle = 'Collector Audit Trail';
        return view('collectoraudit', compact('audits', 'pageTitle', 'totalAudits'));
    }

    public function userdetails(Request $request)
    {
        $allUsers = User::all();
        
        // Get purok filter from request
        $purokFilter = $request->get('purok', 'all');
        
        // Filter by purok if selected
        if ($purokFilter !== 'all') {
            $allUsers = $allUsers->filter(function($user) use ($purokFilter) {
                return ($user->purok ?? '') === $purokFilter;
            });
        }
        
        // Fetch emails from Firebase Auth for each user
        foreach ($allUsers as $user) {
            if (isset($user->uid) && $user->uid) {
                try {
                    $authUser = $this->firebase->getAuthUser($user->uid);
                    if ($authUser) {
                        $user->email = $authUser->email;
                    }
                } catch (\Exception $e) {
                    $user->email = null;
                }
            }
        }
        
        // Paginate the results (10 per page)
        $users = $this->paginateCollection($allUsers, 10, $request);
        
        // Get all puroks for the filter dropdown
        $allPuroks = Purok::all();
        
        // Get total users count
        $totalUsers = total_registered::getTotalCount();
        
        $pageTitle = 'User Details';
        return view('userdetails', compact('users', 'pageTitle', 'totalUsers', 'allPuroks', 'purokFilter'));
    }

    public function collectordetails(Request $request)
    {
        $allCollectors = Collector::all();
        
        // Paginate the results (10 per page)
        $collectors = $this->paginateCollection($allCollectors, 10, $request);
        
        // Get total collectors count
        $totalCollectors = (object)['total_collectors' => Collector::count()];
        
        $pageTitle = 'Collector Details';
        return view('collectordetails', compact('collectors', 'pageTitle', 'totalCollectors'));
    }

    public function deleteUser($key)
    {
        try {
            $user = User::find($key);
            
            if (!$user) {
                return back()->withErrors(['error' => 'User not found']);
            }

            // Get current admin from session
            $adminId = session('admin_id');
            if (!$adminId) {
                return back()->withErrors(['error' => 'Admin session not found']);
            }

            // Get user uid before deletion
            $userUid = $user->uid ?? null;

            // Delete from Firebase
            $user->delete();

            // Create admin audit record
            admin_audit_history::create([
                'admin_id' => $adminId,
                'action' => 'deletion',
                'action_performed' => 'Deleted a user',
                'uid' => $userUid,
                'created_at' => now()->toDateTimeString(),
            ]);

            return back()->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
    }

    public function updateUser(Request $request, $key)
    {
        try {
            $user = User::find($key);
            
            if (!$user) {
                return back()->withErrors(['error' => 'User not found']);
            }

            // Validate input
            $validated = $request->validate([
                'user_fname' => 'required|string|max:255',
                'user_lname' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'purok' => 'nullable|string|max:255',
                'household_number' => 'nullable|string|max:255',
                'cellphone' => 'nullable|string|max:255',
            ]);

            // Get current admin from session
            $adminId = session('admin_id');
            if (!$adminId) {
                return back()->withErrors(['error' => 'Admin session not found']);
            }

            // Get current email from Firebase Auth
            $currentEmail = null;
            if (isset($user->uid) && $user->uid) {
                try {
                    $authUser = $this->firebase->getAuthUser($user->uid);
                    if ($authUser) {
                        $currentEmail = $authUser->email;
                    }
                } catch (\Exception $e) {
                    // User might not exist in Auth
                }
            }

            // Update email in Firebase Auth if it changed
            if (isset($user->uid) && $user->uid && $currentEmail && $currentEmail !== $validated['email']) {
                try {
                    $this->firebase->updateAuthUserEmail($user->uid, $validated['email']);
                } catch (\Exception $e) {
                    return back()->withErrors(['error' => 'Failed to update email: ' . $e->getMessage()]);
                }
            }

            // Update user record in Firebase
            $updateData = [
                'user_fname' => $validated['user_fname'],
                'user_lname' => $validated['user_lname'],
                'purok' => $validated['purok'] ?? null,
                'household_number' => $validated['household_number'] ?? null,
                'cellphone' => $validated['cellphone'] ?? null,
            ];

            foreach ($updateData as $field => $value) {
                $user->$field = $value;
            }
            $user->save();

            // Get user uid for notification
            $userUid = $user->uid ?? null;

            // Create admin audit record
            admin_audit_history::create([
                'admin_id' => $adminId,
                'action' => 'edit',
                'action_performed' => 'Edited user details',
                'uid' => $userUid,
                'created_at' => now()->toDateTimeString(),
            ]);

            // Create notification for the user
            if ($userUid) {
                Notification::create([
                    'message' => "Admin has edited your user details",
                    'recipient' => $userUid,
                    'created_at' => now()->toDateTimeString(),
                ]);
            }

            // Preserve the purok filter in redirect
            $purokFilter = $request->get('page_purok_filter', request('purok', 'all'));
            return redirect()->route('userdetails', ['purok' => $purokFilter])->with('success', 'User details updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    public function deleteCollector($key)
    {
        try {
            $collector = Collector::find($key);
            
            if (!$collector) {
                return back()->withErrors(['error' => 'Collector not found']);
            }

            // Get current admin from session
            $adminId = session('admin_id');
            if (!$adminId) {
                return back()->withErrors(['error' => 'Admin session not found']);
            }

            // Get collector firebase_uid before deletion
            $collectorUid = $collector->firebase_uid ?? null;

            // Delete from Firebase Auth if firebase_uid exists
            if (isset($collector->firebase_uid) && $collector->firebase_uid) {
                $this->firebase->deleteAuthUser($collector->firebase_uid);
            }

            // Delete from Firebase Realtime Database
            $collector->delete();

            // Create admin audit record
            admin_audit_history::create([
                'admin_id' => $adminId,
                'action' => 'deletion',
                'action_performed' => 'Deleted a collector',
                'uid' => $collectorUid,
                'created_at' => now()->toDateTimeString(),
            ]);

            return back()->with('success', 'Collector deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete collector: ' . $e->getMessage()]);
        }
    }

    public function updateCollector(Request $request, $key)
    {
        try {
            $collector = Collector::find($key);
            
            if (!$collector) {
                return back()->withErrors(['error' => 'Collector not found']);
            }

            // Validate input
            $validated = $request->validate([
                'coll_fname' => 'required|string|max:255',
                'coll_lname' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'collcell_num' => 'nullable|string|max:255',
                'username' => 'nullable|string|max:255',
            ]);

            // Get current admin from session
            $adminId = session('admin_id');
            if (!$adminId) {
                return back()->withErrors(['error' => 'Admin session not found']);
            }

            // Get current email from Firebase Auth
            $currentEmail = null;
            if (isset($collector->firebase_uid) && $collector->firebase_uid) {
                try {
                    $authUser = $this->firebase->getAuthUser($collector->firebase_uid);
                    if ($authUser) {
                        $currentEmail = $authUser->email;
                    }
                } catch (\Exception $e) {
                    // User might not exist in Auth
                }
            }

            // Update email in Firebase Auth if it changed
            if (isset($collector->firebase_uid) && $collector->firebase_uid && $currentEmail && $currentEmail !== $validated['email']) {
                try {
                    $this->firebase->updateAuthUserEmail($collector->firebase_uid, $validated['email']);
                } catch (\Exception $e) {
                    return back()->withErrors(['error' => 'Failed to update email: ' . $e->getMessage()]);
                }
            }

            // Update collector record in Firebase
            $updateData = [
                'coll_fname' => $validated['coll_fname'],
                'coll_lname' => $validated['coll_lname'],
                'email' => $validated['email'],
                'collcell_num' => $validated['collcell_num'] ?? null,
                'username' => $validated['username'] ?? null,
            ];

            foreach ($updateData as $field => $value) {
                $collector->$field = $value;
            }
            $collector->save();

            // Create admin audit record
            admin_audit_history::create([
                'admin_id' => $adminId,
                'action' => 'edit',
                'action_performed' => 'Edited collector details',
                'uid' => $collector->firebase_uid ?? null,
                'created_at' => now()->toDateTimeString(),
            ]);

            return back()->with('success', 'Collector details updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update collector: ' . $e->getMessage()]);
        }
    }

    public function changeCredentialRequests(Request $request)
    {
        $allRequests = change_credential_request::all();
        
        // Calculate total pending requests (always show this in the card)
        $totalPendingRequests = $allRequests->filter(function($req) {
            $status = $req->status ?? null;
            return $status !== 'accepted' && $status !== 'rejected';
        })->count();
        
        // Get status filter from request, default to 'pending'
        $statusFilter = $request->get('status', 'pending');
        
        // Filter requests by status
        $filteredRequests = $allRequests->filter(function($req) use ($statusFilter) {
            $status = $req->status ?? null;
            
            if ($statusFilter === 'all') {
                return true; // Show all requests
            } elseif ($statusFilter === 'pending') {
                // Show requests that don't have a status or have status that's not accepted/rejected
                return $status !== 'accepted' && $status !== 'rejected';
            } else {
                // Show requests with the specific status (accepted or rejected)
                return $status === $statusFilter;
            }
        });
        
        // Sort by created_at descending (most recent first)
        $sortedRequests = $filteredRequests->sortByDesc(function($req) {
            return $req->created_at ?? $req->timestamp ?? '';
        })->values();
        
        // Enrich with user/collector details
        foreach ($sortedRequests as $req) {
            $uid = $req->uid ?? $req->user_id ?? null;
            $userType = $req->user_type ?? 'user'; // 'user' or 'collector'
            
            if ($uid) {
                if ($userType === 'collector') {
                    // Collectors are stored with firebase_uid as the key, so try find() first
                    $collector = Collector::find($uid);
                    if (!$collector) {
                        // Fallback: search all collectors if uid is not the key
                        $allCollectors = Collector::all();
                        $collector = $allCollectors->first(function($c) use ($uid) {
                            return ($c->firebase_uid ?? null) === $uid || ($c->key ?? null) === $uid;
                        });
                    }
                    if ($collector) {
                        $req->firstname = $collector->coll_fname ?? 'N/A';
                        $req->lastname = $collector->coll_lname ?? 'N/A';
                        $req->email = $collector->email ?? 'N/A';
                    }
                } else {
                    // Try to find user by uid - search all users since FirebaseModel's where may not work with uid field
                    $allUsers = User::all();
                    $user = $allUsers->first(function($u) use ($uid) {
                        return ($u->uid ?? null) === $uid;
                    });
                    if ($user) {
                        $req->firstname = $user->user_fname ?? 'N/A';
                        $req->lastname = $user->user_lname ?? 'N/A';
                        try {
                            $authUser = $this->firebase->getAuthUser($uid);
                            $req->email = $authUser ? $authUser->email : 'N/A';
                        } catch (\Exception $e) {
                            $req->email = 'N/A';
                        }
                    }
                }
            }
        }
        
        // Paginate the results (10 per page)
        $requests = $this->paginateCollection($sortedRequests, 10, $request);
        
        $pageTitle = 'Change Credential Requests';
        return view('changecredentialrequests', compact('requests', 'pageTitle', 'statusFilter', 'totalPendingRequests'));
    }

    public function acceptCredentialRequest(Request $request)
    {
        try {
            $requestKey = $request->input('request_key');
            $credentialRequest = change_credential_request::find($requestKey);
            
            if (!$credentialRequest) {
                return back()->withErrors(['error' => 'Request not found']);
            }
            
            // Check if already processed
            $status = $credentialRequest->status ?? null;
            if ($status === 'accepted' || $status === 'rejected') {
                return back()->withErrors(['error' => 'This request has already been processed']);
            }
            
            // Get current admin from session
            $adminId = session('admin_id');
            if (!$adminId) {
                return back()->withErrors(['error' => 'Admin session not found']);
            }
            
            $uid = $credentialRequest->uid ?? $credentialRequest->user_id ?? null;
            $userType = $credentialRequest->user_type ?? 'user';
            $newCredential = $credentialRequest->new_credential ?? $credentialRequest->new_value ?? null;
            $credentialType = $credentialRequest->credential_type ?? null;
            
            // Check if this is a user detail change request (has user detail fields)
            $hasUserDetails = isset($credentialRequest->user_fname) || 
                             isset($credentialRequest->user_lname) || 
                             isset($credentialRequest->purok) || 
                             isset($credentialRequest->household_number) || 
                             isset($credentialRequest->cellphone);
            
            // Validate: must have uid and either a credential to update OR user details to update
            if (!$uid || (!$newCredential && !$hasUserDetails)) {
                return back()->withErrors(['error' => 'Invalid request data: missing uid or update fields']);
            }
            
            // Update the credential in Firebase Auth (only if credential change is requested)
            if ($newCredential && $credentialType) {
                if ($credentialType === 'email') {
                    $this->firebase->updateAuthUserEmail($uid, $newCredential);
                } elseif ($credentialType === 'password') {
                    $this->firebase->updateAuthUserPassword($uid, $newCredential);
                }
            }
            
            // Update user record in Firebase with request details
            if ($userType === 'user' && $uid) {
                // Find user by uid
                $allUsers = User::all();
                $user = $allUsers->first(function($u) use ($uid) {
                    return ($u->uid ?? null) === $uid;
                });
                
                if ($user) {
                    // Update user fields from the request
                    $updateData = [];
                    
                    if (isset($credentialRequest->user_fname)) {
                        $updateData['user_fname'] = $credentialRequest->user_fname;
                    }
                    if (isset($credentialRequest->user_lname)) {
                        $updateData['user_lname'] = $credentialRequest->user_lname;
                    }
                    if (isset($credentialRequest->purok)) {
                        $updateData['purok'] = $credentialRequest->purok;
                    }
                    if (isset($credentialRequest->household_number)) {
                        $updateData['household_number'] = $credentialRequest->household_number;
                    }
                    if (isset($credentialRequest->cellphone)) {
                        $updateData['cellphone'] = $credentialRequest->cellphone;
                    }
                    
                    // Update user if there's data to update
                    if (!empty($updateData)) {
                        foreach ($updateData as $key => $value) {
                            $user->$key = $value;
                        }
                        $user->save();
                    }
                }
            }
            
            // Update request status
            $credentialRequest->status = 'accepted';
            $credentialRequest->processed_at = now()->toDateTimeString();
            $credentialRequest->processed_by = $adminId;
            $credentialRequest->save();
            
            // Create admin audit record
            admin_audit_history::create([
                'admin_id' => $adminId,
                'action' => 'accepted request',
                'action_performed' => "Accepted user detail change request",
                'uid' => $uid,
                'created_at' => now()->toDateTimeString(),
            ]);
            
            // Preserve the status filter in redirect
            $statusFilter = request()->get('status', 'pending');
            return redirect()->route('change.credential.requests', ['status' => $statusFilter])
                ->with('success', 'Credential request accepted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to accept request: ' . $e->getMessage()]);
        }
    }

    public function rejectCredentialRequest(Request $request)
    {
        try {
            $requestKey = $request->input('request_key');
            $credentialRequest = change_credential_request::find($requestKey);
            
            if (!$credentialRequest) {
                return back()->withErrors(['error' => 'Request not found']);
            }
            
            // Check if already processed
            $status = $credentialRequest->status ?? null;
            if ($status === 'accepted' || $status === 'rejected') {
                return back()->withErrors(['error' => 'This request has already been processed']);
            }
            
            // Get current admin from session
            $adminId = session('admin_id');
            if (!$adminId) {
                return back()->withErrors(['error' => 'Admin session not found']);
            }
            
            $uid = $credentialRequest->uid ?? $credentialRequest->user_id ?? null;
            $userType = $credentialRequest->user_type ?? 'user';
            
            // Update request status
            $credentialRequest->status = 'rejected';
            $credentialRequest->processed_at = now()->toDateTimeString();
            $credentialRequest->processed_by = $adminId;
            $credentialRequest->save();
            
            // Create admin audit record
            admin_audit_history::create([
                'admin_id' => $adminId,
                'action' => 'rejected request',
                'action_performed' => "Rejected user detail change request",
                'uid' => $uid,
                'created_at' => now()->toDateTimeString(),
            ]);
            
            // Preserve the status filter in redirect
            $statusFilter = request()->get('status', 'pending');
            return redirect()->route('change.credential.requests', ['status' => $statusFilter])
                ->with('success', 'Credential request rejected successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to reject request: ' . $e->getMessage()]);
        }
    }

    public function getNotifications()
    {
        try {
            // Fetch all notifications from Firebase
            $allNotifications = Notification::all();
            
            // Filter notifications for 'admin' or 'all' recipient
            $notifications = $allNotifications->filter(function($notification) {
                $recipient = $notification->recipient ?? '';
                return $recipient === 'admin' || $recipient === 'all';
            })->sortByDesc(function($notification) {
                $createdAt = $notification->created_at ?? '';
                // Convert to timestamp for sorting, if empty use 0 (will sort to bottom)
                if (empty($createdAt)) {
                    return 0;
                }
                return strtotime($createdAt);
            })->values();
            
            // Count new notifications (those without 'read' field or read = false)
            $newNotificationsCount = $notifications->filter(function($notification) {
                return !isset($notification->read) || $notification->read === false || $notification->read === null;
            })->count();
            
            // Format notifications for response (already sorted by latest first)
            $formattedNotifications = $notifications->map(function($notification) {
                return [
                    'key' => $notification->key,
                    'message' => $notification->message ?? 'N/A',
                    'created_at' => $notification->created_at ?? null,
                    'read' => $notification->read ?? false,
                    'recipient' => $notification->recipient ?? 'all',
                ];
            });
            
            return response()->json([
                'notifications' => $formattedNotifications,
                'new_count' => $newNotificationsCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'notifications' => [],
                'new_count' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function markNotificationAsRead(Request $request)
    {
        try {
            $notificationKey = $request->input('notification_key');
            $notification = Notification::find($notificationKey);
            
            if ($notification) {
                $notification->read = true;
                $notification->save();
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false, 'message' => 'Notification not found']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
