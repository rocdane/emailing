<?php

namespace App\Services;

use App\Jobs\ProcessEmailCampaignJob;
use App\Models\EmailCampaign;
use App\Respositories\EmailRepository;
use App\Respositories\SuscriberRespository;
use Illuminate\Http\UploadedFile;
use App\Services\EmailParsingService;

class EmailCampaignService
{
    public function __construct(
        private EmailParsingService $emailParsingService,
        private SuscriberRepository $suscriberRepository,
        private EmailRepositorty $emailRepository
    ) {}

    /**
     * Créer une nouvelle campagne d'email
     */
    public function createCampaign(
        UploadedFile $file,
        string $subject,
        string $content,
        string $campaignName = null
    ): EmailCampaign {
        // Parser les emails du fichier
        $emails = $this->emailParsingService->parseEmailFile($file);
        
        if (empty($emails)) {
            throw new \InvalidArgumentException('Aucun email valide trouvé dans le fichier.');
        }

        // Créer la campagne
        $campaign = EmailCampaign::create([
            'name' => $campaignName ?: 'Campagne du ' . now()->format('d/m/Y H:i'),
            'subject' => $subject,
            'content' => $content,
            'total_emails' => count($emails),
            'status' => 'pending',
        ]);

        // Créer ou récupérer les suscribers
        $suscribers = $this->suscriberRepository->bulkCreateFromEmails($emails);

        // Créer les emails
        $this->emailRepository->createBulkEmails($suscribers, $subject, $content, $campaign);

        // Dispatcher le job pour l'envoi asynchrone
        ProcessEmailCampaignJob::dispatch($campaign);

        return $campaign;
    }

    /**
     * Obtenir les stats d'une campagne
     */
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
}
