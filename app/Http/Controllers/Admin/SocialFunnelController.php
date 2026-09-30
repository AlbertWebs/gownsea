<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignLink;
use App\Models\Product;
use App\Models\SiteVisit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Rule;
use Illuminate\View\View;

class SocialFunnelController extends Controller
{
    public function index(Request $request): View
    {
        $links = CampaignLink::query()->latest()->paginate(20);
        $visitorCounts = SiteVisit::query()
            ->whereNotNull('utm_campaign')
            ->selectRaw('utm_source, utm_medium, utm_campaign, utm_content, utm_term, count(distinct visitor_hash) as visitors')
            ->groupBy('utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term')
            ->get()
            ->keyBy(fn (SiteVisit $visit) => $this->attributionKey($visit->utm_source, $visit->utm_medium, $visit->utm_campaign, $visit->utm_content, $visit->utm_term));

        $links->getCollection()->transform(function (CampaignLink $link) use ($visitorCounts) {
            $link->setAttribute('tracked_visitors', (int) ($visitorCounts->get($this->attributionKey(
                $link->utm_source,
                $link->utm_medium,
                $link->utm_campaign,
                $link->utm_content,
                $link->utm_term,
            ))->visitors ?? 0));

            return $link;
        });

        $createdId = filter_var($request->query('created'), FILTER_VALIDATE_INT);

        return view('admin.marketing.campaign-links.index', [
            'links' => $links,
            'createdLink' => $createdId ? CampaignLink::query()->find($createdId) : null,
            'destinations' => $this->destinations(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $destinations = $this->destinations();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'destination' => ['required', 'string', Rule::in(array_keys($destinations))],
            'utm_source' => ['required', Rule::in(['instagram', 'facebook', 'tiktok', 'youtube', 'whatsapp', 'linkedin', 'x', 'other'])],
            'utm_medium' => ['required', Rule::in(['paid_social', 'organic_social', 'influencer'])],
            'utm_campaign' => ['required', 'string', 'max:190'],
            'utm_content' => ['nullable', 'string', 'max:190'],
            'utm_term' => ['nullable', 'string', 'max:190'],
        ]);

        $destination = $destinations[$data['destination']];
        $link = CampaignLink::query()->create([
            'name' => trim($data['name']),
            'destination_path' => $destination['path'],
            'destination_label' => $destination['label'],
            'utm_source' => $data['utm_source'],
            'utm_medium' => $data['utm_medium'],
            'utm_campaign' => trim($data['utm_campaign']),
            'utm_content' => filled($data['utm_content'] ?? null) ? trim($data['utm_content']) : null,
            'utm_term' => filled($data['utm_term'] ?? null) ? trim($data['utm_term']) : null,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.marketing.index', ['created' => $link->id])->with('status', 'Your tracked campaign link is ready to use.');
    }

    public function destroy(CampaignLink $campaignLink): RedirectResponse
    {
        $campaignLink->delete();

        return redirect()->route('admin.marketing.index')->with('status', 'Campaign link removed.');
    }

    /** @return array<string, array{label: string, path: string}> */
    private function destinations(): array
    {
        $destinations = [
            'home' => ['label' => 'Homepage', 'path' => route('home', [], false)],
            'graduation' => ['label' => 'Graduation attire', 'path' => route('graduation-attire', [], false)],
            'legal' => ['label' => 'Legal attire', 'path' => route('legal-attire', [], false)],
            'church' => ['label' => 'Church wear', 'path' => route('church-wear', [], false)],
            'hire' => ['label' => 'Gown hire', 'path' => route('gown-for-hire', [], false)],
            'bulk' => ['label' => 'Bulk enquiry', 'path' => route('bulk-inquiry', [], false)],
            'contact' => ['label' => 'Contact Gownsea', 'path' => route('contact-us', [], false)],
        ];

        foreach (Product::query()->published()->orderBy('name')->get(['id', 'name', 'slug']) as $product) {
            $destinations['product:'.$product->id] = [
                'label' => $product->name,
                'path' => route('products.show', $product->slug, false),
            ];
        }

        return $destinations;
    }

    private function attributionKey(?string $source, ?string $medium, ?string $campaign, ?string $content, ?string $term): string
    {
        return implode('|', array_map(fn ($value) => (string) $value, [$source, $medium, $campaign, $content, $term]));
    }
}
