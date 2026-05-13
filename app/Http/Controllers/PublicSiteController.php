<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Support\WeddingPalettes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PublicSiteController extends Controller
{
    public function home(Request $request)
    {
        if (! $this->currentSite()->is_published) {
            return response()->view('site-coming-soon', ['site' => $this->currentSite()]);
        }

        if (! $this->currentSite()->account->hasActivePaidAccess()) {
            return response()->view('site-unavailable', ['site' => $this->currentSite()]);
        }

        $lookupCode = $request->query('code');
        $rsvpSettings = $this->rsvpSettings();

        return response()
            ->view('app', [
                'page' => 'home',
                'payload' => [
                    'siteTitle' => $this->currentSite()->title,
                    'content' => $this->homepageContent(),
                    'rsvpSettings' => $rsvpSettings,
                    'rsvpCode' => $lookupCode,
                    'openRsvpModal' => (bool) $lookupCode,
                    'publicSlug' => $this->currentSite()->public_slug,
                ],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function rsvp(Request $request, ?string $code = null)
    {
        if (! $this->currentSite()->is_published) {
            return response()->view('site-coming-soon', ['site' => $this->currentSite()]);
        }

        if (! $this->currentSite()->account->hasActivePaidAccess()) {
            return response()->view('site-unavailable', ['site' => $this->currentSite()]);
        }

        $lookupCode = $code ?: $request->query('code');
        $rsvpSettings = $this->rsvpSettings();

        return response()
            ->view('app', [
                'page' => 'home',
                'payload' => [
                    'siteTitle' => $this->currentSite()->title,
                    'content' => $this->homepageContent(),
                    'rsvpSettings' => $rsvpSettings,
                    'rsvpCode' => $lookupCode,
                    'openRsvpModal' => true,
                    'publicSlug' => $this->currentSite()->public_slug,
                ],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function showBySlug(Request $request, string $public_slug)
    {
        if ($this->currentSite()->public_slug !== $public_slug) {
            abort(404);
        }

        if (! $this->currentSite()->is_published) {
            if (! $this->canPreviewUnpublishedSite($request)) {
                return view('site-coming-soon', ['site' => $this->currentSite()]);
            }

            return $this->renderPublicHome($request->query('code'));
        }

        if (! $this->currentSite()->account->hasActivePaidAccess()) {
            return view('site-unavailable', ['site' => $this->currentSite()]);
        }

        return $this->renderPublicHome($request->query('code'));
    }

    public function demo(Request $request)
    {
        [$content, $rsvpSettings, $publicSlug, $siteTitle] = $this->demoTemplatePayload();

        return response()
            ->view('app', [
                'page' => 'home',
                'payload' => [
                    'siteTitle' => $siteTitle,
                    'content' => $content,
                    'rsvpSettings' => $rsvpSettings,
                    'rsvpCode' => null,
                    'openRsvpModal' => false,
                    'publicSlug' => $publicSlug,
                ],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function renderPublicHome(?string $lookupCode)
    {
        $rsvpSettings = $this->rsvpSettings();
        $previewBanner = $this->previewBannerPayload();

        return response()
            ->view('app', [
                'page' => 'home',
                'payload' => [
                    'siteTitle' => $this->currentSite()->title,
                    'content' => $this->homepageContent(),
                    'rsvpSettings' => $rsvpSettings,
                    'rsvpCode' => $lookupCode,
                    'openRsvpModal' => (bool) $lookupCode,
                    'publicSlug' => $this->currentSite()->public_slug,
                    'previewBanner' => $previewBanner,
                ],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function canPreviewUnpublishedSite(Request $request): bool
    {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        if ((bool) $user->is_staff) {
            return true;
        }

        return (int) $user->account_id === (int) $this->currentSite()->account_id;
    }

    private function previewBannerPayload(): ?array
    {
        if ($this->currentSite()->is_published) {
            return null;
        }

        $user = request()->user();

        if (! $user) {
            return null;
        }

        if ((bool) $user->is_staff) {
            return [
                'mode' => 'staff',
                'title' => 'Staff preview mode',
                'message' => 'You are viewing this unpublished site as staff.',
                'accountUrl' => route('staff.dashboard'),
                'accountLabel' => 'Staff Dashboard',
            ];
        }

        if ((int) $user->account_id !== (int) $this->currentSite()->account_id) {
            return null;
        }

        if (! $this->currentSite()->account->hasActivePaidAccess()) {
            return [
                'mode' => 'customer',
                'title' => 'Preview mode',
                'message' => 'Only you can view this draft right now. Subscribe now to make your site live and shareable.',
                'accountUrl' => route('customer.dashboard', ['tab' => 'account']),
                'subscribeUrl' => route('customer.dashboard'),
                'accountLabel' => 'Account',
                'subscribeLabel' => 'Subscribe Now',
            ];
        }

        return [
            'mode' => 'customer',
            'title' => 'Preview mode',
            'message' => 'Only you can view this draft right now. Publish your website when ready to share with your guests.',
            'accountUrl' => route('customer.admin.content.page'),
            'accountLabel' => 'Update Website',
            'subscribeUrl' => route('customer.admin.dashboard'),
            'subscribeLabel' => 'Publish Now',
        ];
    }

    private function homepageContent(): array
    {
        $fallback = config('wedding.homepage_content', []);
        $saved = SiteSetting::query()
            ->forSite($this->currentSiteId())
            ->where('key', 'homepage_content')
            ->first();

        if (! $saved || ! is_array($saved->value)) {
            return WeddingPalettes::applyToContent($fallback);
        }

        return WeddingPalettes::applyToContent($this->mergeReplacingLists($fallback, $saved->value));
    }

    private function rsvpSettings(): array
    {
        $fallback = config('wedding.rsvp_settings', []);
        $saved = SiteSetting::query()
            ->forSite($this->currentSiteId())
            ->where('key', 'rsvp_settings')
            ->first();

        if (! $saved || ! is_array($saved->value)) {
            return $fallback;
        }

        return $this->mergeReplacingLists($fallback, $saved->value);
    }

    private function demoTemplatePayload(): array
    {
        $fallbackContent = config('wedding.homepage_content', []);
        $fallbackRsvp = config('wedding.rsvp_settings', []);

        $demoSourceSetting = null;
        if (Schema::hasTable('platform_settings')) {
            $demoSourceSetting = PlatformSetting::query()
                ->where('key', 'demo_template_source')
                ->first();
        }

        $demoSource = is_array($demoSourceSetting?->value) ? $demoSourceSetting->value : [];
        $demoSiteId = ($demoSource['source'] ?? null) === 'default'
            ? null
            : ($demoSource['site_id'] ?? null);

        $demoSite = null;
        if (is_numeric($demoSiteId)) {
            $demoSite = Site::query()->where('id', (int) $demoSiteId)->first();
        }

        if (! $demoSite) {
            return [
                WeddingPalettes::applyToContent($this->ensureDemoContent($fallbackContent)),
                $this->ensureDemoRsvpSettings($fallbackRsvp),
                null,
                'Magic Invitation Demo',
            ];
        }

        $savedContent = SiteSetting::query()
            ->where('site_id', $demoSite->id)
            ->where('key', 'homepage_content')
            ->first();
        $savedRsvp = SiteSetting::query()
            ->where('site_id', $demoSite->id)
            ->where('key', 'rsvp_settings')
            ->first();

        $content = (is_array($savedContent?->value))
            ? $this->mergeReplacingLists($fallbackContent, $savedContent->value)
            : $fallbackContent;
        $content = $this->ensureDemoContent($content);
        $content = WeddingPalettes::applyToContent($content);
        $rsvpSettings = (is_array($savedRsvp?->value))
            ? $this->mergeReplacingLists($fallbackRsvp, $savedRsvp->value)
            : $fallbackRsvp;
        $rsvpSettings = $this->ensureDemoRsvpSettings($rsvpSettings);

        return [$content, $rsvpSettings, $demoSite->public_slug, $demoSite->title];
    }

    private function ensureDemoContent(array $content): array
    {
        $demoGalleryItems = [
            ['image' => '/images/wedding/demo-gallery-1.svg', 'imageFocusX' => 50, 'imageFocusY' => 50],
            ['image' => '/images/wedding/demo-gallery-2.svg', 'imageFocusX' => 50, 'imageFocusY' => 50],
            ['image' => '/images/wedding/demo-gallery-3.svg', 'imageFocusX' => 50, 'imageFocusY' => 50],
            ['image' => '/images/wedding/demo-gallery-4.svg', 'imageFocusX' => 50, 'imageFocusY' => 50],
            ['image' => '/images/wedding/demo-gallery-5.svg', 'imageFocusX' => 50, 'imageFocusY' => 50],
            ['image' => '/images/wedding/demo-gallery-6.svg', 'imageFocusX' => 50, 'imageFocusY' => 50],
            ['image' => '/images/wedding/demo-gallery-7.svg', 'imageFocusX' => 50, 'imageFocusY' => 50],
            ['image' => '/images/wedding/demo-gallery-8.svg', 'imageFocusX' => 50, 'imageFocusY' => 50],
        ];

        $content['gallery']['heading'] = 'Us across the years';
        $content['gallery']['items'] = $demoGalleryItems;
        $content['details']['faqs'] = $this->ensureDemoFaqs($content['details']['faqs'] ?? []);
        $content['section_visibility']['gallery'] = true;
        $content['section_visibility']['faqs'] = true;

        return $content;
    }

    private function ensureDemoFaqs(mixed $faqs): array
    {
        $normalisedFaqs = collect(is_array($faqs) ? $faqs : [])
            ->filter(fn (mixed $faq) => is_array($faq) && filled($faq['question'] ?? null))
            ->values()
            ->all();

        $additionalFaq = [
            'question' => 'What time should guests arrive?',
            'answer' => 'Please arrive around 20 minutes before the ceremony so everyone can be seated comfortably.',
        ];

        $alreadyExists = collect($normalisedFaqs)
            ->contains(fn (array $faq) => ($faq['question'] ?? '') === $additionalFaq['question']);

        if (! $alreadyExists) {
            $normalisedFaqs[] = $additionalFaq;
        }

        return $normalisedFaqs;
    }

    private function ensureDemoRsvpSettings(array $rsvpSettings): array
    {
        $courseOptions = [
            'starter' => [
                ['title' => 'Whipped Goats Cheese', 'description' => 'Honeyed goats cheese with pickled pear, toasted walnuts, and garden leaves.'],
                ['title' => 'Smoked Salmon Roulade', 'description' => 'Smoked salmon with lemon creme fraiche, cucumber ribbons, and dill.'],
            ],
            'dessert' => [
                ['title' => 'Dark Chocolate Delice', 'description' => 'Rich chocolate delice with salted caramel and vanilla cream.'],
                ['title' => 'Summer Berry Pavlova', 'description' => 'Crisp meringue, softly whipped cream, and seasonal berries.'],
            ],
        ];

        $courses = is_array($rsvpSettings['menu_courses'] ?? null)
            ? array_values($rsvpSettings['menu_courses'])
            : [];

        foreach ($courseOptions as $courseKey => $options) {
            $courseIndex = $this->findDemoMenuCourseIndex($courses, $courseKey);

            if ($courseIndex === null) {
                $courses[] = [
                    'id' => $courseKey,
                    'name' => ucfirst($courseKey),
                    'items' => $options,
                ];
                continue;
            }

            $items = is_array($courses[$courseIndex]['items'] ?? null)
                ? array_values($courses[$courseIndex]['items'])
                : [];

            foreach ($options as $option) {
                $exists = collect($items)->contains(fn (mixed $item) => is_array($item) && ($item['title'] ?? '') === $option['title']);

                if (! $exists) {
                    $items[] = $option;
                }
            }

            $courses[$courseIndex]['items'] = $items;
        }

        $rsvpSettings['menu_courses'] = $courses;

        return $rsvpSettings;
    }

    private function findDemoMenuCourseIndex(array $courses, string $courseKey): ?int
    {
        foreach ($courses as $index => $course) {
            if (! is_array($course)) {
                continue;
            }

            $label = strtolower(trim(($course['id'] ?? '').' '.($course['name'] ?? '')));

            if (str_contains($label, $courseKey)) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Merge nested associative arrays while replacing list arrays outright.
     */
    private function mergeReplacingLists(array $base, array $incoming): array
    {
        if (array_is_list($incoming)) {
            return $incoming;
        }

        $merged = $base;

        foreach ($incoming as $key => $value) {
            if (
                array_key_exists($key, $base)
                && is_array($base[$key])
                && is_array($value)
                && ! array_is_list($value)
            ) {
                $merged[$key] = $this->mergeReplacingLists($base[$key], $value);
                continue;
            }

            $merged[$key] = $value;
        }

        return $merged;
    }
}
