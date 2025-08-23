<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\EmailCampaignService;

class Dashboard extends Component
{
    public $total_campaigns;
    public $total_sent;
    public $active_suscribers;

    public function mount()
    {
        $this->updateStats();
    }

    public function updateStats(EmailCampaignService $emailCampaignService = null)
    {
        if (!$emailCampaignService) {
            $emailCampaignService = app(EmailCampaignService::class);
        }
        
        $stats = $emailCampaignService->getDashboardStats();

        $this->total_campaigns = $stats['total_campaigns'];
        $this->total_sent = $stats['total_sent'];
        $this->active_suscribers = $stats['active_suscribers'];
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
