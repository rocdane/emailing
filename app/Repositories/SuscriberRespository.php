<?php

namespace App\Repositories;

use App\Models\Suscriber;
use Illuminate\Database\Eloquent\Collection;

class SuscriberRepository implements SuscriberRepositoryInterface
{
    public function findOrCreateByEmail(string $email, array $additionalData = []): Suscriber
    {
        return Suscriber::firstOrCreate(
            ['email' => $email],
            $additionalData
        );
    }

    public function bulkCreateFromEmails(array $emails): Collection
    {
        $suscribers = collect();
        
        foreach ($emails as $email) {
            $suscriber = $this->findOrCreateByEmail($email);
            $suscribers->push($suscriber);
        }

        return $suscribers;
    }

    public function getActivesuscribers(): Collection
    {
        return Suscriber::active()->get();
    }
}