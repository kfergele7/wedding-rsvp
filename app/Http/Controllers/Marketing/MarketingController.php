<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class MarketingController extends Controller
{
    public function home(): View
    {
        return $this->page('home', [
            'meta' => $this->meta(
                'Magic Invitation | Wedding websites and RSVPs, done beautifully',
                'A beautiful wedding website and RSVP system you set up in minutes. One elegant template, simple forms, and publish when ready.'
            ),
            'home' => [
                'faqItems' => [
                    [
                        'q' => 'Is it free to start?',
                        'a' => 'Yes. Build your website, add your guest list, and preview everything in draft mode at no cost.',
                    ],
                    [
                        'q' => 'What happens if I cancel?',
                        'a' => 'Your site remains live until the end of your current billing period, then returns to private draft mode.',
                    ],
                    [
                        'q' => 'Do guests need accounts?',
                        'a' => 'No. Guests use a household invite code and can RSVP without creating an account.',
                    ],
                    [
                        'q' => 'Can I RSVP for someone manually?',
                        'a' => 'Yes. You can submit or update RSVP records for guests who phone in.',
                    ],
                    [
                        'q' => 'Can I export my guest list?',
                        'a' => 'Yes. Export CSV files for guests, RSVP responses, meal choices, and dietary notes.',
                    ],
                    [
                        'q' => 'Can I hide the site until I am ready?',
                        'a' => 'Yes. Keep the site in draft, then publish when you are ready to share with guests.',
                    ],
                    [
                        'q' => 'Can I change my URL?',
                        'a' => 'Your URL is unique by default. Support can assist if a manual change is required.',
                    ],
                ],
            ],
        ]);
    }

    public function pricing(): View
    {
        return $this->page('pricing', [
            'meta' => $this->meta(
                'Pricing | Magic Invitation',
                'Free to build your wedding website and guest list. Subscribe when you are ready to publish and collect RSVPs.'
            ),
            'pricing' => [
                'plan' => [
                    'name' => 'Magic Invitation Pro',
                    'price' => '£19',
                    'interval' => '/month',
                    'includes' => [
                        'Publish your wedding website on a unique public URL',
                        'Invite-code RSVP flow with no guest account sign-up',
                        'Household and party management',
                        'Meal choices and dietary restrictions',
                        'Manual RSVP updates for call-ins',
                        'CSV exports for guests and responses',
                        'Email RSVP requests and account support',
                    ],
                ],
                'freeIncludes' => [
                    'Create and customise your website in draft',
                    'Build your guest list and parties',
                    'Preview your site privately',
                    'Prepare RSVP settings before launch',
                ],
                'paidIncludes' => [
                    'Publish and share your live website',
                    'Collect RSVP responses',
                    'Use QR and invite code links',
                    'Export planning-ready CSV files',
                ],
            ],
        ]);
    }

    public function features(): View
    {
        return $this->page('features', [
            'meta' => $this->meta(
                'Features | Magic Invitation',
                'Form-based editing, invite-code RSVP, household guest management, manual RSVP support, and privacy-first controls.'
            ),
        ]);
    }

    public function howItWorks(): View
    {
        return $this->page('how-it-works', [
            'meta' => $this->meta(
                'How it works | Magic Invitation',
                'Create your wedding website for free, share invite codes and QR links, then publish when ready to collect RSVPs.'
            ),
        ]);
    }

    public function faq(): View
    {
        return $this->page('faq', [
            'meta' => $this->meta(
                'FAQ | Magic Invitation',
                'Answers on pricing, publishing, privacy-first RSVP flows, guest exports, and invite-code access.'
            ),
            'faqGroups' => [
                [
                    'title' => 'Getting Started',
                    'items' => [
                        [
                            'q' => 'Is it free to start?',
                            'a' => 'Yes. Build your site and guest list in draft mode without paying first.',
                        ],
                        [
                            'q' => 'When do I subscribe?',
                            'a' => 'Only when you are ready to publish and start collecting live RSVP responses.',
                        ],
                    ],
                ],
                [
                    'title' => 'Guests and RSVPs',
                    'items' => [
                        [
                            'q' => 'Do guests need accounts?',
                            'a' => 'No. Guests RSVP using invitation codes. No guest sign-up is required.',
                        ],
                        [
                            'q' => 'Can I submit RSVPs for guests manually?',
                            'a' => 'Yes. Manual RSVP entry is built in for guests who call or need help.',
                        ],
                        [
                            'q' => 'Can I export responses?',
                            'a' => 'Yes. Export guests and RSVP details to CSV for planning and venue teams.',
                        ],
                    ],
                ],
                [
                    'title' => 'Publishing and Billing',
                    'items' => [
                        [
                            'q' => 'Can I keep my site hidden?',
                            'a' => 'Yes. Keep it private in draft mode until you are ready to publish.',
                        ],
                        [
                            'q' => 'What happens when I cancel?',
                            'a' => 'Your site stays live until the end of your billing period, then returns to draft visibility.',
                        ],
                        [
                            'q' => 'Can I change my URL later?',
                            'a' => 'A unique URL is assigned automatically. Support can help with special cases.',
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function page(string $page, array $payload = []): View
    {
        return view('marketing.app', [
            'marketingPage' => $page,
            'payload' => $payload,
            'meta' => $payload['meta'] ?? $this->meta('Magic Invitation', 'Wedding website and RSVP software.'),
        ]);
    }

    private function meta(string $title, string $description): array
    {
        return [
            'title' => $title,
            'description' => $description,
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => '/images/marketing/hero.jpg',
        ];
    }
}
