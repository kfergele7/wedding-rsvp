<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class MarketingController extends Controller
{
    public function home(): View
    {
        return view('marketing.home', [
            'plan' => [
                'name' => 'Wedding RSVP Pro',
                'price' => '$19',
                'interval' => '/month',
            ],
        ]);
    }

    public function pricing(): View
    {
        return view('marketing.pricing', [
            'plan' => [
                'name' => 'Wedding RSVP Pro',
                'price' => '$19',
                'interval' => '/month',
                'includes' => [
                    'Custom wedding page on your own public slug',
                    'Household guest list management',
                    'RSVP tracking and manual RSVP entry',
                    'CSV import/export and dietary notes',
                    'Customer admin + Stripe billing portal',
                ],
            ],
        ]);
    }

    public function features(): View
    {
        return view('marketing.features');
    }
}
