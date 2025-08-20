<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Email;
use App\Models\EmailCampaign;

interface EmailRepositoryInterface
{
    public function createBulkEmails(
        Collection $suscribers,
        string $subject,
        string $content,
        EmailCampaign $campaign
    ): Collection ;
    
    public function getPendingEmails(): Collection;
    public function findByTrackingToken(string $token): ?Email;
    public function updateEmailStatus(Email $email, string $status): Email;
    public function getEmailsByCampaign(EmailCampaign $campaign): Collection;
}
