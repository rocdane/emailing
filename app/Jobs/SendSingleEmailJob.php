<?php

namespace App\Jobs;

use App\Events\EmailSent;
use App\Events\EmailFailed;
use App\Mail\CampaignEmail;
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
            // Envoyer l'email
            Mail::to($this->email->subscriber->email)
                ->send(new CampaignEmail($this->email));

            // Marquer comme envoyé
            $this->email->markAsSent();
            
            // Tracker la livraison
            EmailMeta::trackDelivered($this->email, [
                'sent_at' => now()->toISOString(),
            ]);

            // Incrémenter le compteur de la campagne
            $this->email->campaign->incrementSent();

            // Déclencher l'event
            EmailSent::dispatch($this->email);

        } catch (Exception $e) {
            // Marquer comme échoué
            $this->email->markAsFailed();
            
            // Incrémenter le compteur d'échecs
            $this->email->campaign->incrementFailed();

            // Déclencher l'event d'échec
            EmailFailed::dispatch($this->email, $e->getMessage());

            throw $e; // Relancer pour les retry automatiques
        }
    }
}
