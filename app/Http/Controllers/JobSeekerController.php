<?php

namespace App\Http\Controllers;

use App\Models\JobSeeker;
use App\Models\CompanyMaster;
use App\Models\BranchMaster;
use App\Models\BusinessUnitMaster;
use App\Models\Employee;
use App\Models\StatusMaster;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class JobSeekerController extends Controller
{
    public function index(Request $request, $type)
    {
        try {
            $user = auth()->user();
            $employee = $user->employee;
            $isAdmin = $user->role === 'admin';
            $isMaker = $employee && $employee->role === 'maker';
            $isChecker = $employee && $employee->role === 'checker';
            $isPOMaker = $employee && $employee->role === 'po_maker';
            $isPOChecker = $employee && $employee->role === 'po_checker';
            $isFinanceMaker = $employee && $employee->role === 'finance_maker';
            $isFinanceChecker = $employee && $employee->role === 'finance_checker';
            $isBackoutMaker = $employee && $employee->role === 'backout_maker';
            $isBackoutChecker = $employee && $employee->role === 'backout_checker';

            if (!$isMaker && !$isChecker && !$isAdmin && !$isPOMaker && !$isPOChecker && !$isFinanceMaker && !$isFinanceChecker && !$isBackoutMaker && !$isBackoutChecker) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized']);
            }

            $perPage = $request->input('per_page', 10);
            $search = $request->input('search');
            $statusFilter = $request->input('status_filter', 'Pending');
            $companyId = $request->input('company_id'); // New company_id filter

            $backoutStatus = StatusMaster::where('status', 'Backout')->first();
            $backoutStatusId = $backoutStatus ? $backoutStatus->id : null;
            $joinedStatusId = StatusMaster::where('status', 'Joined')->first()->id;

            $query = JobSeeker::with([
                'client' => fn($q) => $q->select('id', 'client_code', 'client_name', 'qualify_days', 'loaded_cost'),
                'company' => fn($q) => $q->select('id', 'name', 'region'),
                'location' => fn($q) => $q->select('id', 'name'),
                'businessUnit' => fn($q) => $q->select('id', 'unit'),
                'status' => fn($q) => $q->select('id', 'status'),
                'assistantManager' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'deputyManager' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'teamLeader' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'recruiter' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'maker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'checker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'poMaker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'poChecker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'financeMaker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'financeChecker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'backoutMaker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'backoutChecker' => fn($q) => $q->select('id', 'name', 'emp_id'),
            ])->where('job_seeker_type', ucfirst($type));

            // Apply company_id filter only for makers, checkers, and admins
            // if ($companyId) {
            //     $query->where('company_id', $companyId);
            // } elseif ($isPOMaker || $isFinanceMaker || $isBackoutMaker) {
            //     // For PO, finance, and backout makers, fetch all companies if no company_id is provided
            //     $query->whereIn('company_id', CompanyMaster::pluck('id'));
            // } elseif ($isMaker || $isChecker || $isAdmin) {
            //     // Restrict makers, checkers, and admins to their own company if no company_id is provided
            //     $query->where('company_id', $employee->company_id);
            // }

            if ($companyId) {
                $query->where('company_id', $companyId);
            } elseif (!$isAdmin && $employee) {
                $query->where('company_id', $employee->company_id);
            }


            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('consultant_code', 'like', "%{$search}%")
                        ->orWhere('consultant_name', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($q) use ($search) {
                            $q->where('client_code', 'like', "%{$search}%")
                                ->orWhere('client_name', 'like', "%{$search}%");
                        })
                        ->orWhere('skill', 'like', "%{$search}%")
                        ->orWhere('sap_id', 'like', "%{$search}%");
                });
            }

            if ($statusFilter !== 'All') {
                $query->where('form_status', $statusFilter);
            }

            // Role-based filtering
            if ($isMaker && !$isAdmin) {
                $query->where('maker_id', $employee->id);

                if ($statusFilter === 'All') {
                    $query->whereIn('process_status', [1, 2, 3, 4, 5, 6, 7, 8]);
                } elseif ($statusFilter === 'Pending') {
                    $query->whereIn('form_status', ['Pending', 'Rejected'])->where('process_status', 1); // Include Rejected
                } elseif ($statusFilter === 'Approved') {
                    $query->where('form_status', 'Approved')->whereIn('process_status', [2, 3, 4, 5, 6, 7, 8]);
                } elseif ($statusFilter === 'Rejected') {
                    $query->where('form_status', 'Rejected')->whereIn('process_status', [1, 2, 3, 4, 5, 6, 7, 8]); // Include process_status: 1
                }
            }
            if ($isChecker && !$isAdmin) {
                $query->where('checker_id', $employee->id);
                if ($statusFilter === 'All') {
                    $query->whereIn('process_status', [1, 2, 3, 4, 5, 6, 7, 8]);
                } elseif ($statusFilter === 'Pending') {
                    $query->where('form_status', 'Pending')->where('process_status', 1);
                } elseif ($statusFilter === 'Approved') {
                    $query->where('form_status', 'Approved')->whereIn('process_status', [2, 4, 6, 8]);
                } elseif ($statusFilter === 'Rejected') {
                    $query->where('form_status', 'Rejected')->whereIn('process_status', [1, 2, 4, 6, 8]); // Add process_status: 1
                }
            } elseif ($isPOMaker && !$isAdmin) {
                $query->where('job_seeker_type', 'Temporary')->where('status_id', $joinedStatusId);
                if ($statusFilter === 'All') {
                    $query->whereIn('process_status', [2, 3, 4, 7, 8]);
                } elseif ($statusFilter === 'Pending') {
                    $query->where(function ($q) use ($joinedStatusId) {
                        $q->where('form_status', 'Pending')->where('process_status', 3)
                            ->orWhere(function ($q) use ($joinedStatusId) {
                                $q->whereIn('form_status', ['Approved', 'Rejected'])->where('process_status', 2)->where('status_id', $joinedStatusId);
                            });
                    });
                } elseif ($statusFilter === 'Approved') {
                    $query->where('form_status', 'Approved')->whereIn('process_status', [2, 4]);
                } elseif ($statusFilter === 'Rejected') {
                    $query->where('form_status', 'Rejected')->whereIn('process_status', [2, 4]);
                }
            } elseif ($isPOChecker && !$isAdmin) {
                $query->where('po_checker_id', $employee->id);
                if ($statusFilter === 'All') {
                    $query->whereIn('process_status', [3, 4, 7, 8]);
                } elseif ($statusFilter === 'Pending') {
                    $query->where('form_status', 'Pending')->where('process_status', 3);
                } elseif ($statusFilter === 'Approved') {
                    $query->where('form_status', 'Approved')->whereIn('process_status', [4, 8]);
                } elseif ($statusFilter === 'Rejected') {
                    $query->where('form_status', 'Rejected')->whereIn('process_status', [4, 8]);
                }
            } elseif ($isFinanceMaker && !$isAdmin) {
                $query->where('job_seeker_type', 'Permanent')->where('status_id', $joinedStatusId);
                if ($statusFilter === 'All') {
                    $query->whereIn('process_status', [2, 5, 6, 7, 8]);
                } elseif ($statusFilter === 'Pending') {
                    $query->where(function ($q) use ($joinedStatusId) {
                        $q->where('form_status', 'Pending')->where('process_status', 5)
                            ->orWhere(function ($q) use ($joinedStatusId) {
                                $q->whereIn('form_status', ['Approved', 'Rejected'])->where('process_status', 2)->where('status_id', $joinedStatusId);
                            });
                    });
                } elseif ($statusFilter === 'Approved') {
                    $query->where('form_status', 'Approved')->whereIn('process_status', [2, 6]);
                } elseif ($statusFilter === 'Rejected') {
                    $query->where('form_status', 'Rejected')->whereIn('process_status', [2, 6]);
                }
            } elseif ($isFinanceChecker && !$isAdmin) {
                $query->where('finance_checker_id', $employee->id);
                if ($statusFilter === 'All') {
                    $query->whereIn('process_status', [5, 6, 7, 8]);
                } elseif ($statusFilter === 'Pending') {
                    $query->where('form_status', 'Pending')->where('process_status', 5);
                } elseif ($statusFilter === 'Approved') {
                    $query->where('form_status', 'Approved')->whereIn('process_status', [6, 8]);
                } elseif ($statusFilter === 'Rejected') {
                    $query->where('form_status', 'Rejected')->whereIn('process_status', [6, 8]);
                }
            } elseif ($isBackoutMaker && !$isAdmin) {
                $query->where('status_id', $joinedStatusId);
                if ($statusFilter === 'All') {
                    $query->whereIn('process_status', [2, 4, 6, 7, 8]);
                } elseif ($statusFilter === 'Pending') {
                    $query->where(function ($q) use ($joinedStatusId) {
                        $q->where('form_status', 'Pending')->where('process_status', 7)
                            ->orWhere(function ($q) use ($joinedStatusId) {
                                $q->whereIn('form_status', ['Approved', 'Rejected'])->whereIn('process_status', [2, 4, 6])->where('status_id', $joinedStatusId);
                            });
                    });
                } elseif ($statusFilter === 'Approved') {
                    $query->where('form_status', 'Approved')->whereIn('process_status', [2, 4, 6, 8]);
                } elseif ($statusFilter === 'Rejected') {
                    $query->where('form_status', 'Rejected')->whereIn('process_status', [2, 4, 6, 8]);
                }
            } elseif ($isBackoutChecker && !$isAdmin) {
                $query->where('backout_checker_id', $employee->id);
                if ($statusFilter === 'All') {
                    $query->whereIn('process_status', [7, 8]);
                } elseif ($statusFilter === 'Pending') {
                    $query->where('form_status', 'Pending')->where('process_status', 7);
                } elseif ($statusFilter === 'Approved') {
                    $query->where('form_status', 'Approved')->where('process_status', 8);
                } elseif ($statusFilter === 'Rejected') {
                    $query->where('form_status', 'Rejected')->where('process_status', 8);
                }
            } elseif ($isAdmin) {
                if ($statusFilter === 'All') {
                    // No additional filtering
                } elseif ($statusFilter === 'Pending') {
                    $query->where('form_status', 'Pending');
                } elseif ($statusFilter === 'Approved') {
                    $query->where('form_status', 'Approved');
                } elseif ($statusFilter === 'Rejected') {
                    $query->where('form_status', 'Rejected');
                }
            }

            $jobSeekers = $query->orderBy('id', 'desc')->paginate($perPage);

            // Fetch all companies for the dropdown
            // $companies = $isAdmin
            //     ? CompanyMaster::select('id', 'name')->orderBy('name')->get()->toArray()
            //     : ($employee && $employee->company
            //         ? [['id' => $employee->company_id, 'name' => $employee->company->name]]
            //         : []);

            $companies = CompanyMaster::select('id', 'name')->orderBy('name')->get()->toArray();
            $employeeCompanyId = $employee ? $employee->company_id : null;

            return Inertia::render('JobSeekers/Index', [
                'auth' => $user,
                'initialCounts' => $this->getCounts($user, $employee, $type, $companyId),
                'jobSeekers' => $jobSeekers,
                'isAdmin' => $isAdmin,
                'isMaker' => $isMaker,
                'isChecker' => $isChecker,
                'isPOMaker' => $isPOMaker,
                'isPOChecker' => $isPOChecker,
                'isFinanceMaker' => $isFinanceMaker,
                'isFinanceChecker' => $isFinanceChecker,
                'isBackoutMaker' => $isBackoutMaker,
                'isBackoutChecker' => $isBackoutChecker,
                'employeeRole' => $employee ? $employee->role : null,
                'counts' => $this->getCounts($user, $employee, $type, $companyId),
                'type' => ucfirst($type),
                'backoutStatusId' => $backoutStatusId,
                'employeeCompanyId' => $employee ? $employee->company_id : null,
                'employeeId' => $employee ? $employee->id : null,
                'employeeCheckerId' => $employee && $employee->is_self_checker ? $employee->id : ($employee && $employee->checker ? $employee->checker->id : null),
                'statusFilter' => $statusFilter,
                'companies' => $companies,
                'selectedCompanyId' => $request->input('company_id', $employeeCompanyId),
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to load Job Seekers index page for type {$type}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);
            return Inertia::render('Error', [
                'status' => 500,
                'message' => "Failed to load Job Seekers page: " . $e->getMessage(),
            ]);
        }
    }

    public function create(Request $request)
    {
        try {
            $type = $request->route()->defaults['type'] ?? 'permanent';
            $isTemporary = $type === 'temporary';
            $user = auth()->user();
            $employee = $user->employee;
            $isAdmin = $user->role === 'admin';
            $isMaker = $employee && $employee->role === 'maker';

            if (!$isAdmin && !$isMaker) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized']);
            }

            $companyId = $isAdmin ? ($request->input('company_id') ?? session('selected_company_id')) : ($employee ? $employee->company_id : null);
            $statusValue = $isTemporary ? 0 : 1;

            // Use company region instead of static mapping
            $company = $companyId ? CompanyMaster::find($companyId) : null;
            $country = $company && in_array($company->region, ['India', 'APAC', 'EU-UK', 'Aegis']) ? $company->region : 'India';

            $branches = [];
            $businessUnits = [];
            $clients = [];
            $assistantManagers = [];
            $deputyManagers = [];
            $teamLeaders = [];
            $recruiters = [];
            $statuses = [];

            if ($companyId && is_numeric($companyId)) {
                if ($isAdmin) {
                    session(['selected_company_id' => $companyId]);
                }

                $branches = BranchMaster::where('company_id', $companyId)
                    ->where('branch_status', $statusValue)
                    ->get(['id', 'name']);
                $businessUnits = BusinessUnitMaster::where('company_id', $companyId)
                    ->where('unit_status', $statusValue)
                    ->get(['id', 'unit']);
                $clients = Client::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('client_status', $statusValue)
                    ->get(['id', 'client_code', 'client_name', 'qualify_days', 'loaded_cost']);
                $assistantManagers = Employee::where('company_id', $companyId)
                    ->where('designation', 'AM')
                    ->get(['id', 'emp_id', 'name', 'status']);
                $deputyManagers = Employee::where('company_id', $companyId)
                    ->where('designation', 'DM')
                    ->get(['id', 'emp_id', 'name', 'status']);
                $teamLeaders = Employee::where('company_id', $companyId)
                    ->where('designation', 'TL')
                    ->get(['id', 'emp_id', 'name', 'status']);
                $recruiters = Employee::where('company_id', $companyId)
                    ->where('designation', 'Recruiter')
                    ->get(['id', 'emp_id', 'name', 'status']);

                // Filter statuses: Include 'FTE Conversion Fee' only for permanent job seekers in India
                $statusQuery = StatusMaster::query()->select(['id', 'status']);
                if ($isTemporary || $country !== 'India') {
                    $statusQuery->where('status', '!=', 'FTE Conversion Fee');
                }
                $statuses = $statusQuery->get();

                Log::info("Initial data for create:", [
                    'user_id' => $user->id,
                    'isAdmin' => $isAdmin,
                    'isTemporary' => $isTemporary,
                    'company_id' => $companyId,
                    'country' => $country,
                    'type' => $type,
                    'statusValue' => $statusValue,
                    'branches' => $branches->toArray(),
                    'businessUnits' => $businessUnits->toArray(),
                    'clients' => $clients->toArray(),
                    'assistantManagers' => $assistantManagers->toArray(),
                    'deputyManagers' => $deputyManagers->toArray(),
                    'teamLeaders' => $teamLeaders->toArray(),
                    'recruiters' => $recruiters->toArray(),
                    'statuses' => $statuses->toArray(),
                ]);
            } else {
                Log::info("No valid companyId provided for create, skipping data fetch", [
                    'user_id' => $user->id,
                    'isAdmin' => $isAdmin,
                    'isTemporary' => $isTemporary,
                    'type' => $type,
                    'company_id' => $companyId,
                    'country' => $country,
                ]);

                // Fetch statuses, excluding 'FTE Conversion Fee' if not India/permanent
                $statusQuery = StatusMaster::query()->select(['id', 'status']);
                if ($isTemporary || $country !== 'India') {
                    $statusQuery->where('status', '!=', 'FTE Conversion Fee');
                }
                $statuses = $statusQuery->get();
            }

            return Inertia::render('JobSeekers/Form', [
                'auth' => $user,
                'masterName' => 'Job Seeker',
                'isTemporary' => $isTemporary,
                'isEdit' => false,
                'isAdmin' => $isAdmin,
                'isMaker' => $isMaker,
                'isPOMaker' => $employee && $employee->role === 'po_maker',
                'isFinanceMaker' => $employee && $employee->role === 'finance_maker',
                'isBackoutMaker' => $employee && $employee->role === 'backout_maker',
                'employeeCompanyId' => $companyId,
                'employeeRole' => $employee ? $employee->role : null,
                'companies' => $isAdmin ? CompanyMaster::get(['id', 'name', 'region']) : ($companyId ? CompanyMaster::where('id', $companyId)->get(['id', 'name', 'region']) : []),
                'branches' => $branches,
                'businessUnits' => $businessUnits,
                'statuses' => $statuses,
                'clients' => $clients,
                'assistantManagers' => $assistantManagers,
                'deputyManagers' => $deputyManagers,
                'teamLeaders' => $teamLeaders,
                'recruiters' => $recruiters,
                'counts' => $this->getCounts($user, $employee, $type),
                'employeeId' => $employee ? $employee->id : null,
                'employeeCheckerId' => $employee && $employee->is_self_checker ? $employee->id : ($employee && $employee->checker ? $employee->checker->id : null),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to load Job Seeker create form for type {$type}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return Inertia::render('Error', [
                'status' => 500,
                'message' => "Failed to load create form: " . $e->getMessage(),
            ]);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $type = $request->route()->defaults['type'] ?? 'permanent';
            $isTemporary = $type === 'temporary';
            $user = auth()->user();
            $employee = $user->employee;
            $isAdmin = $user->role === 'admin';
            $isMaker = $employee && $employee->role === 'maker';
            $isPOMaker = $employee && $employee->role === 'po_maker';
            $isFinanceMaker = $employee && $employee->role === 'finance_maker';
            $isBackoutMaker = $employee && $employee->role === 'backout_maker';

            if (!$isAdmin && !$isMaker && !$isPOMaker && !$isFinanceMaker && !$isBackoutMaker) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized']);
            }

            $jobSeeker = JobSeeker::with([
                'company' => fn($q) => $q->select('id', 'name', 'region'),
                'location' => fn($q) => $q->select('id', 'name'),
                'businessUnit' => fn($q) => $q->select('id', 'unit'),
                'assistantManager' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'deputyManager' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'teamLeader' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'recruiter' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'status' => fn($q) => $q->select('id', 'status'),
                'client' => fn($q) => $q->select('id', 'client_code', 'client_name', 'qualify_days', 'loaded_cost'),
                'maker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'checker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'poMaker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'poChecker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'financeMaker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'financeChecker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'backoutMaker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'backoutChecker' => fn($q) => $q->select('id', 'name', 'emp_id'),
            ])->where('job_seeker_type', ucfirst($type))->findOrFail($id);

            $joinedStatus = StatusMaster::where('status', 'Joined')->first();
            Log::info('Debug StatusMaster Query', [
                'job_seeker_id' => $jobSeeker->id,
                'job_seeker_status_id' => $jobSeeker->status_id,
                'joined_status_id' => $joinedStatus ? $joinedStatus->id : 'Not found',
                'job_seeker_type' => $jobSeeker->job_seeker_type,
                'form_status' => $jobSeeker->form_status,
                'process_status' => $jobSeeker->process_status,
            ]);

            if ($isMaker && $jobSeeker->maker_id !== $employee->id) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only edit Job Seekers you created']);
            }

            if ($isMaker && $jobSeeker->status_id === StatusMaster::where('status', 'Backout')->first()->id) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Cannot edit a Job Seeker with Backout status']);
            }

            if ($isPOMaker && ($jobSeeker->job_seeker_type !== 'Temporary' || !in_array($jobSeeker->form_status, ['Pending', 'Approved', 'Rejected']) || !in_array($jobSeeker->process_status, [2, 3, 4]) || $jobSeeker->status_id !== ($joinedStatus->id ?? null))) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only edit Temporary Job Seekers with Approved or Rejected status and Joined status at process status 2']);
            }
            if ($isFinanceMaker && ($jobSeeker->job_seeker_type !== 'Permanent' || !in_array($jobSeeker->form_status, ['Pending', 'Approved', 'Rejected']) || !in_array($jobSeeker->process_status, [2, 5, 6]) || $jobSeeker->status_id !== ($joinedStatus->id ?? null))) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only edit Permanent Job Seekers with Approved or Rejected status and Joined status at process status 2']);
            }
            if ($isBackoutMaker && (!in_array($jobSeeker->form_status, ['Pending', 'Approved', 'Rejected']) || !in_array($jobSeeker->process_status, [4, 6, 7, 8]) || $jobSeeker->status_id !== ($joinedStatus->id ?? null))) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only edit Approved or Rejected Job Seekers with Joined status at process status 2, 4, or 6']);
            }

            $companyId = $jobSeeker->company_id;
            $statusValue = $isTemporary ? 0 : 1;

            // Use company region instead of static mapping
            $country = $jobSeeker->company && in_array($jobSeeker->company->region, ['India', 'APAC', 'EU-UK', 'Aegis']) ? $jobSeeker->company->region : 'India';

            // Filter statuses: Include 'FTE Conversion Fee' only for permanent job seekers in India
            $statusQuery = StatusMaster::query()->select(['id', 'status']);
            if ($isTemporary || $country !== 'India') {
                $statusQuery->where('status', '!=', 'FTE Conversion Fee');
            }
            $statuses = $statusQuery->get();

            return Inertia::render('JobSeekers/Form', [
                'auth' => $user,
                'masterName' => 'Job Seeker',
                'isTemporary' => $isTemporary,
                'isEdit' => true,
                'jobSeeker' => $jobSeeker,
                'isAdmin' => $isAdmin,
                'isMaker' => $isMaker,
                'isPOMaker' => $isPOMaker,
                'isFinanceMaker' => $isFinanceMaker,
                'isBackoutMaker' => $isBackoutMaker,
                'employeeCompanyId' => $companyId,
                'employeeRole' => $employee ? $employee->role : null,
                'companies' => $isAdmin ? CompanyMaster::get(['id', 'name', 'region']) : ($companyId ? CompanyMaster::where('id', $companyId)->get(['id', 'name', 'region']) : []),
                'branches' => $companyId ? BranchMaster::where('company_id', $companyId)->where('branch_status', $statusValue)->get(['id', 'name']) : [],
                'businessUnits' => $companyId ? BusinessUnitMaster::where('company_id', $companyId)->where('unit_status', $statusValue)->get(['id', 'unit']) : [],
                'statuses' => $statuses,
                'clients' => $companyId ? Client::where('status', 'active')->where('company_id', $companyId)->where('client_status', $statusValue)->get(['id', 'client_code', 'client_name', 'qualify_days', 'loaded_cost']) : [],
                'assistantManagers' => $companyId ? Employee::where('company_id', $companyId)->where('designation', 'AM')->get(['id', 'emp_id', 'name', 'status']) : [],
                'deputyManagers' => $companyId ? Employee::where('company_id', $companyId)->where('designation', 'DM')->get(['id', 'emp_id', 'name', 'status']) : [],
                'teamLeaders' => $companyId ? Employee::where('company_id', $companyId)->where('designation', 'TL')->get(['id', 'emp_id', 'name', 'status']) : [],
                'recruiters' => $companyId ? Employee::where('company_id', $companyId)->where('designation', 'Recruiter')->get(['id', 'emp_id', 'name', 'status']) : [],
                'counts' => $this->getCounts($user, $employee, $type),
                'employeeId' => $employee ? $employee->id : null,
                'employeeCheckerId' => $employee && $employee->is_self_checker ? $employee->id : ($employee && $employee->checker ? $employee->checker->id : null),
                'isReadOnlyReasonOfRejection' => $isMaker && $jobSeeker->reason_of_rejection !== null,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to load Job Seeker edit form for type {$type}, ID {$id}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return Inertia::render('Error', [
                'status' => 500,
                'message' => "Failed to load edit form: " . $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        Log::info("Starting Job Seeker store process", [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'route_type' => $request->route()->defaults['type'] ?? 'unknown',
        ]);

        try {
            $type = $request->route()->defaults['type'] ?? 'permanent';
            $isTemporary = $type === 'temporary';
            $user = auth()->user();
            $employee = $user->employee;
            $isAdmin = $user->role === 'admin';
            $isMaker = $employee && $employee->role === 'maker';

            if (!$isAdmin && !$isMaker) {
                Log::warning("Unauthorized attempt to create Job Seeker", ['user_id' => $user->id]);
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])
                    ->with('error', 'Unauthorized: Only admins or makers can create Job Seekers');
            }

            $validated = $request->validate($this->validationRules($request));
            Log::info("Validation passed", ['validated_data' => $validated]);

            $jobSeeker = DB::transaction(function () use ($request, $validated, $user, $employee, $type, $isAdmin, $isTemporary) {
                $data = $validated;
                $data['maker_id'] = $employee ? $employee->id : null;
                $data['checker_id'] = $employee && $employee->is_self_checker ? $employee->id : ($employee && $employee->checker ? $employee->checker->id : null);
                $data['form_status'] = $isAdmin ? $request->input('form_status', 'Pending') : 'Pending';
                $data['job_seeker_type'] = ucfirst($type);
                $data['process_status'] = 1;
                $data['reason_of_rejection'] = null;

                if ($isTemporary) {
                    $data['po_end_date'] = null;
                    $data['po_end_month'] = null;
                    $data['po_end_year'] = null;
                    $data['actual_billing_value'] = null;
                    $data['invoice_no'] = null;
                } else {
                    $data['actual_billing_value'] = null;
                    $data['invoice_no'] = null;
                }

                $client = Client::find($validated['client_id']);
                $company = CompanyMaster::find($validated['company_id']);
                $country = $company && in_array($company->region, ['India', 'APAC', 'EU-UK', 'Aegis']) ? $company->region : 'India';

                if ($client && $validated['join_date']) {
                    $data['qly_date'] = Carbon::parse($validated['join_date'])->addDays($client->qualify_days);
                }

                if ($isTemporary && $country === 'APAC' && isset($validated['hire_type']) && in_array($validated['hire_type'], ['ABN', 'PAGT'])) {
                    $data['loaded_cost'] = $validated['hire_type'] === 'ABN' ? 5.00 : 8.00;
                } elseif ($client) {
                    $data['loaded_cost'] = $client->loaded_cost ?? 0.00;
                } else {
                    $data['loaded_cost'] = 0.00;
                }

                return JobSeeker::create($data);
            });

            $counts = $this->getCounts($user, $employee, $type);

            return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with([
                'success' => 'Job Seeker created successfully',
                'counts' => $counts,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create Job Seeker for type {$type}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);
            return Redirect::route("job-seekers.{$type}.index")->with([
                'error' => 'Failed to create Job Seeker: ' . $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $type = $request->route()->defaults['type'] ?? 'permanent';
            $isTemporary = $type === 'temporary';
            $user = auth()->user();
            $employee = $user->employee;
            $isAdmin = $user->role === 'admin';
            $isMaker = $employee && $employee->role === 'maker';
            $isChecker = $employee && $employee->role === 'checker';
            $isPOMaker = $employee && $employee->role === 'po_maker';
            $isFinanceMaker = $employee && $employee->role === 'finance_maker';
            $isFinanceChecker = $employee && $employee->role === 'finance_checker';
            $isBackoutMaker = $employee && $employee->role === 'backout_maker';
            $isBackoutChecker = $employee && $employee->role === 'backout_checker';

            if (!$isAdmin && !$isMaker && !$isChecker && !$isPOMaker && !$isFinanceMaker && !$isFinanceChecker && !$isBackoutMaker && !$isBackoutChecker) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized');
            }

            $jobSeeker = JobSeeker::where('job_seeker_type', ucfirst($type))->findOrFail($id);

            if ($isMaker && $jobSeeker->maker_id !== $employee->id) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only edit Job Seekers you created');
            }

            if ($isChecker && $jobSeeker->checker_id !== $employee->id) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only edit Job Seekers you are assigned to check');
            }

            if ($isPOMaker && ($jobSeeker->job_seeker_type !== 'Temporary' || !in_array($jobSeeker->form_status, ['Pending', 'Approved', 'Rejected']) || !in_array($jobSeeker->process_status, [2, 3, 4]))) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only edit Temporary Job Seekers with Approved or Rejected status and Joined status at process status 2']);
            }
            if ($isFinanceMaker && ($jobSeeker->job_seeker_type !== 'Permanent' || !in_array($jobSeeker->form_status, ['Pending', 'Approved', 'Rejected']) || !in_array($jobSeeker->process_status, [2, 5, 6]))) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only edit Permanent Job Seekers with Approved or Rejected status and Joined status at process status 2']);
            }
            if ($isBackoutMaker && (!in_array($jobSeeker->form_status, ['Pending', 'Approved', 'Rejected']) || !in_array($jobSeeker->process_status, [4, 6, 7, 8]))) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only edit Approved or Rejected Job Seekers with Joined status at process status 2, 4, or 6']);
            }


            $validated = $request->validate($this->validationRules($request, $id));

            $jobSeeker = DB::transaction(function () use ($request, $validated, $id, $user, $employee, $isAdmin, $isMaker, $isChecker, $isPOMaker, $isFinanceMaker, $isFinanceChecker, $isBackoutMaker, $isBackoutChecker, $type, $isTemporary) {
                $jobSeeker = JobSeeker::where('job_seeker_type', ucfirst($type))->findOrFail($id);
                $data = $validated;

                if ($isPOMaker) {
                    $data = array_intersect_key($data, array_flip(['po_end_date', 'po_end_month', 'po_end_year']));
                    $data['po_maker_id'] = $employee->id;
                    $data['po_checker_id'] = $employee->is_self_checker ? $employee->id : ($employee->checker ? $employee->checker->id : null);
                    $data['process_status'] = 3;
                    $data['form_status'] = 'Pending';
                } elseif ($isFinanceMaker) {
                    $data = array_intersect_key($data, array_flip(['actual_billing_value', 'invoice_no']));
                    $data['finance_maker_id'] = $employee->id;
                    $data['finance_checker_id'] = $employee->is_self_checker ? $employee->id : ($employee->checker ? $employee->checker->id : null);
                    $data['process_status'] = 5;
                    $data['form_status'] = 'Pending';
                } elseif ($isBackoutMaker) {
                    $data = array_intersect_key($data, array_flip(['backout_term_date', 'backout_term_month', 'backout_term_year', 'backout_reason', 'backout_comments']));
                    $data['backout_maker_id'] = $employee->id;
                    $data['backout_checker_id'] = $employee->is_self_checker ? $employee->id : ($employee->checker ? $employee->checker->id : null);
                    $data['process_status'] = 7;
                    $data['form_status'] = 'Pending';
                } elseif ($isMaker || $isChecker) {
                    if ($isTemporary) {
                        $data['po_end_date'] = null;
                        $data['po_end_month'] = null;
                        $data['po_end_year'] = null;
                        $data['actual_billing_value'] = null;
                        $data['invoice_no'] = null;
                    } else {
                        $data['actual_billing_value'] = null;
                        $data['invoice_no'] = null;
                    }
                    $data['form_status'] = 'Pending';
                    $data['process_status'] = 1;
                    $data['reason_of_rejection'] = null; // Reset for Maker or Checker edits
                } elseif ($isBackoutChecker) {
                    $data = array_intersect_key($data, array_flip(['backout_term_date', 'backout_term_month', 'backout_term_year', 'reason_of_attrition', 'type_of_attrition']));
                    $data['form_status'] = 'Pending';
                    $data['process_status'] = 7; // Reset for Backout Checker edits
                } elseif ($isAdmin) {
                    $data['form_status'] = $request->input('form_status', 'Pending');
                    $data['process_status'] = $request->input('process_status', $jobSeeker->process_status);
                }

                if (($isMaker || $isAdmin) && isset($validated['client_id']) && isset($validated['join_date'])) {
                    $client = Client::find($validated['client_id']);
                    $company = CompanyMaster::find($validated['company_id']);
                    $country = $company && in_array($company->region, ['India', 'APAC', 'EU-UK', 'Aegis']) ? $company->region : 'India';

                    if ($client && $validated['join_date']) {
                        $data['qly_date'] = Carbon::parse($validated['join_date'])->addDays($client->qualify_days);
                    }

                    if ($isTemporary && $country === 'APAC' && isset($validated['hire_type']) && in_array($validated['hire_type'], ['ABN', 'PAGT'])) {
                        $data['loaded_cost'] = $validated['hire_type'] === 'ABN' ? 5.00 : 8.00;
                    } elseif ($client) {
                        $data['loaded_cost'] = $client->loaded_cost ?? 0.00;
                    } else {
                        $data['loaded_cost'] = 0.00;
                    }
                }

                $jobSeeker->update($data);
                return $jobSeeker;
            });

            $counts = $this->getCounts($user, $employee, $type);

            return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with([
                'success' => 'Job Seeker updated successfully',
                'counts' => $counts,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update Job Seeker ID {$id} for type {$type}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);
            return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with([
                'error' => 'Failed to update Job Seeker: ' . $e->getMessage(),
            ]);
        }
    }
    public function destroy(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $employee = $user->employee;
            $isAdmin = $user->role === 'admin';
            $isMaker = $employee && $employee->role === 'maker';

            $type = $request->route()->defaults['type'];

            if (!$isAdmin && !$isMaker) {
                Log::warning('Unauthorized delete attempt by user ID: ' . $user->id);
                return Redirect::route("job-seekers.{$type}.index")->with([
                    'error' => 'Unauthorized',
                    'counts' => $this->getCounts($user, $employee, $type),
                ]);
            }

            $jobSeeker = JobSeeker::where('job_seeker_type', ucfirst($type))->findOrFail($id);

            if ($isMaker && $jobSeeker->maker_id !== $employee->id) {
                Log::warning('Maker attempted to delete unowned Job Seeker ID: ' . $id);
                return Redirect::route("job-seekers.{$type}.index")->with([
                    'error' => 'Unauthorized: You can only delete Job Seekers you created',
                    'counts' => $this->getCounts($user, $employee, $type),
                ]);
            }

            if ($isMaker && $jobSeeker->form_status !== 'Pending') {
                Log::warning('Maker attempted to delete non-pending Job Seeker ID: ' . $id);
                return Redirect::route("job-seekers.{$type}.index")->with([
                    'error' => 'Cannot delete a non-pending Job Seeker',
                    'counts' => $this->getCounts($user, $employee, $type),
                ]);
            }

            Log::info("Deleting Job Seeker ID: {$id} for type {$type} by user ID: " . $user->id);
            $jobSeeker->delete();
            $counts = $this->getCounts($user, $employee, $type);

            return Redirect::route("job-seekers.{$type}.index")->with([
                'success' => 'Job Seeker deleted successfully',
                'counts' => $counts,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete Job Seeker ID: {$id} for type {$type}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return Redirect::route("job-seekers.{$type}.index")->with([
                'error' => 'Failed to delete Job Seeker: ' . $e->getMessage(),
                'counts' => $this->getCounts($user, $employee, $type),
            ]);
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $type = $request->route()->defaults['type'];
            $user = auth()->user();
            $employee = $user->employee;
            $isAdmin = $user->role === 'admin';
            $isMaker = $employee && $employee->role === 'maker' && $employee->id === ($employee->is_self_checker ? $employee->id : ($employee->checker ? $employee->checker->id : null));
            $isChecker = $employee && $employee->role === 'checker';
            $isPOMaker = $employee && $employee->role === 'po_maker' && $employee->id === ($employee->is_self_checker ? $employee->id : ($employee->po_checker ? $employee->po_checker->id : null));
            $isPOChecker = $employee && $employee->role === 'po_checker' && $employee->id === $employee->id;
            $isFinanceMaker = $employee && $employee->role === 'finance_maker' && $employee->id === ($employee->is_self_checker ? $employee->id : ($employee->finance_checker ? $employee->finance_checker->id : null));
            $isFinanceChecker = $employee && $employee->role === 'finance_checker';
            $isBackoutMaker = $employee && $employee->role === 'backout_maker' && $employee->id === ($employee->is_self_checker ? $employee->id : ($employee->backout_checker ? $employee->backout_checker->id : null));
            $isBackoutChecker = $employee && $employee->role === 'backout_checker' && $employee->id === $employee->id;

            if (!$isAdmin && !$isChecker && !$isMaker && !$isPOMaker && !$isPOChecker && !$isFinanceMaker && !$isFinanceChecker && !$isBackoutMaker && !$isBackoutChecker) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized');
            }

            $jobSeeker = JobSeeker::where('job_seeker_type', ucfirst($type))->findOrFail($id);

            if ($isMaker && $jobSeeker->checker_id !== $employee->id) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only approve Job Seekers you are assigned to check');
            }

            if ($isChecker && $jobSeeker->checker_id !== $employee->id) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only approve Job Seekers you are assigned to check');
            }

            if ($isPOMaker && $jobSeeker->po_checker_id !== $employee->id) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only approve Job Seekers you are assigned to check');
            }

            if ($isPOChecker && $jobSeeker->po_checker_id !== $employee->id) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only approve Job Seekers you are assigned to check');
            }

            if ($isFinanceMaker && $jobSeeker->finance_checker_id !== $employee->id) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only approve Job Seekers you are assigned to check');
            }

            if ($isFinanceChecker && $jobSeeker->finance_checker_id !== $employee->id) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only approve Job Seekers you are assigned to check');
            }

            if ($isBackoutMaker && $jobSeeker->backout_checker_id !== $employee->id) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only approve Job Seekers you are assigned to check');
            }

            if ($isBackoutChecker && $jobSeeker->backout_checker_id !== $employee->id) {
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only approve Job Seekers you are assigned to check');
            }

            $jobSeeker = DB::transaction(function () use ($jobSeeker, $isMaker, $isChecker, $isPOMaker, $isPOChecker, $isFinanceMaker, $isFinanceChecker, $isBackoutMaker, $isBackoutChecker, $type) {
                $data = [
                    'form_status' => 'Approved',
                    'reason_of_rejection' => null, // Clear reason_of_rejection
                ];

                if ($isMaker || $isChecker) {
                    $data['process_status'] = 2;
                } elseif ($isPOMaker || $isPOChecker) {
                    $data['process_status'] = 4;
                } elseif ($isFinanceMaker || $isFinanceChecker) {
                    $data['process_status'] = 6;
                } elseif ($isBackoutMaker || $isBackoutChecker) {
                    $data['process_status'] = 8;
                }

                $jobSeeker->update($data);
                return $jobSeeker;
            });

            $counts = $this->getCounts(auth()->user(), $employee, $type);

            return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Approved'])->with([
                'success' => 'Job Seeker approved successfully',
                'counts' => $counts,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to approve Job Seeker ID {$id} for type {$type}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with([
                'error' => 'Failed to approve Job Seeker: ' . $e->getMessage(),
            ]);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $type = $request->route()->defaults['type'];
            $user = auth()->user();
            $employee = $user->employee;
            $isAdmin = $user->role === 'admin';
            $isMaker = $employee && $employee->role === 'maker' && $employee->id === ($employee->is_self_checker ? $employee->id : ($employee->checker ? $employee->checker->id : null));
            $isChecker = $employee && $employee->role === 'checker';
            $isPOMaker = $employee && $employee->role === 'po_maker' && $employee->id === ($employee->is_self_checker ? $employee->id : ($employee->po_checker ? $employee->po_checker->id : null));
            $isPOChecker = $employee && $employee->role === 'po_checker' && $employee->id === $employee->id;
            $isFinanceMaker = $employee && $employee->role === 'finance_maker' && $employee->id === ($employee->is_self_checker ? $employee->id : ($employee->finance_checker ? $employee->finance_checker->id : null));
            $isFinanceChecker = $employee && $employee->role === 'finance_checker';
            $isBackoutMaker = $employee && $employee->role === 'backout_maker' && $employee->id === ($employee->is_self_checker ? $employee->id : ($employee->backout_checker ? $employee->backout_checker->id : null));
            $isBackoutChecker = $employee && $employee->role === 'backout_checker' && $employee->id === $employee->id;

            if (!$isAdmin && !$isChecker && !$isMaker && !$isPOMaker && !$isPOChecker && !$isFinanceMaker && !$isFinanceChecker && !$isBackoutMaker && !$isBackoutChecker) {
                \Log::warning("Unauthorized reject attempt for Job Seeker ID {$id} by user ID {$user->id}", [
                    'role' => $employee ? $employee->role : 'none',
                ]);
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized');
            }

            $jobSeeker = JobSeeker::where('job_seeker_type', ucfirst($type))->findOrFail($id);

            // Authorization checks
            if ($isMaker && $jobSeeker->checker_id !== $employee->id) {
                \Log::warning("Maker unauthorized for Job Seeker ID {$id}: checker_id mismatch", [
                    'checker_id' => $jobSeeker->checker_id,
                    'employee_id' => $employee->id,
                ]);
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only reject Job Seekers you are assigned to check');
            }
            if ($isChecker && $jobSeeker->checker_id !== $employee->id) {
                \Log::warning("Checker unauthorized for Job Seeker ID {$id}: checker_id mismatch", [
                    'checker_id' => $jobSeeker->checker_id,
                    'employee_id' => $employee->id,
                ]);
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only reject Job Seekers you are assigned to check');
            }
            if ($isPOMaker && $jobSeeker->po_checker_id !== $employee->id) {
                \Log::warning("PO Maker unauthorized for Job Seeker ID {$id}: po_checker_id mismatch", [
                    'po_checker_id' => $jobSeeker->po_checker_id,
                    'employee_id' => $employee->id,
                ]);
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only reject Job Seekers you are assigned to check');
            }
            if ($isPOChecker && $jobSeeker->po_checker_id !== $employee->id) {
                \Log::warning("PO Checker unauthorized for Job Seeker ID {$id}: po_checker_id mismatch", [
                    'po_checker_id' => $jobSeeker->po_checker_id,
                    'employee_id' => $employee->id,
                ]);
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only reject Job Seekers you are assigned to check');
            }
            if ($isFinanceMaker && $jobSeeker->finance_checker_id !== $employee->id) {
                \Log::warning("Finance Maker unauthorized for Job Seeker ID {$id}: finance_checker_id mismatch", [
                    'finance_checker_id' => $jobSeeker->finance_checker_id,
                    'employee_id' => $employee->id,
                ]);
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only reject Job Seekers you are assigned to check');
            }
            if ($isFinanceChecker && $jobSeeker->finance_checker_id !== $employee->id) {
                \Log::warning("Finance Checker unauthorized for Job Seeker ID {$id}: finance_checker_id mismatch", [
                    'finance_checker_id' => $jobSeeker->finance_checker_id,
                    'employee_id' => $employee->id,
                ]);
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only reject Job Seekers you are assigned to check');
            }
            if ($isBackoutMaker && $jobSeeker->backout_checker_id !== $employee->id) {
                \Log::warning("Backout Maker unauthorized for Job Seeker ID {$id}: backout_checker_id mismatch", [
                    'backout_checker_id' => $jobSeeker->backout_checker_id,
                    'employee_id' => $employee->id,
                ]);
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only reject Job Seekers you are assigned to check');
            }
            if ($isBackoutChecker && $jobSeeker->backout_checker_id !== $employee->id) {
                \Log::warning("Backout Checker unauthorized for Job Seeker ID {$id}: backout_checker_id mismatch", [
                    'backout_checker_id' => $jobSeeker->backout_checker_id,
                    'employee_id' => $employee->id,
                ]);
                return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with('error', 'Unauthorized: You can only reject Job Seekers you are assigned to check');
            }

            $validated = $request->validate([
                'reason_of_rejection' => 'required|string|max:1000',
            ]);

            \Log::info("Attempting to reject Job Seeker ID {$id} for type {$type}", [
                'user_id' => $user->id,
                'role' => $employee ? $employee->role : 'admin',
                'reason_of_rejection' => $validated['reason_of_rejection'],
                'current_form_status' => $jobSeeker->form_status,
                'current_process_status' => $jobSeeker->process_status,
                'current_reason_of_rejection' => $jobSeeker->reason_of_rejection,
                'current_po_end_date' => $jobSeeker->po_end_date,
                'current_actual_billing_value' => $jobSeeker->actual_billing_value,
                'current_backout_term_date' => $jobSeeker->backout_term_date,
            ]);

            $jobSeeker = DB::transaction(function () use ($jobSeeker, $validated, $type, $isMaker, $isChecker, $isPOMaker, $isPOChecker, $isFinanceMaker, $isFinanceChecker, $isBackoutMaker, $isBackoutChecker, $isAdmin) {
                $data = [
                    'form_status' => 'Rejected',
                    'reason_of_rejection' => $validated['reason_of_rejection'],
                ];

                // Set process_status based on role, retain all maker-entered fields
                if ($isMaker || $isChecker || $isAdmin) {
                    $data['process_status'] = 1; // Return to initial maker
                } elseif ($isPOMaker || $isPOChecker) {
                    $data['process_status'] = 2; // Return to PO maker
                } elseif ($isFinanceMaker || $isFinanceChecker) {
                    $data['process_status'] = 2; // Return to finance maker
                } elseif ($isBackoutMaker || $isBackoutChecker) {
                    $data['process_status'] = 6; // Return to backout maker
                }

                $jobSeeker->update($data);

                $updatedJobSeeker = $jobSeeker->fresh();
                \Log::info("Job Seeker ID {$jobSeeker->id} updated", [
                    'data' => $data,
                    'updated_form_status' => $updatedJobSeeker->form_status,
                    'updated_process_status' => $updatedJobSeeker->process_status,
                    'updated_reason_of_rejection' => $updatedJobSeeker->reason_of_rejection,
                    'updated_po_end_date' => $updatedJobSeeker->po_end_date,
                    'updated_actual_billing_value' => $updatedJobSeeker->actual_billing_value,
                    'updated_backout_term_date' => $updatedJobSeeker->backout_term_date,
                ]);

                return $updatedJobSeeker;
            });

            $counts = $this->getCounts(auth()->user(), $employee, $type);

            \Log::info("Reject successful for Job Seeker ID {$id}", [
                'counts' => $counts,
            ]);

            return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Rejected'])->with([
                'success' => 'Job Seeker rejected successfully',
                'counts' => $counts,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("Validation failed for reject Job Seeker ID {$id}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
                'errors' => $e->errors(),
            ]);
            return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with([
                'error' => 'Validation failed: ' . implode(', ', $e->errors()['reason_of_rejection'] ?? ['Unknown error']),
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to reject Job Seeker ID {$id} for type {$type}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Redirect::route("job-seekers.{$type}.index", ['status_filter' => 'Pending'])->with([
                'error' => 'Failed to reject Job Seeker: ' . $e->getMessage(),
            ]);
        }
    }


    public function show($type, $id)
    {
        try {
            $user = auth()->user();
            $employee = $user->employee;
            $isAdmin = $user->role === 'admin';
            $isChecker = $employee && $employee->role === 'checker';
            $isMaker = $employee && $employee->role === 'maker';
            $isPOMaker = $employee && $employee->role === 'po_maker';
            $isPOChecker = $employee && $employee->role === 'po_checker';
            $isFinanceMaker = $employee && $employee->role === 'finance_maker';
            $isFinanceChecker = $employee && $employee->role === 'finance_checker';
            $isBackoutMaker = $employee && $employee->role === 'backout_maker';
            $isBackoutChecker = $employee && $employee->role === 'backout_checker';

            if (!$isMaker && !$isChecker && !$isAdmin && !$isPOMaker && !$isPOChecker && !$isFinanceMaker && !$isFinanceChecker && !$isBackoutMaker && !$isBackoutChecker) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized']);
            }

            $jobSeeker = JobSeeker::with([
                'client' => fn($q) => $q->select('id', 'client_code', 'client_name', 'qualify_days', 'loaded_cost'),
                'company' => fn($q) => $q->select('id', 'name'),
                'location' => fn($q) => $q->select('id', 'name'),
                'businessUnit' => fn($q) => $q->select('id', 'unit'),
                'status' => fn($q) => $q->select('id', 'status'),
                'assistantManager' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'deputyManager' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'teamLeader' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'recruiter' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'maker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'checker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'poMaker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'poChecker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'financeMaker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'financeChecker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'backoutMaker' => fn($q) => $q->select('id', 'name', 'emp_id'),
                'backoutChecker' => fn($q) => $q->select('id', 'name', 'emp_id'),
            ])->where('job_seeker_type', ucfirst($type))->findOrFail($id);

            if ($isMaker && $jobSeeker->maker_id !== $employee->id) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only view Job Seekers you created']);
            }

            if ($isChecker && $jobSeeker->checker_id !== $employee->id && $jobSeeker->process_status === 1) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You are not the assigned checker']);
            }

            if ($isPOMaker && ($jobSeeker->job_seeker_type !== 'Temporary' || $jobSeeker->form_status !== 'Approved' || $jobSeeker->process_status !== 2 || $jobSeeker->status_id !== StatusMaster::where('status', 'Joined')->first()->id)) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only view Temporary Job Seekers with Approved status and Joined status']);
            }

            if ($isPOChecker && $jobSeeker->po_checker_id !== $employee->id && $jobSeeker->process_status === 3) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You are not the assigned PO checker']);
            }

            if ($isFinanceMaker && ($jobSeeker->job_seeker_type !== 'Permanent' || $jobSeeker->form_status !== 'Approved' || $jobSeeker->process_status !== 2 || $jobSeeker->status_id !== StatusMaster::where('status', 'Joined')->first()->id)) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only view Permanent Job Seekers with Approved status and Joined status']);
            }

            if ($isFinanceChecker && $jobSeeker->finance_checker_id !== $employee->id && $jobSeeker->process_status === 5) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You are not the assigned finance checker']);
            }

            if ($isBackoutMaker && ($jobSeeker->form_status !== 'Approved' || !in_array($jobSeeker->process_status, [2, 4]) || $jobSeeker->status_id !== StatusMaster::where('status', 'Joined')->first()->id)) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You can only view Approved Job Seekers with Joined status at process status 2 or 4']);
            }

            if ($isBackoutChecker && $jobSeeker->backout_checker_id !== $employee->id && $jobSeeker->process_status === 5) {
                return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized: You are not the assigned backout checker']);
            }

            return Inertia::render('JobSeekers/ViewModal', [
                'auth' => $user,
                'jobSeeker' => $jobSeeker,
                'type' => ucfirst($type),
                'showActions' => $isAdmin ||
                    ($isChecker && $jobSeeker->process_status === 1 && $jobSeeker->checker_id === $employee->id) ||
                    ($isPOChecker && $jobSeeker->process_status === 3 && $jobSeeker->po_checker_id === $employee->id) ||
                    ($isFinanceChecker && $jobSeeker->process_status === 5 && $jobSeeker->finance_checker_id === $employee->id) ||
                    ($isBackoutChecker && $jobSeeker->process_status === 7 && $jobSeeker->backout_checker_id === $employee->id),
                'counts' => $this->getCounts($user, $employee, $type),
                'employeeId' => $employee ? $employee->id : null,
                'employeeCheckerId' => $employee && $employee->is_self_checker ? $employee->id : ($employee && $employee->checker ? $employee->checker->id : null),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to view Job Seeker ID {$id} for type {$type}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return Inertia::render('Error', [
                'status' => 500,
                'message' => "Failed to view Job Seeker: " . $e->getMessage(),
            ]);
        }
    }

    public function counts(Request $request, $type)
    {
        try {
            $user = auth()->user();
            $employee = $user->employee;
            $companyId = $request->input('company_id');
            $counts = $this->getCounts($user, $employee, $type, $companyId);

            return response()->json($counts);
        } catch (\Exception $e) {
            \Log::error("Failed to fetch Job Seeker counts for type {$type}: " . $e->getMessage(), [
                'user_id' => auth()->id(),
                'company_id' => $request->input('company_id'),
            ]);
            return response()->json([
                'error' => 'Failed to fetch counts',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getBranches(Request $request, $companyId)
    {
        try {
            if (!is_numeric($companyId)) {
                Log::error("Invalid companyId: {$companyId}");
                return response()->json(['error' => 'Invalid company ID'], 400);
            }
            $type = $request->route()->defaults['type'];
            $statusValue = $type === 'temporary' ? 0 : 1;
            Log::info("Fetching branches for company_id: {$companyId}, type: {$type}, statusValue: {$statusValue}");
            $branches = BranchMaster::where('company_id', $companyId)
                ->where('branch_status', $statusValue)
                ->get(['id', 'name']);
            Log::info("Branches query result:", $branches->toArray());
            return response()->json($branches);
        } catch (\Exception $e) {
            Log::error("Failed to fetch branches for company ID {$companyId}, type {$type}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch branches'], 500);
        }
    }

    public function getBusinessUnits(Request $request, $companyId)
    {
        try {
            if (!is_numeric($companyId)) {
                Log::error("Invalid companyId: {$companyId}");
                return response()->json(['error' => 'Invalid company ID'], 400);
            }
            $type = $request->route()->defaults['type'];
            $statusValue = $type === 'temporary' ? 0 : 1;
            Log::info("Fetching business units for company_id: {$companyId}, type: {$type}, statusValue: {$statusValue}");
            $businessUnits = BusinessUnitMaster::where('company_id', $companyId)
                ->where('unit_status', $statusValue)
                ->get(['id', 'unit']);
            Log::info("Business units query result:", $businessUnits->toArray());
            return response()->json($businessUnits);
        } catch (\Exception $e) {
            Log::error("Failed to fetch business units for company ID {$companyId}, type {$type}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch business units'], 500);
        }
    }

    public function getClients(Request $request, $companyId)
    {
        try {
            if (!is_numeric($companyId)) {
                Log::error("Invalid companyId: {$companyId}");
                return response()->json(['error' => 'Invalid company ID'], 400);
            }
            $type = $request->route()->defaults['type'];
            $statusValue = $type === 'temporary' ? 0 : 1;
            Log::info("Fetching clients for company_id: {$companyId}, type: {$type}, statusValue: {$statusValue}");
            $clients = Client::where('company_id', $companyId)
                ->where('status', 'active')
                ->where('client_status', $statusValue)
                ->get(['id', 'client_code', 'client_name', 'qualify_days', 'loaded_cost']);
            Log::info("Clients query result:", $clients->toArray());
            return response()->json($clients);
        } catch (\Exception $e) {
            Log::error("Failed to fetch clients for company ID {$companyId}, type {$type}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch clients'], 500);
        }
    }

    public function getEmployees(Request $request, $companyId)
    {
        try {
            if (!is_numeric($companyId)) {
                Log::error("Invalid companyId: {$companyId}");
                return response()->json(['error' => 'Invalid company ID'], 400);
            }
            $type = $request->route()->defaults['type'];
            $statusValue = $type === 'temporary' ? 0 : 1;
            Log::info("Fetching employees for company_id: {$companyId}, type: {$type}, statusValue: {$statusValue}");
            $employees = Employee::where('company_id', $companyId)
                ->get(['id', 'emp_id', 'name', 'designation']);
            $result = [
                'assistantManagers' => $employees->where('designation', 'AM')->values(),
                'deputyManagers' => $employees->where('designation', 'DM')->values(),
                'teamLeaders' => $employees->where('designation', 'TL')->values(),
                'recruiters' => $employees->where('designation', 'Recruiter')->values(),
            ];
            Log::info("Employees query result:", $result);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Failed to fetch employees for company ID {$companyId}, type {$type}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch employees'], 500);
        }
    }

    private function getCounts($user, $employee, $type, $companyId = null)
    {
        $counts = ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
        $isAdmin = $user->role === 'admin';
        $isMaker = $employee && $employee->role === 'maker';
        $isChecker = $employee && $employee->role === 'checker';
        $isPOMaker = $employee && $employee->role === 'po_maker';
        $isPOChecker = $employee && $employee->role === 'po_checker';
        $isFinanceMaker = $employee && $employee->role === 'finance_maker';
        $isFinanceChecker = $employee && $employee->role === 'finance_checker';
        $isBackoutMaker = $employee && $employee->role === 'backout_maker';
        $isBackoutChecker = $employee && $employee->role === 'backout_checker';
        $joinedStatusId = StatusMaster::where('status', 'Joined')->first()->id ?? null;
        $backoutStatusId = StatusMaster::where('status', 'Backout')->first()->id ?? null;

        $query = JobSeeker::where('job_seeker_type', ucfirst($type));

        // Apply company_id filter if provided
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($isAdmin) {
            $counts['all'] = (clone $query)->count();
            $counts['pending'] = (clone $query)->where('form_status', 'Pending')->count();
            $counts['approved'] = (clone $query)->where('form_status', 'Approved')->count();
            $counts['rejected'] = (clone $query)->where('form_status', 'Rejected')->count();
        } elseif ($isMaker) {
            $query->where('maker_id', $employee->id)
                ->where('status_id', '!=', $backoutStatusId);
            $counts['all'] = (clone $query)->whereIn('process_status', [1, 2, 3, 4, 5, 6, 7, 8])->count();
            $counts['pending'] = (clone $query)->where('form_status', ['Pending', 'Rejected'])->where('process_status', 1)->count();
            $counts['approved'] = (clone $query)->where('form_status', 'Approved')->whereIn('process_status', [2, 3, 4, 5, 6, 7, 8])->count();
            $counts['rejected'] = (clone $query)->where('form_status', 'Rejected')->whereIn('process_status', [1, 2, 3, 4, 5, 6, 7, 8])->count();
        } elseif ($isChecker) {
            $query->where('checker_id', $employee->id);
            $counts['all'] = (clone $query)->whereIn('process_status', [1, 2, 3, 4, 5, 6, 7, 8])->count();
            $counts['pending'] = (clone $query)->where('form_status', 'Pending')->where('process_status', 1)->count();
            $counts['approved'] = (clone $query)->where('form_status', 'Approved')->whereIn('process_status', [2, 4, 6, 8])->count();
            $counts['rejected'] = (clone $query)->where('form_status', 'Rejected')->whereIn('process_status', [1, 2, 4, 6, 8])->count();
        } elseif ($isPOMaker) {
            $query->where('job_seeker_type', 'Temporary')->where('status_id', $joinedStatusId);
            $counts['all'] = (clone $query)->whereIn('process_status', [2, 3, 4, 7, 8])->count();
            $counts['pending'] = (clone $query)->where(function ($q) use ($joinedStatusId) {
                $q->where('form_status', 'Pending')->where('process_status', 3)
                    ->orWhere(function ($q) use ($joinedStatusId) {
                        $q->where('form_status', 'Approved')->where('process_status', 2)->where('status_id', $joinedStatusId);
                    });
            })->count();
            $counts['approved'] = (clone $query)->where('form_status', 'Approved')->whereIn('process_status', [2, 4])->count();
            $counts['rejected'] = (clone $query)->where('form_status', 'Rejected')->whereIn('process_status', [2, 4])->count();
        } elseif ($isPOChecker) {
            $query->where('po_checker_id', $employee->id);
            $counts['all'] = (clone $query)->whereIn('process_status', [3, 4, 7, 8])->count();
            $counts['pending'] = (clone $query)->where('form_status', 'Pending')->where('process_status', 3)->count();
            $counts['approved'] = (clone $query)->where('form_status', 'Approved')->whereIn('process_status', [4, 8])->count();
            $counts['rejected'] = (clone $query)->where('form_status', 'Rejected')->whereIn('process_status', [4, 8])->count();
        } elseif ($isFinanceMaker) {
            $query->where('job_seeker_type', 'Permanent')->where('status_id', $joinedStatusId);
            $counts['all'] = (clone $query)->whereIn('process_status', [2, 5, 6, 7, 8])->count();
            $counts['pending'] = (clone $query)->where(function ($q) use ($joinedStatusId) {
                $q->where('form_status', 'Pending')->where('process_status', 5)
                    ->orWhere(function ($q) use ($joinedStatusId) {
                        $q->where('form_status', 'Approved')->where('process_status', 2)->where('status_id', $joinedStatusId);
                    });
            })->count();
            $counts['approved'] = (clone $query)->where('form_status', 'Approved')->whereIn('process_status', [2, 6])->count();
            $counts['rejected'] = (clone $query)->where('form_status', 'Rejected')->whereIn('process_status', [2, 6])->count();
        } elseif ($isFinanceChecker) {
            $query->where('finance_checker_id', $employee->id);
            $counts['all'] = (clone $query)->whereIn('process_status', [5, 6, 7, 8])->count();
            $counts['pending'] = (clone $query)->where('form_status', 'Pending')->where('process_status', 5)->count();
            $counts['approved'] = (clone $query)->where('form_status', 'Approved')->whereIn('process_status', [6, 8])->count();
            $counts['rejected'] = (clone $query)->where('form_status', 'Rejected')->whereIn('process_status', [6, 8])->count();
        } elseif ($isBackoutMaker) {
            $query->where('status_id', $joinedStatusId);
            $counts['all'] = (clone $query)->whereIn('process_status', [2, 4, 6, 7, 8])->count();
            $counts['pending'] = (clone $query)->where(function ($q) use ($joinedStatusId) {
                $q->where('form_status', 'Pending')->where('process_status', 7)
                    ->orWhere(function ($q) use ($joinedStatusId) {
                        $q->where('form_status', 'Approved')->whereIn('process_status', [2, 4, 6])->where('status_id', $joinedStatusId);
                    });
            })->count();
            $counts['approved'] = (clone $query)->where('form_status', 'Approved')->whereIn('process_status', [2, 4, 6, 8])->count();
            $counts['rejected'] = (clone $query)->where('form_status', 'Rejected')->whereIn('process_status', [2, 4, 6, 8])->count();
        } elseif ($isBackoutChecker) {
            $query->where('backout_checker_id', $employee->id);
            $counts['all'] = (clone $query)->whereIn('process_status', [7, 8])->count();
            $counts['pending'] = (clone $query)->where('form_status', 'Pending')->where('process_status', 7)->count();
            $counts['approved'] = (clone $query)->where('form_status', 'Approved')->where('process_status', 8)->count();
            $counts['rejected'] = (clone $query)->where('form_status', 'Rejected')->where('process_status', 8)->count();
        }

        \Log::info("Counts calculated for type {$type}", [
            'user_id' => $user->id,
            'employee_role' => $employee ? $employee->role : null,
            'company_id' => $companyId,
            'counts' => $counts,
        ]);

        return $counts;
    }
    private function validationRules($request, $id = null)
    {
        $type = $request->route()->defaults['type'];
        $companyId = $request->input('company_id');
        $statusId = $request->input('status_id');
        $joinedStatusId = StatusMaster::where('status', 'Joined')->first()->id ?? null;

        $rules = [
            'company_id' => 'required|integer|exists:company_masters,id',
            'location_id' => 'required|integer|exists:branch_masters,id',
            'hire_type' => 'nullable|string',
            'business_unit_id' => 'required_if:company_id,2|integer|exists:business_unit_masters,id|nullable',
            'am_id' => 'nullable|integer|exists:employees,id',
            'dm_id' => 'nullable|integer|exists:employees,id',
            'tl_id' => 'nullable|integer|exists:employees,id',
            'recruiter_id' => 'nullable|integer|exists:employees,id',
            'consultant_code' => 'nullable|string',
            'consultant_name' => 'required|string',
            'skill' => 'required|string',
            'sap_id' => 'nullable|string|unique:job_seekers,sap_id,' . ($id ?: 'NULL'),
            'status_id' => 'required|integer|exists:status_masters,id',
            'form_status' => 'nullable|in:Pending,Approved,Rejected',
            'client_id' => 'required|integer|exists:clients,id',
            'poc' => 'nullable|string',
            'client_reporting_manager' => 'nullable|string',
            'quarter' => 'nullable|string',
            'selection_date' => 'nullable|date',
            'offer_date' => 'nullable|date',
            'join_date' => 'required|date',
            'join_month' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
            'join_year' => 'nullable|integer',
            'qly_date' => 'nullable|date',
            'backout_term_date' => 'nullable|date',
            'backout_term_month' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
            'backout_term_year' => 'nullable|integer|min:1900|max:9999',
            'reason_of_attrition' => 'nullable|string|max:1000',
            'type_of_attrition' => 'nullable|in:Voluntary,Involuntary',
            'po_end_date' => 'nullable|date',
            'po_end_month' => 'nullable|string',
            'po_end_year' => 'nullable|integer|min:1900|max:9999',
            'remark1' => 'nullable|string',
            'remark2' => 'nullable|string',
            'sources' => 'nullable|string',
            'source' => 'nullable|string',
            'domain' => 'nullable|string',
            'bd_absconding_term' => 'nullable|string',
        ];

        if ($statusId == 8) { // Backout
            $rules['backout_term_date'] = 'required|date';
            $rules['backout_term_month'] = 'required|string|regex:/^\d{4}-\d{2}$/';
            $rules['backout_term_year'] = 'required|integer|min:1900|max:9999';
            if ($type === 'temporary') {
                $rules['bo_type'] = 'required|in:Client BO,Candidate BO';
            }
        }

        if ($statusId == 9) { // Termination
            $rules['backout_term_date'] = 'required|date';
            $rules['backout_term_month'] = 'required|string|regex:/^\d{4}-\d{2}$/';
            $rules['backout_term_year'] = 'required|integer|min:1900|max:9999';
            $rules['reason_of_attrition'] = 'required|string|max:1000';
            $rules['type_of_attrition'] = 'required|in:Voluntary,Involuntary';
        }

        if ($statusId == $joinedStatusId) {
            $rules['join_date'] = 'required|date|before_or_equal:' . Carbon::today()->toDateString();
        }

        // Temporary job seeker rules
        if ($type === 'temporary') {
            $rules = array_merge($rules, [
                'pay_rate' => 'required|numeric|min:0',
                'bill_rate' => 'required|numeric|min:0',
                'pay_rate_1' => 'nullable|numeric|min:0',
                'gp_month' => 'nullable|numeric',
                'otc' => 'nullable|numeric|min:0',
                'otc_split' => 'nullable|numeric|min:0',
                'msp_fees' => 'nullable|numeric|min:0',
                'loaded_cost' => 'nullable|numeric|min:0',
                'final_gp' => 'nullable|numeric',
                'percentage_gp' => 'nullable|numeric',
                'gp_hour' => 'nullable|numeric',
                'gp_hour_usd' => 'nullable|numeric',

            ]);

            $company = CompanyMaster::find($companyId);
            $country = $company && in_array($company->region, ['India', 'APAC', 'EU-UK', 'Aegis']) ? $company->region : 'India';
            if ($country === 'APAC') {
                $rules['hire_type'] = 'required|in:ABN,PAGT';
            }

            // EU-UK specific fields (company_id = 4)
            if ($companyId == 4) {
                $rules = array_merge($rules, [
                    'pay_rate_usd' => 'nullable|numeric|min:0',
                    'bill_rate_usd' => 'nullable|numeric|min:0',
                    'basic_pay_rate' => 'nullable|numeric|min:0',
                    'benefits' => 'nullable|numeric|min:0',
                    'end_client' => 'required|string',
                ]);
            }
        }

        // Permanent job seeker rules
        if ($type === 'permanent') {
            $rules = array_merge($rules, [
                'ctc_offered' => 'required|numeric|min:0',
                'billing_value' => 'required|numeric|min:0',
                'loaded_gp' => 'nullable|numeric',
                'final_billing_value' => 'nullable|numeric',
                'actual_billing_value' => 'nullable|numeric',
                'invoice_no' => 'nullable|string|max:255',
                'currency' => 'nullable|in:INR,USD,EUR,GBP',
                'join_month' => 'nullable|string',
                'join_year' => 'nullable|integer',
                'select_month' => 'nullable|string',

            ]);
        }

        // Role-specific restrictions
        if ($request->user()->employee && $request->user()->employee->role === 'po_maker') {
            $rules = array_intersect_key($rules, array_flip(['po_end_date', 'po_end_month', 'po_end_year']));
        } elseif ($request->user()->employee && $request->user()->employee->role === 'finance_maker') {
            $rules = array_intersect_key($rules, array_flip(['actual_billing_value', 'invoice_no']));
        } elseif ($request->user()->employee && $request->user()->employee->role === 'backout_maker') {
            $rules = array_intersect_key($rules, array_flip(['backout_term_date', 'backout_term_month', 'backout_term_year', 'reason_of_attrition', 'type_of_attrition', 'bo_type',]));
            if ($statusId == 8) { // Backout
                $rules['backout_term_date'] = 'required|date';
                $rules['backout_term_month'] = 'required|string|regex:/^\d{4}-\d{2}$/';
                $rules['backout_term_year'] = 'required|integer|min:1900|max:9999';
                if ($type === 'temporary') {
                    $rules['bo_type'] = 'required|in:Client BO,Candidate BO';
                }
            } elseif ($statusId == 9) { // Termination
                $rules['backout_term_date'] = 'required|date';
                $rules['backout_term_month'] = 'required|string|regex:/^\d{4}-\d{2}$/';
                $rules['backout_term_year'] = 'required|integer|min:1900|max:9999';
                $rules['reason_of_attrition'] = 'required|string|max:1000';
                $rules['type_of_attrition'] = 'required|in:Voluntary,Involuntary';
            }
        }

        \Log::info("Validation rules applied for {$type}:", ['rules' => $rules, 'request_data' => $request->all()]);

        return $rules;
    }

    private function getFieldConfig()
    {
        return [
            'India' => [
                'temporary' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'hire_type', 'label' => 'Hire Type'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'business_unit_id', 'label' => 'Business Unit'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'dm_id', 'label' => 'Delivery Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_code', 'label' => 'Consultant Code'],
                    ['name' => 'consultant_name', 'label' => 'Consultant Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'sap_id', 'label' => 'SAP ID'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'client_reporting_manager', 'label' => 'Client Reporting Manager'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backout/Term Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backout/Term Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backout/Term Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'bo_type', 'label' => 'BO Type'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'pay_rate', 'label' => 'Pay Rate'],
                    ['name' => 'pay_rate_1', 'label' => 'Pay Rate 1'],
                    ['name' => 'bill_rate', 'label' => 'Bill Rate'],
                    ['name' => 'gp_month', 'label' => 'GP/Month'],
                    ['name' => 'otc', 'label' => 'OTC'],
                    ['name' => 'otc_split', 'label' => 'OTC Split'],
                    ['name' => 'msp_fees', 'label' => 'MSP Fees'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'final_gp', 'label' => 'Final GP'],
                    ['name' => 'percentage_gp', 'label' => '% GP'],
                    ['name' => 'end_client', 'label' => 'End Client'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                    ['name' => 'process_status', 'label' => 'Process Status'],
                    ['name' => 'form_status', 'label' => 'Job Seeker Status'],
                ],
                'permanent' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'business_unit_id', 'label' => 'Business Unit'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'dm_id', 'label' => 'Delivery Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_name', 'label' => 'Candidate Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'select_month', 'label' => 'Select Month'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backout Out Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backout Out Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backout Out Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'ctc_offered', 'label' => 'CTC Offered'],
                    ['name' => 'billing_value', 'label' => 'Billing Value'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'loaded_gp', 'label' => 'Loaded GP'],
                    ['name' => 'final_billing_value', 'label' => 'Final Billing Value'],
                    ['name' => 'actual_billing_value', 'label' => 'Actual Billing Value'],
                    ['name' => 'invoice_no', 'label' => 'Invoice No'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                    ['name' => 'process_status', 'label' => 'Process Status'],
                    ['name' => 'form_status', 'label' => 'Job Seeker Status'],
                ],
            ],
            'APAC' => [
                'temporary' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'hire_type', 'label' => 'Hire Type'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_code', 'label' => 'Consultant Code'],
                    ['name' => 'consultant_name', 'label' => 'Consultant Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'sap_id', 'label' => 'SAP ID'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backoutout/Term Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backoutout/Term Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backoutout/Term Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'bo_type', 'label' => 'BO Type'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'pay_rate', 'label' => 'Pay Rate'],
                    ['name' => 'pay_rate_1', 'label' => 'Pay Rate 1'],
                    ['name' => 'bill_rate', 'label' => 'Bill Rate'],
                    ['name' => 'gp_month', 'label' => 'GP/Month'],
                    ['name' => 'msp_fees', 'label' => 'MSP Fees'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'final_gp', 'label' => 'Final GP'],
                    ['name' => 'percentage_gp', 'label' => '% GP'],
                    ['name' => 'end_client', 'label' => 'End Client'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                    ['name' => 'process_status', 'label' => 'Process Status'],
                    ['name' => 'form_status', 'label' => 'Job Seeker Status'],
                ],
                'permanent' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_name', 'label' => 'Candidate Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'select_month', 'label' => 'Select Month'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backout Out Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backout Out Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backout Out Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'ctc_offered', 'label' => 'CTC Offered'],
                    ['name' => 'billing_value', 'label' => 'Billing Value'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'loaded_gp', 'label' => 'Loaded GP'],
                    ['name' => 'final_billing_value', 'label' => 'Final Billing Value'],
                    ['name' => 'actual_billing_value', 'label' => 'Actual Billing Value'],
                    ['name' => 'invoice_no', 'label' => 'Invoice No'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                    ['name' => 'process_status', 'label' => 'Process Status'],
                    ['name' => 'form_status', 'label' => 'Job Seeker Status'],
                ],
            ],
            'EU-UK' => [
                'temporary' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'hire_type', 'label' => 'Hire Type'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'end_client', 'label' => 'End Client'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'consultant_name', 'label' => 'Consultant Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backoutout/End Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backoutout/End Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backoutout/End Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'bo_type', 'label' => 'BO Type'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'pay_rate', 'label' => 'Pay Rate'],
                    ['name' => 'bill_rate', 'label' => 'Bill Rate'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'pay_rate_usd', 'label' => 'Pay Rate (USD)'],
                    ['name' => 'bill_rate_usd', 'label' => 'Bill Rate (USD)'],
                    ['name' => 'basic_pay_rate', 'label' => 'Basic Pay Rate'],
                    ['name' => 'benefits', 'label' => 'Benefits'],
                    ['name' => 'gp_hour', 'label' => 'GP/Hour'],
                    ['name' => 'gp_hour_usd', 'label' => 'GP/Hour (USD)'],
                    ['name' => 'percentage_gp', 'label' => '% GP'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'client_reporting_manager', 'label' => 'Manager'],
                    ['name' => 'consultant_code', 'label' => 'Placement Code (Ceipal)'],
                    ['name' => 'sources', 'label' => 'Sources'],
                    ['name' => 'process_status', 'label' => 'Process Status'],
                    ['name' => 'form_status', 'label' => 'Job Seeker Status'],
                ],
                'permanent' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_name', 'label' => 'Candidate Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'select_month', 'label' => 'Select Month'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backout Out Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backout Out Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backout Out Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'ctc_offered', 'label' => 'CTC Offered'],
                    ['name' => 'billing_value', 'label' => 'Billing Value'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'loaded_gp', 'label' => 'Loaded GP'],
                    ['name' => 'final_billing_value', 'label' => 'Final Billing Value'],
                    ['name' => 'actual_billing_value', 'label' => 'Actual Billing Value'],
                    ['name' => 'invoice_no', 'label' => 'Invoice No'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                    ['name' => 'process_status', 'label' => 'Process Status'],
                    ['name' => 'form_status', 'label' => 'Job Seeker Status'],
                ],
            ],
            'Aegis' => [
                'temporary' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'hire_type', 'label' => 'Hire Type'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_code', 'label' => 'Placement Code (Ceipal)'],
                    ['name' => 'consultant_name', 'label' => 'Consultant Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'sap_id', 'label' => 'SAP ID'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backoutout Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backoutout Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backoutout Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'bo_type', 'label' => 'BO Type'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'pay_rate', 'label' => 'Pay Rate'],
                    ['name' => 'pay_rate_1', 'label' => 'Pay Rate 1'],
                    ['name' => 'bill_rate', 'label' => 'Bill Rate'],
                    ['name' => 'gp_month', 'label' => 'GP/Month'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'final_gp', 'label' => 'Final GP'],
                    ['name' => 'percentage_gp', 'label' => '% GP'],
                    ['name' => 'end_client', 'label' => 'End Client'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                    ['name' => 'domain', 'label' => 'Domain'],
                    ['name' => 'process_status', 'label' => 'Process Status'],
                    ['name' => 'form_status', 'label' => 'Job Seeker Status'],
                ],
                'permanent' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_name', 'label' => 'Candidate Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'select_month', 'label' => 'Select Month'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backout Out Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backout Out Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backout Out Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'ctc_offered', 'label' => 'CTC Offered'],
                    ['name' => 'billing_value', 'label' => 'Billing Value'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'loaded_gp', 'label' => 'Loaded GP'],
                    ['name' => 'final_billing_value', 'label' => 'Final Billing Value'],
                    ['name' => 'actual_billing_value', 'label' => 'Actual Billing Value'],
                    ['name' => 'invoice_no', 'label' => 'Invoice No'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                    ['name' => 'domain', 'label' => 'Domain'],
                    ['name' => 'process_status', 'label' => 'Process Status'],
                    ['name' => 'form_status', 'label' => 'Job Seeker Status'],
                ],
            ],
        ];
    }

    public function downloadTemplate(Request $request)
    {
        try {
            if (ob_get_level()) {
                ob_end_clean();
            }

            $type = $request->route()->defaults['type'] ?? 'permanent';
            $user = auth()->user();
            $employee = $user->employee;
            $isAdmin = $user->role === 'admin';
            $isMaker = $employee && $employee->role === 'maker';

            if (!$isAdmin && !$isMaker) {
                Log::warning("Unauthorized access attempt to download template", [
                    'user_id' => $user->id,
                    'type' => $type,
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $companyId = $isAdmin ? ($request->input('company_id') ?? session('selected_company_id')) : ($employee ? $employee->company_id : null);
            if (!$companyId || !is_numeric($companyId)) {
                Log::error("Invalid company ID provided", [
                    'user_id' => $user->id,
                    'company_id' => $companyId,
                    'type' => $type,
                ]);
                return response()->json(['error' => 'Invalid company ID'], 400);
            }

            if ($isAdmin) {
                session(['selected_company_id' => $companyId]);
            }

            $company = CompanyMaster::findOrFail($companyId);
            $region = in_array($company->region, ['India', 'APAC', 'EU-UK', 'Aegis']) ? $company->region : 'India';
            $fieldConfig = $this->getFieldConfig();
            $fields = $fieldConfig[$region][strtolower($type)] ?? [];
            $statusValue = $type === 'temporary' ? 0 : 1;

            if (empty($fields)) {
                Log::error("No fields found for region and type", [
                    'region' => $region,
                    'type' => $type,
                    'user_id' => $user->id,
                ]);
                return response()->json(['error' => 'Invalid configuration for region and type'], 400);
            }

            $fields = array_filter($fields, function ($field) {
                return !in_array($field['name'], ['company_name', 'company_id']);
            });

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $fields = array_merge([['name' => 'company_id', 'label' => 'Country']], $fields);
            $headers = array_column($fields, 'label');

            if (count($headers) > 16384) {
                Log::error("Too many columns in field configuration", [
                    'region' => $region,
                    'type' => $type,
                    'header_count' => count($headers),
                    'user_id' => $user->id,
                ]);
                return response()->json(['error' => 'Too many columns in template'], 400);
            }

            foreach ($headers as $index => $header) {
                $column = Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue("{$column}1", $header);
            }
            $sheet->getStyle('1:1')->getFont()->setBold(true);

            $sheet->setCellValue('A2', $company->name);

            $dropdownFields = [
                'company_id' => [$companyId => $company->name],
                'location_id' => BranchMaster::where('company_id', $companyId)
                    ->where('branch_status', $statusValue)
                    ->pluck('name', 'id')
                    ->toArray(),
                'client_id' => Client::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('client_status', $statusValue)
                    ->pluck('client_name', 'id')
                    ->toArray(),
                'status_id' => StatusMaster::query()
                    ->when($type === 'temporary', function ($query) {
                        return $query->where('status', '!=', 'FTE Conversion Fee');
                    })
                    ->pluck('status', 'id')
                    ->toArray(),
                'am_id' => Employee::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('designation', 'AM')
                    ->pluck('name', 'id')
                    ->toArray(),
                'dm_id' => Employee::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('designation', 'DM')
                    ->pluck('name', 'id')
                    ->toArray(),
                'tl_id' => Employee::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('designation', 'TL')
                    ->pluck('name', 'id')
                    ->toArray(),
                'recruiter_id' => Employee::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('designation', 'Recruiter')
                    ->pluck('name', 'id')
                    ->toArray(),
                'business_unit_id' => BusinessUnitMaster::where('company_id', $companyId)
                    ->where('unit_status', $statusValue)
                    ->pluck('unit', 'id')
                    ->toArray(),
                'bo_type' => ['Client BO' => 'Client BO', 'Candidate BO' => 'Candidate BO'],
                'type_of_attrition' => ['Voluntary' => 'Voluntary', 'Involuntary' => 'Involuntary'],
                'form_status' => ['Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected'],
                'process_status' => [
                    1 => 'Maker Create/edit',
                    2 => 'Checker approve/reject',
                    3 => 'PO maker edit temporary approved joined',
                    4 => 'PO checker approve/reject pending temporary joined',
                    5 => 'Finance maker edit permanent approved joined',
                    6 => 'Finance checker approve/reject permanent pending joined',
                    7 => 'Backout maker edit temporary/permanent approved joined',
                    8 => 'Backout checker approve/reject temporary/permanent pending joined',
                ],
            ];

            Log::info("Dropdown fields configured: " . json_encode(array_keys($dropdownFields)));
            foreach ($dropdownFields as $field => $options) {
                Log::info("Dropdown options for {$field}: " . json_encode($options));
            }

            $dropdownSheet = $spreadsheet->createSheet();
            $dropdownSheet->setTitle('DropdownData');
            $row = 1;

            foreach ($dropdownFields as $field => $options) {
                $fieldIndex = array_search($field, array_column($fields, 'name'));
                if ($fieldIndex === false) {
                    Log::warning("Field {$field} not found in field configuration, skipping dropdown");
                    continue;
                }

                if (empty($options)) {
                    Log::warning("No options available for {$field}, skipping dropdown");
                    continue;
                }

                $column = Coordinate::stringFromColumnIndex($fieldIndex + 1);
                $optionList = [];
                $startRow = $row;

                foreach ($options as $id => $name) {
                    $dropdownSheet->setCellValue("A{$row}", $id);
                    $dropdownSheet->setCellValue("B{$row}", $name);
                    $optionList[] = strval($name);
                    $row++;
                }

                if (!empty($optionList)) {
                    $validation = $sheet->getDataValidation("{$column}2:{$column}1000");
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowDropDown(true);

                    if ($field === 'process_status') {
                        $validation->setFormula1("=DropdownData!B{$startRow}:B" . ($startRow + count($optionList) - 1));
                    } else {
                        $escapedOptions = array_map(function ($value) {
                            return str_replace(',', '\,', str_replace('"', '\"', $value));
                        }, $optionList);
                        $formula = '"' . implode(',', $escapedOptions) . '"';
                        if (strlen($formula) > 255) {
                            Log::warning("Dropdown formula for {$field} exceeds 255 characters, using range instead");
                            $validation->setFormula1("=DropdownData!B{$startRow}:B" . ($startRow + count($optionList) - 1));
                        } else {
                            $validation->setFormula1($formula);
                        }
                    }
                    Log::info("Dropdown created for {$field} in column {$column} with formula: {$validation->getFormula1()}");
                } else {
                    Log::warning("No valid options for {$field}, dropdown not created");
                }
            }
            $dropdownSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

            $filename = $region . '_' . strtolower($type) . '_jobseeker.xlsx';
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
                'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
                'Cache-Control' => 'cache, must-revalidate',
                'Pragma' => 'public',
            ]);

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            Log::error("Failed to generate template: {$e->getMessage()}", [
                'user_id' => auth()->id(),
                'type' => $type,
                'company_id' => $companyId ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to generate template: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error("Unexpected error in downloadTemplate: {$e->getMessage()}", [
                'user_id' => auth()->id(),
                'type' => $type,
                'company_id' => $companyId ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }



    public function import(Request $request)
    {
        $type = $request->route()->defaults['type'];
        $user = auth()->user();
        $employee = $user->employee;
        $isAdmin = $user->role === 'admin';
        $isMaker = $employee && $employee->role === 'maker';

        if (!$isAdmin && !$isMaker) {
            return redirect()->route("job-seekers.{$type}.index")->with('error', 'Unauthorized');
        }

        $companyId = $isAdmin ? $request->company_id : ($employee->company_id ?? null);
        if (!$companyId) {
            Log::error("No company_id provided for " . ($isAdmin ? 'admin' : 'employee') . " user_id: {$user->id}");
            return redirect()->route("job-seekers.{$type}.index")->with('error', 'No company selected or employee not assigned to a company');
        }

        $request->validate([
            'company_id' => $isAdmin ? ['required', 'string', 'exists:company_masters,id'] : ['nullable'],
            'file' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ]);

        $company = CompanyMaster::findOrFail($companyId);
        $region = in_array($company->region, ['India', 'APAC', 'EU-UK', 'Aegis']) ? $company->region : 'India';
        $fieldConfig = $this->getFieldConfig();
        $fields = array_merge(['company_id'], array_column($fieldConfig[$region][strtolower($type)], 'name'));
        $statusValue = $type === 'temporary' ? 0 : 1;

        Log::info("Import started: user_id={$user->id}, role=" . ($isAdmin ? 'admin' : 'employee') . ", company_id={$companyId}, type={$type}, region={$region}, company_name={$company->name}");

        $spreadsheet = IOFactory::load($request->file('file')->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $headers = array_shift($rows);
        $fieldMap = [];
        foreach ($fields as $field) {
            $label = $field === 'company_id' ? 'Country' : $this->getFieldLabel($field, $fieldConfig[$region][strtolower($type)]);
            $index = array_search($label, $headers);
            if ($index !== false) {
                $fieldMap[$index] = $field;
            }
        }

        Log::info("Field map: " . json_encode($fieldMap));

        $errors = [];
        $successCount = 0;
        $newJobSeekers = [];

        foreach ($rows as $index => $row) {
            if (empty(array_filter($row, fn($value) => !is_null($value) && $value !== ''))) {
                Log::info("Row " . ($index + 1) . ": Skipped empty row");
                continue;
            }

            $data = [];
            foreach ($fieldMap as $col => $field) {
                $value = $row[$col] ?? null;
                if (in_array($field, ['select_month', 'join_month', 'backout_term_month', 'po_end_month'])) {
                    $data[$field] = $this->normalizeMonth($value);
                } elseif (in_array($field, ['join_year', 'backout_term_year', 'po_end_year'])) {
                    $data[$field] = $this->normalizeYear($value);
                } elseif (in_array($field, ['selection_date', 'offer_date', 'join_date', 'qly_date', 'backout_term_date', 'po_end_date'])) {
                    $data[$field] = $this->normalizeDate($value);
                } elseif ($field === 'process_status') {
                    $data[$field] = $this->normalizeProcessStatus($value);
                } elseif ($field === 'form_status') {
                    $data[$field] = $this->normalizeFormStatus($value);
                } else {
                    $data[$field] = $value;
                }
            }

            Log::info("Row " . ($index + 1) . " raw data: " . json_encode($row));
            Log::info("Row " . ($index + 1) . " processed data: " . json_encode($data));

            if ($isAdmin && isset($data['company_id']) && $data['company_id'] && $data['company_id'] !== $company->name) {
                $errors[] = "Row " . ($index + 1) . ": Invalid country (company name): {$data['company_id']}. Expected: {$company->name}";
                Log::warning("Row " . ($index + 1) . ": Skipped due to invalid company name");
                continue;
            }

            $data['company_id'] = (string) $companyId;
            $data['job_seeker_type'] = ucfirst($type);
            if ($isMaker && $employee) {
                $data['maker_id'] = (string) $employee->id;
                if ($employee->checker_id) {
                    $data['checker_id'] = (string) $employee->checker_id;
                }
            }
            $data['process_status'] = $data['process_status'] ?? 1;
            $data['form_status'] = $data['form_status'] ?? 'Pending';

            $dropdownFields = [
                'location_id' => BranchMaster::where('company_id', $companyId)
                    ->where('branch_status', $statusValue)
                    ->pluck('id', 'name')
                    ->toArray(),
                'client_id' => Client::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('client_status', $statusValue)
                    ->pluck('id', 'client_name')
                    ->toArray(),
                'status_id' => StatusMaster::query()
                    ->when($type === 'temporary', function ($query) {
                        return $query->where('status', '!=', 'FTE Conversion Fee');
                    })
                    ->pluck('id', 'status')
                    ->toArray(),
                'am_id' => Employee::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('designation', 'AM')
                    ->pluck('id', 'name')
                    ->toArray(),
                'dm_id' => Employee::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('designation', 'DM')
                    ->pluck('id', 'name')
                    ->toArray(),
                'tl_id' => Employee::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('designation', 'TL')
                    ->pluck('id', 'name')
                    ->toArray(),
                'recruiter_id' => Employee::where('company_id', $companyId)
                    ->where('status', 'active')
                    ->where('designation', 'Recruiter')
                    ->pluck('id', 'name')
                    ->toArray(),
                'business_unit_id' => BusinessUnitMaster::where('company_id', $companyId)
                    ->where('unit_status', $statusValue)
                    ->pluck('id', 'unit')
                    ->toArray(),
                'bo_type' => ['Client BO' => 'Client BO', 'Candidate BO' => 'Candidate BO'],
                'type_of_attrition' => ['Voluntary' => 'Voluntary', 'Involuntary' => 'Involuntary'],
                'form_status' => ['Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected'],
                'process_status' => [
                    'Maker Create/edit' => 1,
                    'Checker approve/reject' => 2,
                    'PO maker edit temporary approved joined' => 3,
                    'PO checker approve/reject pending temporary joined' => 4,
                    'Finance maker edit permanent approved joined' => 5,
                    'Finance checker approve/reject permanent pending joined' => 6,
                    'Backout maker edit temporary/permanent approved joined' => 7,
                    'Backout checker approve/reject temporary/permanent pending joined' => 8,
                ],
            ];

            Log::info("Row " . ($index + 1) . " dropdown options: " . json_encode(array_map('array_keys', $dropdownFields)));

            // Map dropdown values, handling both string and numeric process_status
            foreach ($dropdownFields as $field => $options) {
                if ($field === 'process_status' && isset($data[$field]) && is_numeric($data[$field])) {
                    // If process_status is numeric and valid, keep it
                    if (in_array((int) $data[$field], range(1, 8))) {
                        $data[$field] = (string) $data[$field];
                    } else {
                        $errors[] = "Row " . ($index + 1) . ": Invalid numeric value for {$field}: {$data[$field]}. Available options: " . implode(', ', array_keys($options));
                        $data[$field] = '1'; // Default to 1 for invalid numeric values
                        Log::warning("Row " . ($index + 1) . ": Invalid numeric {$field}: {$data[$field]}, defaulting to 1");
                    }
                } elseif (isset($data[$field]) && $data[$field] && isset($options[$data[$field]])) {
                    $data[$field] = (string) $options[$data[$field]];
                } elseif (isset($data[$field]) && $data[$field]) {
                    $errors[] = "Row " . ($index + 1) . ": Invalid value for {$field}: {$data[$field]}. Available options: " . implode(', ', array_keys($options));
                    $data[$field] = null;
                    Log::warning("Row " . ($index + 1) . ": Invalid {$field}: {$data[$field]}");
                }
            }

            Log::info("Row " . ($index + 1) . " dropdown mapped data: " . json_encode($data));

            $validator = Validator::make($data, $this->validationExcelRules($type, $region));

            if ($validator->fails()) {
                $errors[] = "Row " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                Log::warning("Row " . ($index + 1) . ": Validation failed: " . implode(', ', $validator->errors()->all()));
                continue;
            }

            try {
                $jobSeeker = JobSeeker::create($data);
                $newJobSeekers[] = $jobSeeker;
                $successCount++;
                Log::info("Row " . ($index + 1) . ": Successfully created job seeker");
            } catch (\Exception $e) {
                Log::error("Row " . ($index + 1) . ": Failed to create job seeker: " . $e->getMessage(), [
                    'data' => $data,
                    'trace' => $e->getTraceAsString(),
                ]);
                $errors[] = "Row " . ($index + 1) . ": Failed to create job seeker: " . Str::limit($e->getMessage(), 100);
                continue;
            }
        }

        Log::info("Import completed: successCount={$successCount}, errors=" . json_encode($errors));

        return redirect()->route("job-seekers.{$type}.index")->with([
            'success' => "$successCount job seekers imported successfully",
            'counts' => $this->getCounts($user, $employee, $type, $companyId),
            'jobSeekers' => $newJobSeekers,
        ]);
    }

    private function normalizeYear($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        // Handle plain year (e.g., "2025" or "2025.00")
        if (preg_match('/^\d{4}(\.0+)?$/', $value)) {
            return (string) floor((float) $value); // Convert to integer then string, e.g., "2025.00" -> "2025"
        }

        // Handle two-digit year (e.g., "25" -> "2025")
        if (preg_match('/^\d{2}$/', $value)) {
            $year = (int) $value;
            return (string) ($year < 50 ? 2000 + $year : 1900 + $year);
        }

        // Handle date formats (e.g., "6/12/2025", "2025-06-12", "12-06-2025")
        try {
            $date = \DateTime::createFromFormat('m/d/Y', $value)
                ?: \DateTime::createFromFormat('d-m-Y', $value)
                ?: \DateTime::createFromFormat('Y-m-d', $value);
            if ($date) {
                $year = $date->format('Y');
                if (preg_match('/^\d{4}$/', $year)) {
                    return (string) $year;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to parse date for year: $value, Error: {$e->getMessage()}");
        }

        // Handle Excel date serial
        try {
            if (is_numeric($value) && $value > 0) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                $year = $date->format('Y');
                if (preg_match('/^\d{4}$/', $year)) {
                    return (string) $year;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to parse Excel date serial for year: $value, Error: {$e->getMessage()}");
        }

        Log::warning("Invalid year format: $value");
        return null;
    }

    private function normalizeMonth($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        // Handle month-year format (e.g., "May-25", "2025-05")
        try {
            $date = \DateTime::createFromFormat('M-y', $value)
                ?: \DateTime::createFromFormat('Y-m', $value);
            if ($date) {
                return $date->format('Y-m');
            }
        } catch (\Exception $e) {
            Log::warning("Failed to parse month: $value, Error: {$e->getMessage()}");
        }

        // Handle date formats (e.g., "6/12/2025", "2025-06-12")
        try {
            $date = \DateTime::createFromFormat('m/d/Y', $value)
                ?: \DateTime::createFromFormat('d-m-Y', $value)
                ?: \DateTime::createFromFormat('Y-m-d', $value);
            if ($date) {
                return $date->format('Y-m');
            }
        } catch (\Exception $e) {
            Log::warning("Failed to parse date for month: $value, Error: {$e->getMessage()}");
        }

        // Handle Excel date serial
        try {
            if (is_numeric($value) && $value > 0) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m');
            }
        } catch (\Exception $e) {
            Log::warning("Failed to parse Excel date serial for month: $value, Error: {$e->getMessage()}");
        }

        Log::warning("Invalid month format: $value");
        return null;
    }

    private function normalizeDate($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        // Handle date formats (e.g., "6/12/2025", "2025-06-12", "12-06-2025")
        try {
            $date = \DateTime::createFromFormat('m/d/Y', $value)
                ?: \DateTime::createFromFormat('d-m-Y', $value)
                ?: \DateTime::createFromFormat('Y-m-d', $value);
            if ($date) {
                return $date->format('Y-m-d');
            }
        } catch (\Exception $e) {
            Log::warning("Failed to parse date: $value, Error: {$e->getMessage()}");
        }

        // Handle Excel date serial
        try {
            if (is_numeric($value) && $value > 0) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            }
        } catch (\Exception $e) {
            Log::warning("Failed to parse Excel date serial: $value, Error: {$e->getMessage()}");
        }

        Log::warning("Invalid date format: $value");
        return null;
    }

    private function normalizeProcessStatus($value)
    {
        if (empty($value)) {
            return 1; // Default to 1 if not provided
        }

        $value = trim($value);
        $processStatusMap = [
            'Maker Create/edit' => 1,
            'Checker approve/reject' => 2,
            'PO maker edit temporary approved joined' => 3,
            'PO checker approve/reject pending temporary joined' => 4,
            'Finance maker edit permanent approved joined' => 5,
            'Finance checker approve/reject permanent pending joined' => 6,
            'Backout maker edit temporary/permanent approved joined' => 7,
            'Backout checker approve/reject temporary/permanent pending joined' => 8,
        ];

        if (isset($processStatusMap[$value])) {
            return $processStatusMap[$value];
        }

        if (is_numeric($value) && $value >= 1 && $value <= 8 && floor($value) == $value) {
            return (int) $value;
        }

        Log::warning("Invalid process status: $value");
        return 1; // Default to 1 for invalid values
    }

    private function normalizeFormStatus($value)
    {
        if (empty($value)) {
            return 'Pending'; // Default to Pending if not provided
        }

        $value = trim($value);
        $validStatuses = ['Pending', 'Approved', 'Rejected'];
        if (in_array($value, $validStatuses)) {
            return $value;
        }

        Log::warning("Invalid form status: $value");
        return 'Pending'; // Default to Pending for invalid values
    }

    private function validationExcelRules($type, $region)
    {
        $fieldConfig = $this->getFieldConfig();
        $fields = array_merge(['company_id', 'job_seeker_type', 'maker_id', 'checker_id'], array_column($fieldConfig[$region][strtolower($type)], 'name'));
        $fields = array_filter($fields, function ($field) {
            return $field !== 'company_name';
        });
        $rules = [];

        foreach ($fields as $field) {
            $rule = [];
            if (in_array($field, ['company_id', 'client_id', 'status_id', 'location_id', 'am_id', 'business_unit_id', 'dm_id', 'tl_id', 'recruiter_id', 'maker_id', 'checker_id'])) {
                $rule = ['nullable', 'string', 'exists:' . $this->getTableForField($field) . ',id'];
            } elseif ($field === 'job_seeker_type') {
                $rule = ['required', 'in:Temporary,Permanent'];
            } elseif (in_array($field, ['selection_date', 'offer_date', 'join_date', 'qly_date', 'backout_term_date', 'po_end_date'])) {
                $rule = ['nullable', 'date'];
            } elseif (in_array($field, ['select_month', 'join_month', 'backout_term_month', 'po_end_month'])) {
                $rule = ['nullable', 'string', 'regex:/^\d{4}-\d{2}$/'];
            } elseif (in_array($field, ['join_year', 'backout_term_year', 'po_end_year'])) {
                $rule = ['nullable', 'string', 'regex:/^\d{4}$/'];
            } elseif (in_array($field, ['pay_rate', 'pay_rate_1', 'bill_rate', 'gp_month', 'otc', 'otc_split', 'msp_fees', 'loaded_cost', 'final_gp', 'percentage_gp'])) {
                $rule = ['nullable', 'numeric'];
            } elseif ($field === 'process_status') {
                $rule = ['nullable', 'integer', 'between:1,8'];
            } elseif ($field === 'bo_type') {
                $rule = ['nullable', 'in:Client BO,Candidate BO'];
            } elseif ($field === 'type_of_attrition') {
                $rule = ['nullable', 'in:Voluntary,Involuntary'];
            } elseif ($field === 'form_status') {
                $rule = ['nullable', 'in:Pending,Approved,Rejected'];
            } else {
                $rule = ['nullable', 'string', 'max:255'];
            }
            $rules[$field] = $rule;
        }

        if ($type === 'temporary') {
            $rules['status_id'][] = function ($attribute, $value, $fail) {
                $fteStatus = StatusMaster::where('status', 'FTE Conversion Fee')->first();
                if ($fteStatus && $value == $fteStatus->id) {
                    $fail("The status 'FTE Conversion Fee' is not allowed for temporary job seekers.");
                }
            };
        }

        return $rules;
    }

    private function getTableForField($field)
    {
        return match ($field) {
            'company_id' => 'company_masters',
            'client_id' => 'clients',
            'status_id' => 'status_masters',
            'location_id' => 'branch_masters',
            'am_id' => 'employees',
            'dm_id' => 'employees',
            'tl_id' => 'employees',
            'recruiter_id' => 'employees',
            'business_unit_id' => 'business_unit_masters',
            'checker_id' => 'employees',
            'maker_id' => 'employees',
            'po_maker_id' => 'employees',
            'po_checker_id' => 'employees',
            'finance_maker_id' => 'employees',
            'finance_checker_id' => 'employees',
            'backout_maker_id' => 'employees',
            'backout_checker_id' => 'employees',
            default => 'job_seekers',
        };
    }

    private function getFieldLabel($field, $config)
    {
        foreach ($config as $item) {
            if ($item['name'] === $field) {
                return $item['label'];
            }
        }
        return $field;
    }
}