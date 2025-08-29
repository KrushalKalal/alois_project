<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Jackiedo\DotenvEditor\DotenvEditor;
use Illuminate\Support\Facades\Log;

class EmailService
{
    protected $dotenvEditor;

    public function __construct()
    {
        $this->dotenvEditor = app('dotenv-editor');
    }

    public function sendHelloEmail(array $toEmails, array $ccEmails = [], $attachmentPath = null, $attachmentName = null)
    {
        Log::info('sendHelloEmail initiated', [
            'to_emails' => $toEmails,
            'cc_emails' => $ccEmails,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $mainEmail = \App\Models\MainEmail::where('is_active', true)->first();

        if (!$mainEmail) {
            Log::error('No active main email found in main_emails table.');
            return false;
        }

        Log::info('Active main email found', ['email' => $mainEmail->email]);

        $mail = new PHPMailer(true);

        try {
            // Enable detailed SMTP debugging
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function ($str, $level) {
                Log::debug("PHPMailer: $str");
            };

            // Server settings
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST'); // Remove fallback
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME', $mainEmail->email); // Keep mainEmail fallback
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION');
            $mail->Port = env('MAIL_PORT');

            if (!$mail->Host || !$mail->Username || !$mail->Password || !$mail->SMTPSecure || !$mail->Port) {
                Log::error('Missing SMTP configuration', [
                    'host' => $mail->Host,
                    'username' => $mail->Username,
                    'password' => $mail->Password ? 'set' : 'not set',
                    'encryption' => $mail->SMTPSecure,
                    'port' => $mail->Port,
                ]);
                return false;
            }

            Log::info('SMTP settings configured', [
                'host' => $mail->Host,
                'username' => $mail->Username,
                'port' => $mail->Port,
                'encryption' => $mail->SMTPSecure,
            ]);

            // Sender
            $mail->setFrom($mainEmail->email, $mainEmail->name ?? env('MAIL_FROM_NAME', 'Qubeta Technolab'));

            // Recipients
            $validToEmails = [];
            foreach ($toEmails as $email) {
                if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress(trim($email));
                    $validToEmails[] = trim($email);
                } else {
                    Log::warning('Invalid To email address skipped', ['email' => $email]);
                }
            }

            $validCcEmails = [];
            foreach ($ccEmails as $email) {
                if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                    $mail->addCC(trim($email));
                    $validCcEmails[] = trim($email);
                } else {
                    Log::warning('Invalid CC email address skipped', ['email' => $email]);
                }
            }

            if (empty($validToEmails)) {
                Log::error('No valid To email addresses provided after validation.');
                return false;
            }

            Log::info('Recipients set', [
                'to' => $validToEmails,
                'cc' => $validCcEmails,
            ]);

            // Attachment
            if ($attachmentPath && file_exists($attachmentPath)) {
                $mail->addAttachment($attachmentPath, $attachmentName);
                Log::info('Attachment added', ['path' => $attachmentPath, 'name' => $attachmentName]);
            } else {
                Log::warning('Attachment file not found or not provided', ['path' => $attachmentPath]);
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Qubeta Technolab Test Email';
            $mail->Body = 'Dear Recipient,<br><br>This is a test email from Qubeta Technolab.<br><br>Best regards,<br>Qubeta Technolab';
            $mail->AltBody = 'Dear Recipient, This is a test email from Qubeta Technolab. Best regards, Qubeta Technolab';

            Log::info('Email content prepared', ['subject' => $mail->Subject]);

            $mail->send();
            Log::info('Email sent successfully', [
                'to' => $validToEmails,
                'cc' => $validCcEmails,
                'attachment' => $attachmentName,
            ]);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send email', [
                'error' => $e->getMessage(),
                'phpmailer_error' => $mail->ErrorInfo,
                'to' => $toEmails,
                'cc' => $ccEmails,
                'attachment' => $attachmentPath,
            ]);
            return false;
        }
    }

    public function updateEnvEmail($email)
    {
        try {
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->dotenvEditor->setKey('MAIL_USERNAME', $email);
                $this->dotenvEditor->setKey('MAIL_FROM_ADDRESS', $email);
                $this->dotenvEditor->save();
                \Artisan::call('config:clear');
                Log::info('Environment file updated with email: ' . $email);
            } else {
                Log::warning('Invalid or empty email provided for .env update: ' . ($email ?? 'null'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to update .env file: ' . $e->getMessage());
            throw $e;
        }
    }
}