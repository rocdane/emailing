<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Suscriber;

interface SuscriberRepositoryInterface
{
    public function findOrCreateByEmail(string $email, array $additionalData = []): Suscriber;

    public function bulkCreateFromEmails(array $emails): Collection;
    
    public function getActivesuscribers(): Collection;
}
