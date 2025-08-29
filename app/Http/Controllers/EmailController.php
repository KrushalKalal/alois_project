<?php

namespace App\Http\Controllers;

use App\Models\MainEmail;
use App\Models\CompanyMaster;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class EmailController extends Controller
{
    public function index()
    {
        return Inertia::render('Emails/Index', [
            'auth' => auth()->user(),
            'mainEmails' => MainEmail::select('id', 'email', 'name', 'is_active')->get(),
            'filters' => ['search' => '', 'per_page' => 10],
        ]);
    }

    public function create()
    {
        try {
            return Inertia::render('Emails/Form', [
                'auth' => auth()->user(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load Main Email create form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load create form.');
        }
    }

    public function storeMainEmail(Request $request)
    {
        Log::info('Store Main Email Request:', $request->all());

        try {
            $request->validate([
                'email' => 'required|email|unique:main_emails,email',
                'name' => 'nullable|string|max:255',
                'password' => 'required_if:is_active,true|string|min:8|nullable',
                'is_active' => 'required|boolean',
            ]);

            if ($request->is_active) {
                MainEmail::where('is_active', true)->update(['is_active' => false]);
            }

            $plainTextPassword = $request->password ? trim($request->password) : null;
            $hashedPassword = $plainTextPassword ? Hash::make($plainTextPassword) : null;
            Log::info('Hashed password for store:', ['password' => $hashedPassword]);

            $mainEmail = MainEmail::create([
                'email' => $request->email,
                'name' => $request->name,
                'password' => $hashedPassword,
                'is_active' => $request->is_active,
            ]);

            Log::info('MainEmail created:', $mainEmail->toArray());

            if ($mainEmail->is_active && $plainTextPassword) {
                MainEmail::setActiveEmail($mainEmail->id, $plainTextPassword);
            }

            return redirect()->route('emails.index')->with('success', 'Main email added successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create Main Email: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create main email: ' . $e->getMessage());
        }
    }

    public function edit(MainEmail $mainEmail)
    {
        try {
            return Inertia::render('Emails/Form', [
                'auth' => auth()->user(),
                'mainEmail' => [
                    'id' => $mainEmail->id,
                    'email' => $mainEmail->email,
                    'name' => $mainEmail->name,
                    'password' => null,
                    'is_active' => $mainEmail->is_active,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load Main Email edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load edit form.');
        }
    }

    public function updateMainEmail(Request $request, $id)
    {
        Log::info('Update Main Email Request:', $request->all());

        try {
            $mainEmail = MainEmail::findOrFail($id);
            $wasActive = $mainEmail->is_active;

            $passwordRule = ($wasActive && $request->is_active) ? 'nullable|string|min:8' : 'required_if:is_active,true|string|min:8|nullable';

            $request->validate([
                'email' => 'required|email|unique:main_emails,email,' . $id,
                'name' => 'nullable|string|max:255',
                'password' => $passwordRule,
                'is_active' => 'required|boolean',
            ]);

            $plainTextPassword = $request->password ? trim($request->password) : null;
            $hashedPassword = $plainTextPassword ? Hash::make($plainTextPassword) : $mainEmail->password;

            if ($request->is_active && !$wasActive) {
                MainEmail::where('is_active', true)->update(['is_active' => false]);
            }

            $mainEmail->update([
                'email' => $request->email,
                'name' => $request->name,
                'password' => $hashedPassword,
                'is_active' => $request->is_active,
            ]);

            Log::info('MainEmail updated:', $mainEmail->toArray());

            if ($mainEmail->is_active) {
                $envPassword = $plainTextPassword ?? ($wasActive ? env('MAIL_PASSWORD') : null);
                if (!$envPassword) {
                    throw new \Exception('No plain-text password available for active email.');
                }
                MainEmail::setActiveEmail($mainEmail->id, $envPassword);
            }

            return redirect()->route('emails.index')->with('success', 'Main email updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update main email: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update main email: ' . $e->getMessage());
        }
    }

    public function destroy(MainEmail $mainEmail)
    {
        try {
            Log::info('Attempting to delete MainEmail:', ['id' => $mainEmail->id, 'email' => $mainEmail->email]);

            if ($mainEmail->is_active) {
                Log::warning('Attempt to delete active Main Email: ' . $mainEmail->email);
                return redirect()->route('emails.index')->with('error', 'Cannot delete an active email.');
            }

            $mainEmail->delete();
            Log::info('MainEmail deleted:', ['id' => $mainEmail->id, 'email' => $mainEmail->email]);

            return redirect()->route('emails.index')->with('success', 'Main email deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete Main Email: ' . $e->getMessage(), ['id' => $mainEmail->id]);
            return redirect()->route('emails.index')->with('error', 'Failed to delete main email: ' . $e->getMessage());
        }
    }

    public function sendHelloEmail(Request $request)
    {
        try {
            $request->validate(['company_id' => 'required|exists:company_masters,id']);

            $company = CompanyMaster::findOrFail($request->company_id);
            $toEmails = $company->to_emails ?? [];
            $ccEmails = $company->cc_emails ?? [];

            Log::info('Sending hello email', [
                'company_id' => $company->id,
                'to_emails' => $toEmails,
                'cc_emails' => $ccEmails,
            ]);

            if (empty($toEmails)) {
                Log::warning('No To email addresses found for company: ' . $company->id);
                return redirect()->back()->with('error', 'No To email addresses found for this company.');
            }

            $emailService = new EmailService();
            $sent = $emailService->sendHelloEmail($toEmails, $ccEmails);

            return redirect()->back()->with(
                $sent ? ['success' => 'Hello email sent successfully.'] : ['error' => 'Failed to send email.']
            );
        } catch (\Exception $e) {
            Log::error('Failed to send hello email: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}