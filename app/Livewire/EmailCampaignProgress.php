<?php

namespace App\Livewire;

use App\Models\EmailCampaign;
use App\Services\EmailCampaignService;
use Livewire\Component;

class EmailCampaignProgress extends Component
{
    public EmailCampaign $campaign;
    public array $stats = [];

    public function mount(EmailCampaign $campaign)
    {
        $this->campaign = $campaign;
        $this->updateStats();
    }

    /**
     * Écouter les events de broadcast pour mettre à jour en temps réel
     */
    protected function getListeners(): array
    {
        return [
            "echo-private:email-campaign.{$this->campaign->id},EmailSent" => 'updateStats',
            "echo-private:email-campaign.{$this->campaign->id},EmailFailed" => 'updateStats',
        ];
    }

    public function updateStats(EmailCampaignService $emailCampaignService = null)
    {
        // Recharger la campagne depuis la DB
        $this->campaign = $this->campaign->fresh();
        
        if (!$emailCampaignService) {
            $emailCampaignService = app(EmailCampaignService::class);
        }
        
        $this->stats = $emailCampaignService->getCampaignStats($this->campaign);
    }

    public function render()
    {
        return view('livewire.email-campaign-progress');
    }
}
