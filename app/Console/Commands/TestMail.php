<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature = 'mail:test';
    protected $description = 'Test email sending';

    public function handle()
    {
        $this->info('Attempting to send test email...');
        
        try {
            Mail::raw('This is a test email from Unboxed', function($message) {
                $message->to('slimceslime1@gmail.com')
                        ->subject('Test Email from Unboxed');
            });
            
            $this->info('Test email sent! Check your inbox.');
        } catch (\Exception $e) {
            $this->error('Error sending email: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}