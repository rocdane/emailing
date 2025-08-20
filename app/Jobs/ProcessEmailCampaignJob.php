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
        // Marquer la campagne comme en cours
        $this->campaign->update(['status' => 'processing']);
        
        // Déclencher l'event de début de campagne
        EmailCampaignStarted::dispatch($this->campaign);

        // Récupérer tous les emails de la campagne
        $emails = $this->campaign->emails()->pending()->get();

        // Dispatcher un job pour chaque email
        foreach ($emails as $email) {
            SendSingleEmailJob::dispatch($email);
        }
    }
}
