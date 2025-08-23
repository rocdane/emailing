<?php

namespace App\Jobs;

use App\Events\EmailSent;
use App\Events\EmailFailed;
use App\Mail\Letter;
use App\Models\Email;
use App\Models\EmailMeta;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendSingleEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60; // 1 minute entre les tentatives

    public function __construct(
        private Email $email
    ) {}

    public function handle(): void
    {
        try {
            Mail::to($this->email->subscriber->email)->send(new Letter($this->email));

            $this->email->markAsSent();
            
            EmailMeta::trackDelivered($this->email, [ 'sent_at' => now()->toISOString(),]);

            $this->email->campaign->incrementSent();

            EmailSent::dispatch($this->email, 'Email sent successfully.');

        } catch (Exception $e) {
            $this->email->markAsFailed();
        
            $this->email->campaign->incrementFailed();

            EmailFailed::dispatch($this->email, $e->getMessage());

            throw $e;
        }
    }
}
