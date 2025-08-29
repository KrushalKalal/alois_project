<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EmployeeMiddleware;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\CompanyMasterController;
use App\Http\Controllers\BranchMasterController;
use App\Http\Controllers\BusinessUnitMasterController;
use App\Http\Controllers\StatusMasterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobSeekerController;
use App\Http\Controllers\EmailController;



Route::get('/', function () {
    return Inertia::render('Auth/Login');
})->name('login.home');

Route::middleware('auth')->group(function () {
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/dashboard/export', [AdminDashboardController::class, 'export'])->name('admin.dashboard.export');
        Route::post('/admin/dashboard/send-email', [AdminDashboardController::class, 'sendHelloEmail'])->name('admin.dashboard.send-email');
        Route::get('/admin/profile', [ProfileController::class, 'showAdmin'])->name('admin.profile');
        Route::post('/admin/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');

        // Main Email Routes
        Route::get('/emails', [EmailController::class, 'index'])->name('emails.index');
        Route::get('/emails/create', [EmailController::class, 'create'])->name('emails.create');
        Route::post('/emails/main', [EmailController::class, 'storeMainEmail'])->name('emails.storeMainEmail');
        Route::get('/emails/{mainEmail}/edit', [EmailController::class, 'edit'])->name('emails.edit');
        Route::post('/emails/main/{mainEmail}', [EmailController::class, 'updateMainEmail'])->name('emails.updateMainEmail');

        // Company Master Routes
        Route::get('/company-masters', [CompanyMasterController::class, 'index'])->name('company-masters.index');
        Route::get('/company-masters/create', [CompanyMasterController::class, 'create'])->name('company-masters.create');
        Route::post('/company-masters', [CompanyMasterController::class, 'store'])->name('company-masters.store');
        Route::get('/company-masters/{companyMaster}', [CompanyMasterController::class, 'show'])->name('company-masters.show');
        Route::get('/company-masters/{companyMaster}/edit', [CompanyMasterController::class, 'edit'])->name('company-masters.edit');
        Route::post('/company-masters/{companyMaster}', [CompanyMasterController::class, 'update'])->name('company-masters.update');
        Route::delete('/company-masters/{companyMaster}', [CompanyMasterController::class, 'destroy'])->name('company-masters.destroy');
        Route::get('/company-masters/excel/template', [CompanyMasterController::class, 'downloadExcelTemplate'])->name('company-masters.excel-template');
        Route::post('/company-masters/excel/import', [CompanyMasterController::class, 'importFromExcel'])->name('company-masters.excel-import');

        // Branch Master Routes
        Route::get('/branch-masters', [BranchMasterController::class, 'index'])->name('branch-masters.index');
        Route::get('/branch-masters/create', [BranchMasterController::class, 'create'])->name('branch-masters.create');
        Route::post('/branch-masters', [BranchMasterController::class, 'store'])->name('branch-masters.store');
        Route::get('/branch-masters/{branchMaster}', [BranchMasterController::class, 'show'])->name('branch-masters.show');
        Route::get('/branch-masters/{branchMaster}/edit', [BranchMasterController::class, 'edit'])->name('branch-masters.edit');
        Route::post('/branch-masters/{branchMaster}', [BranchMasterController::class, 'update'])->name('branch-masters.update');
        Route::delete('/branch-masters/{branchMaster}', [BranchMasterController::class, 'destroy'])->name('branch-masters.destroy');
        Route::get('/branch-masters/excel/template', [BranchMasterController::class, 'downloadExcelTemplate'])->name('branch-masters.excel-template');
        Route::post('/branch-masters/excel/import', [BranchMasterController::class, 'importFromExcel'])->name('branch-masters.excel-import');

        // Status Master Routes
        Route::get('/status-masters', [StatusMasterController::class, 'index'])->name('status-masters.index');
        Route::get('/status-masters/create', [StatusMasterController::class, 'create'])->name('status-masters.create');
        Route::post('/status-masters', [StatusMasterController::class, 'store'])->name('status-masters.store');
        Route::get('/status-masters/{statusMaster}', [StatusMasterController::class, 'show'])->name('status-masters.show');
        Route::get('/status-masters/{statusMaster}/edit', [StatusMasterController::class, 'edit'])->name('status-masters.edit');
        Route::post('/status-masters/{statusMaster}', [StatusMasterController::class, 'update'])->name('status-masters.update');
        Route::delete('/status-masters/{statusMaster}', [StatusMasterController::class, 'destroy'])->name('status-masters.destroy');
        Route::get('/status-masters/excel/template', [StatusMasterController::class, 'downloadExcelTemplate'])->name('status-masters.excel-template');
        Route::post('/status-masters/excel/import', [StatusMasterController::class, 'importFromExcel'])->name('status-masters.excel-import');

        // Business Unit Master Routes
        Route::get('/business-unit-masters', [BusinessUnitMasterController::class, 'index'])->name('business-unit-masters.index');
        Route::get('/business-unit-masters/create', [BusinessUnitMasterController::class, 'create'])->name('business-unit-masters.create');
        Route::post('/business-unit-masters', [BusinessUnitMasterController::class, 'store'])->name('business-unit-masters.store');
        Route::get('/business-unit-masters/{businessUnitMaster}', [BusinessUnitMasterController::class, 'show'])->name('business-unit-masters.show');
        Route::get('/business-unit-masters/{businessUnitMaster}/edit', [BusinessUnitMasterController::class, 'edit'])->name('business-unit-masters.edit');
        Route::post('/business-unit-masters/{businessUnitMaster}', [BusinessUnitMasterController::class, 'update'])->name('business-unit-masters.update');
        Route::delete('/business-unit-masters/{businessUnitMaster}', [BusinessUnitMasterController::class, 'destroy'])->name('business-unit-masters.destroy');
        Route::get('/business-unit-masters/excel/template', [BusinessUnitMasterController::class, 'downloadExcelTemplate'])->name('business-unit-masters.excel-template');
        Route::post('/business-unit-masters/excel/import', [BusinessUnitMasterController::class, 'importFromExcel'])->name('business-unit-masters.excel-import');

        // Client Master Routes
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::post('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
        Route::get('/clients/excel/template', [ClientController::class, 'downloadExcelTemplate'])->name('clients.excel-template');
        Route::post('/clients/excel/import', [ClientController::class, 'importFromExcel'])->name('clients.excel-import');

        // Consultant Master Routes
        Route::get('/consultants', [ConsultantController::class, 'index'])->name('consultants.index');
        Route::get('/consultants/create', [ConsultantController::class, 'create'])->name('consultants.create');
        Route::post('/consultants', [ConsultantController::class, 'store'])->name('consultants.store');
        Route::get('/consultants/{consultant}', [ConsultantController::class, 'show'])->name('consultants.show');
        Route::get('/consultants/{consultant}/edit', [ConsultantController::class, 'edit'])->name('consultants.edit');
        Route::post('/consultants/{consultant}', [ConsultantController::class, 'update'])->name('consultants.update');
        Route::delete('/consultants/{consultant}', [ConsultantController::class, 'destroy'])->name('consultants.destroy');
        Route::get('/consultants/excel/template', [ConsultantController::class, 'downloadExcelTemplate'])->name('consultants.excel-template');
        Route::post('/consultants/excel/import', [ConsultantController::class, 'importFromExcel'])->name('consultants.excel-import');

        // Employee Master Routes
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::post('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('/employees/excel/template', [EmployeeController::class, 'downloadExcelTemplate'])->name('employees.excel-template');
        Route::post('/employees/excel/import', [EmployeeController::class, 'importFromExcel'])->name('employees.excel-import');


    });

    Route::middleware(EmployeeMiddleware::class)->group(function () {
        Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
        Route::get('/employee/dashboard/export', [EmployeeDashboardController::class, 'export'])->name('employee.dashboard.export');
        Route::get('/employee/profile', [ProfileController::class, 'showEmployee'])->name('employee.profile');
        Route::post('/employee/profile/password', [ProfileController::class, 'updatePassword'])->name('employee.profile.password');
    });

    Route::prefix('job-seekers/temporary')->name('job-seekers.temporary.')->group(function () {
        Route::get('/', [JobSeekerController::class, 'index'])->defaults('type', 'temporary')->name('index');
        Route::get('/create', [JobSeekerController::class, 'create'])->defaults('type', 'temporary')->name('create');
        Route::get('/{id}/edit', [JobSeekerController::class, 'edit'])->defaults('type', 'temporary')->name('edit');
        Route::get('/{id}/show', [JobSeekerController::class, 'show'])->defaults('type', 'temporary')->name('show');
        Route::post('/', [JobSeekerController::class, 'store'])->defaults('type', 'temporary')->name('store');
        Route::put('/{id}', [JobSeekerController::class, 'update'])->defaults('type', 'temporary')->name('update');
        Route::delete('/{id}', [JobSeekerController::class, 'destroy'])->defaults('type', 'temporary')->name('destroy');
        Route::post('/{id}/approve', [JobSeekerController::class, 'approve'])->defaults('type', 'temporary')->name('approve');
        Route::post('/{id}/reject', [JobSeekerController::class, 'reject'])->defaults('type', 'temporary')->name('reject');
        Route::get('/counts', [JobSeekerController::class, 'counts'])->defaults('type', 'temporary')->name('counts');
        Route::get('/companies/{companyId}/branches', [JobSeekerController::class, 'getBranches'])->defaults('type', 'temporary')->name('branches');
        Route::get('/companies/{companyId}/business-units', [JobSeekerController::class, 'getBusinessUnits'])->defaults('type', 'temporary')->name('business-units');
        Route::get('/companies/{companyId}/clients', [JobSeekerController::class, 'getClients'])->defaults('type', 'temporary')->name('clients');
        Route::get('/companies/{companyId}/employees', [JobSeekerController::class, 'getEmployees'])->defaults('type', 'temporary')->name('employees');
        Route::get('/template', [JobSeekerController::class, 'downloadTemplate'])->defaults('type', 'temporary')->name('template');
        Route::post('/import', [JobSeekerController::class, 'import'])->defaults('type', 'temporary')->name('import');
    });

    // Permanent Job Seekers
    Route::prefix('job-seekers/permanent')->name('job-seekers.permanent.')->group(function () {
        Route::get('/', [JobSeekerController::class, 'index'])->defaults('type', 'permanent')->name('index');
        Route::get('/create', [JobSeekerController::class, 'create'])->defaults('type', 'permanent')->name('create');
        Route::get('/{id}/edit', [JobSeekerController::class, 'edit'])->defaults('type', 'permanent')->name('edit');
        Route::get('/{id}/show', [JobSeekerController::class, 'show'])->defaults('type', 'permanent')->name('show');
        Route::post('/', [JobSeekerController::class, 'store'])->defaults('type', 'permanent')->name('store');
        Route::put('/{id}', [JobSeekerController::class, 'update'])->defaults('type', 'permanent')->name('update');
        Route::delete('/{id}', [JobSeekerController::class, 'destroy'])->defaults('type', 'permanent')->name('destroy');
        Route::post('/{id}/approve', [JobSeekerController::class, 'approve'])->defaults('type', 'permanent')->name('approve');
        Route::post('/{id}/reject', [JobSeekerController::class, 'reject'])->defaults('type', 'permanent')->name('reject');
        Route::get('/counts', [JobSeekerController::class, 'counts'])->defaults('type', 'permanent')->name('counts');
        Route::get('/companies/{companyId}/branches', [JobSeekerController::class, 'getBranches'])->defaults('type', 'permanent')->name('branches');
        Route::get('/companies/{companyId}/business-units', [JobSeekerController::class, 'getBusinessUnits'])->defaults('type', 'permanent')->name('business-units');
        Route::get('/companies/{companyId}/clients', [JobSeekerController::class, 'getClients'])->defaults('type', 'permanent')->name('clients');
        Route::get('/companies/{companyId}/employees', [JobSeekerController::class, 'getEmployees'])->defaults('type', 'permanent')->name('employees');
        Route::get('/template', [JobSeekerController::class, 'downloadTemplate'])->defaults('type', 'permanent')->name('template');
        Route::post('/import', [JobSeekerController::class, 'import'])->defaults('type', 'permanent')->name('import');
    });

});


require __DIR__ . '/auth.php';
