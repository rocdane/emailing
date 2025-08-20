<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\Models\EmailCampaign;

interface IEmailCampaignService
{
    public function createCampaign(
        UploadedFile $file,
        string $subject,
        string $content,
        string $campaignName = null
    ): EmailCampaign;

    public function getCampaignStats(EmailCampaign $campaign): array;
}
