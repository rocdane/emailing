<?php

namespace App\Jobs;

use App\Events\EmailCampaignStarted;
use App\Models\EmailCampaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessEmailCampaignJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private EmailCampaign $campaign
    ) {}

    public function handle(): void
    {
        $this->campaign->update(['status' => 'processing']);
        
        EmailCampaignStarted::dispatch($this->campaign);

        $emails = $this->campaign->emails()->pending()->get();

        foreach ($emails as $email) {
            SendSingleEmailJob::dispatch($email);
        }
    }
}
