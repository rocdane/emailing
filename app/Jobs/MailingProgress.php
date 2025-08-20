<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\NewsLetter;
use App\Models\Email;
use Mail;
use Throwable;

class MailingProgress implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected array $letter)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $suscriber = Suscriber::firstOrCreate(['address' => $this->letter['address']], $this->letter);
            
            Email::create([$this->letter, 'suscriber_id' => $Suscriber->id]);

            $email = Email::firstOrCreate(['address' => $this->email['address'],],$this->email);

            Mail::to($suscriber->address)->send(new Letter($this->letter));

            \Log::info('Mail sent successfully to ' . $email->address);
        } catch (Throwable $th) {
            \Log::error('Failed to send email : ' . $th->getMessage());
        }
    }
}
