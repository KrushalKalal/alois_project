<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class MainEmail extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'name', 'password', 'is_active'];

    public static function setActiveEmail($id, $plainTextPassword = null)
    {
        \Log::info('setActiveEmail called', [
            'id' => $id,
            'plainTextPassword' => $plainTextPassword ? 'provided' : 'not provided',
        ]);

        // Deactivate all emails
        self::query()->update(['is_active' => false]);

        // Activate the selected email
        $email = self::findOrFail($id);
        $email->update(['is_active' => true]);

        // Clear config cache before updating .env
        \Artisan::call('config:clear');
        \Log::info('Config cache cleared');

        // Update .env file
        try {
            DotenvEditor::setKeys([
                'MAIL_FROM_ADDRESS' => $email->email,
                'MAIL_USERNAME' => $email->email,
                'MAIL_PASSWORD' => $plainTextPassword ?? $email->password,
            ])->save();
            \Log::info('.env file updated successfully', [
                'email' => $email->email,
                'password_set' => $plainTextPassword ? 'provided' : 'from database',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update .env file: ' . $e->getMessage());
            throw $e;
        }

        // Re-cache config
        \Artisan::call('config:cache');
        \Log::info('Config cache re-generated');
    }
}