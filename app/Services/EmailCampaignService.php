<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

use App\Jobs\ProcessEmailCampaignJob;
use App\Models\EmailCampaign;
use App\Repositories\EmailRepository;
use App\Repositories\SuscriberRepository;

class EmailCampaignService
{
    public function __construct(
        private EmailParsingService $emailParsingService,
        private SuscriberRepository $suscriberRepository,
        private EmailRepository $emailRepository
    ) {}

    public function createCampaign(
        UploadedFile $file,
        string $subject,
        string $content,
        string $campaignName = null
    ): EmailCampaign {
        
        //todo: validate file type and size before parsing
        $emails = $this->emailParsingService->parseEmailFile($file);
        
        if (empty($emails)) {
            throw new \InvalidArgumentException('Aucun email valide trouvé dans le fichier.');
        }

        $suscribers = $this->suscriberRepository->bulkCreateFromEmails($emails);

        // create the campaign
        $campaign = EmailCampaign::create([
            'name' => $campaignName ?: 'Campagne du ' . now()->format('d/m/Y H:i'),
            'subject' => $subject,
            'content' => $content,
            'total_emails' => count($emails),
            'status' => 'pending',
        ]);

        $this->emailRepository->createBulkEmails($suscribers, $subject, $content, $campaign);

        //todo: dispatch job to process campaign
        ProcessEmailCampaignJob::dispatch($campaign);

        return $campaign;
    }

    public function getCampaignStats(EmailCampaign $campaign): array
    {
        $emails = $campaign->emails()->get();
        
        return [
            'total' => $campaign->total_emails,
            'sent' => $campaign->sent_emails,
            'failed' => $campaign->failed_emails,
            'pending' => $campaign->total_emails - $campaign->sent_emails - $campaign->failed_emails,
            'progress_percentage' => $campaign->progress_percentage,
            'opened_count' => $emails->sum(function ($email) {
                return $email->metas()->where('type', 'opened')->count();
            }),
            'clicked_count' => $emails->sum(function ($email) {
                return $email->metas()->where('type', 'clicked')->count();
            }),
        ];
    }

    public function getDashboardStats(): array
    {
        return [
            'total_campaigns' => EmailCampaign::count(),
            'total_sent' => $this->emailRepository->getSentEmails()->count(),
            'active_suscribers' => $this->suscriberRepository->getActiveSuscribers()->count(),
        ];
    }
}
