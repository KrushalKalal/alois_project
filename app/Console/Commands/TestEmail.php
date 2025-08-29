<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class TestEmail extends Command
{
    protected $signature = 'email:test';
    protected $description = 'Test email sending configuration';

    public function handle()
    {
        Log::info('Testing SMTP configuration');

        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function ($str, $level) {
                Log::debug("PHPMailer: $str");
            };

            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION');
            $mail->Port = env('MAIL_PORT');

            Log::info('SMTP settings for test', [
                'host' => $mail->Host,
                'username' => $mail->Username,
                'port' => $mail->Port,
                'encryption' => $mail->SMTPSecure,
            ]);

            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'Qubeta Technolab'));
            $mail->addAddress('karankalal20@gmail.com');
            $mail->isHTML(true);
            $mail->Subject = 'Test Email';
            $mail->Body = 'This is a test email from Qubeta Technolab.';
            $mail->AltBody = 'This is a test email.';

            $mail->send();
            Log::info('Test email sent successfully');
            $this->info('Test email sent successfully.');
        } catch (Exception $e) {
            Log::error('Failed to send test email', [
                'error' => $e->getMessage(),
                'phpmailer_error' => $mail->ErrorInfo,
            ]);
            $this->error('Failed to send test email: ' . $mail->ErrorInfo);
        }
    }
}