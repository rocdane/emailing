<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\EmailCampaignService;
use App\Events\EmailCampaignStarted;

class MailingForm extends Component
{
    use WithFileUploads;

    public $file;
    public $subject;
    public $content;
    public $campaignName;
    public $isSubmitting = false;

    protected function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt|max:2048',
            'subject' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:10',
            'campaignName' => 'nullable|string|max:255',
        ];
    }

    protected function messages(): array
    {
        return [
            'file.required' => 'Veuillez sélectionner un fichier.',
            'file.mimes' => 'Le fichier doit être au format CSV ou TXT.',
            'file.max' => 'Le fichier ne peut pas dépasser 2MB.',
            'subject.required' => 'Le sujet est obligatoire.',
            'subject.min' => 'Le sujet doit contenir au moins 3 caractères.',
            'content.required' => 'Le contenu est obligatoire.',
            'content.min' => 'Le contenu doit contenir au moins 10 caractères.',
        ];
    }

    public function submit(EmailCampaignService $emailCampaignService)
    {
        $this->isSubmitting = true;
        
        try {
            $this->validate();

            $campaign = $emailCampaignService->createCampaign(
                $this->file,
                $this->subject,
                $this->content,
                $this->campaignName
            );

            event(new EmailCampaignStarted($campaign));

            session()->flash('success', "Campagne créée avec succès ! L'envoi des emails a commencé.");
            
            return $this->redirect(route('email.campaign.progress', ['campaign' => $campaign->id]));

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur : ' . $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.mailing-form');
    }
}
