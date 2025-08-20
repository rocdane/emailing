<?php
namespace App\Mail;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private Email $email
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->email->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.campaign',
            with: [
                'email' => $this->email,
                'subscriber' => $this->email->subscriber,
                'content' => $this->processContent($this->email->content),
                'trackingPixelUrl' => $this->email->getTrackingPixelUrl(),
            ],
        );
    }

    /**
     * Traite le contenu pour ajouter le tracking des liens
     */
    private function processContent(string $content): string
    {
        // Remplacer tous les liens par des liens de tracking
        $pattern = '/<a\s+(?:[^>]*?\s+)?href=(["\']*)(.*?)\1/i';
        
        return preg_replace_callback($pattern, function ($matches) {
            $originalUrl = $matches[2];
            $trackingUrl = $this->email->getTrackingClickUrl($originalUrl);
            
            return str_replace($matches[2], $trackingUrl, $matches[0]);
        }, $content);
    }
}
