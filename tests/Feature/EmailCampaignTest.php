<?php

namespace Tests\Feature;

use App\Jobs\ProcessEmailCampaignJob;
use App\Models\EmailCampaign;
use App\Models\Suscriber;
use App\Services\EmailCampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EmailCampaignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('local');
        Queue::fake();
    }

    public function test_can_create_email_campaign_with_csv_file(): void
    {
        // Créer un fichier CSV de test directement
        $csvContent = "test1@example.com\ntest2@example.com\ntest3@example.com";
        $file = UploadedFile::fake()->createWithContent('emails.csv', $csvContent);

        $service = app(EmailCampaignService::class);

        $campaign = $service->createCampaign(
            $file,
            'Test Subject',
            'Test Content',
            'Test Campaign'
        );

        $this->assertInstanceOf(EmailCampaign::class, $campaign);
        $this->assertEquals('Test Campaign', $campaign->name);
        $this->assertEquals('Test Subject', $campaign->subject);
        $this->assertEquals('Test Content', $campaign->content);
        $this->assertEquals(3, $campaign->total_emails);

        // Vérifier que les subscribers ont été créés
        $this->assertEquals(3, Subscriber::count());
        
        // Vérifier que les emails ont été créés
        $this->assertEquals(3, $campaign->emails()->count());

        // Vérifier que le job a été dispatché
        Queue::assertPushed(ProcessEmailCampaignJob::class);
    }

    public function test_duplicate_emails_are_handled_correctly(): void
    {
        // Créer un suscriber existant
        Suscriber::create(['email' => 'test1@example.com']);

        $csvContent = "test1@example.com\ntest2@example.com\ntest1@example.com";
        $file = UploadedFile::fake()->createWithContent('emails.csv', $csvContent);

        $service = app(EmailCampaignService::class);
        $campaign = $service->createCampaign($file, 'Subject', 'Content');

        // Seuls 2 subscribers uniques doivent exister
        $this->assertEquals(2, Subscriber::count());
        $this->assertEquals(3, $campaign->total_emails); // Mais 3 emails créés
    }

    public function test_invalid_file_throws_exception(): void
    {
        $this->expectException(ValidationException::class);

        // Créer un fichier PDF invalide
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        
        $service = app(EmailCampaignService::class);
        
        $service->createCampaign($file, 'Subject', 'Content');
    }

    public function test_empty_file_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Aucun email valide trouvé dans le fichier.');

        // Créer un fichier CSV vide
        $file = UploadedFile::fake()->createWithContent('empty.csv', '');
        
        $service = app(EmailCampaignService::class);
        
        $service->createCampaign($file, 'Subject', 'Content');
    }

    public function test_file_with_invalid_emails_only(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Aucun email valide trouvé dans le fichier.');

        // Créer un fichier avec seulement des emails invalides
        $csvContent = "not-an-email\ninvalid@\n@missing.com";
        $file = UploadedFile::fake()->createWithContent('invalid.csv', $csvContent);
        
        $service = app(EmailCampaignService::class);
        
        $service->createCampaign($file, 'Subject', 'Content');
    }

    public function test_campaign_progress_tracking(): void
    {
        $campaign = EmailCampaign::create([
            'name' => 'Test',
            'subject' => 'Test',
            'content' => 'Test',
            'total_emails' => 10,
            'sent_emails' => 3,
            'failed_emails' => 1,
        ]);

        $this->assertEquals(40.0, $campaign->progress_percentage);
        
        $campaign->incrementSent();
        $this->assertEquals(4, $campaign->sent_emails);
        
        $campaign->incrementFailed();
        $this->assertEquals(2, $campaign->failed_emails);
    }

    // Méthodes helper pour créer des fichiers de test
    protected function createTestCsvFile(array $emails): UploadedFile
    {
        $content = implode("\n", $emails);
        return UploadedFile::fake()->createWithContent('test.csv', $content);
    }

    protected function createTestTxtFile(array $emails): UploadedFile
    {
        $content = implode("\n", $emails);
        return UploadedFile::fake()->createWithContent('test.txt', $content);
    }
}