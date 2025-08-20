<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'suscriber_id',
        'email_campaign_id',
        'subject',
        'content',
        'status',
        'tracking_token',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    protected static function booted(): void{
        static::creating(function ($email) {
            if (empty($email->tracking_token)) {
                $email->tracking_token = Str::uuid();
            }
        });
    }

    public function suscriber(): BelongsTo
    {
        return $this->belongsTo(Suscriber::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'email_campaign_id');
    }

    public function metas(): HasMany
    {
        return $this->hasMany(EmailMeta::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeOpened($query)
    {
        return $query->where('status', 'opened');
    }

    public function markasSent(): void
    {
        $this->status = 'sent';
        $this->sent_at = now();
        $this->save();
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function getTrackingPixelUrl(): string
    {
        return route('email.tracking.pixel', ['token' => $this->tracking_token]);
    }

    public function getTrackingClickUrl(string $originalUrl): string
    {
        return route('email.tracking.click', [
            'token' => $this->tracking_token,
            'url' => base64_encode($originalUrl)
        ]);
    }
}
