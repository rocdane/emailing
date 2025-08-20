<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Email;
use App\Services\IEmailCampaignService;

class HomeController extends Controller
{
    public function __construct(private IEmailCampaignService $emailCampaignService)
    {}

    public function dashboard()
    {
        $dashboard = $this->report();
        
        return view('page.dashboard', compact('dashboard'));
    }

    public function suscribe()
    {
        return view('page.suscribe');
    }

    public function mailing()
    {
        return view('page.mailing');
    }

    private function report()
    {
        $sent = Email::where('status', 'sent')->count();

        $opened = Email::where('status', 'opened')->count();

        $clicks = Email::where('status', 'clicked')->count();

        $unsubscribed = Email::where('status', 'unsuscribed')->count();

        $emails = Email::all()->count();
        
        return json_decode(json_encode([
            'sent'  => $sent,
            'opened' => $opened,
            'clicks' => $clicks,
            'unsubscribed' => $unsubscribed,
            'emails' => $emails
        ]));
    }
}
