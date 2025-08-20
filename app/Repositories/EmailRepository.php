<?php

namespace App\Repositories;

use App\Models\Email;
use App\Models\EmailCampaign;
use App\Models\Suscriber;
use Illuminate\Database\Eloquent\Collection;

class EmailRepository implements EmailRepositoryInterface
{
    public function createBulkEmails(
        Collection $suscribers,
        string $subject,
        string $content,
        EmailCampaign $campaign
    ): Collection {
        $emails = collect();

        foreach ($suscribers as $suscriber) {
            $email = Email::create([
                'suscriber_id' => $suscriber->id,
                'email_campaign_id' => $campaign->id,
                'subject' => $subject,
                'content' => $content,
                'status' => 'pending',
            ]);
            
            $emails->push($email);
        }

        return $emails;
    }

    public function getPendingEmails(): Collection
    {
        return Email::pending()->with('suscriber')->get();
    }

    public function findByTrackingToken(string $token): ?Email
    {
        return Email::where('tracking_token', $token)
            ->with('suscriber')
            ->first();
    }

    public function updateEmailStatus(Email $email, string $status): Email
    {
        $email->status = $status;
        $email->save();

        return $email;
    }

    public function getEmailsByCampaign(EmailCampaign $campaign): Collection
    {
        return Email::where('email_campaign_id', $campaign->id)
            ->with('suscriber')
            ->get();
    }
}