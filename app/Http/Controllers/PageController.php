<?php

namespace App\Http\Controllers;

use App\Models\JournalPost;
use App\Services\CatalogueService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

class PageController extends Controller
{
    public function __construct(private CatalogueService $catalogue)
    {
    }
    public function home(): View
    {
        return view('pages.home', [
            'meta' => $this->meta(
                'Graduation Gowns for Hire & Sale in Kenya | Gownsea LTD',
                'High-quality graduation, legal, and church attire for hire and sale in Kenya.'
            ),
            'properties' => array_slice($this->catalogue->featuredItems(), 0, 4),
            'posts' => array_slice($this->journalPosts(), 0, 2),
            'heroSlides' => $this->catalogue->heroSlides(),
            'categoryImages' => [
                'graduation' => $this->catalogue->categoryImage('graduation', '/images/site/hero.webp'),
                'legal' => $this->catalogue->categoryImage('legal', '/images/site/Amazon-seller-lawyer-renaldo-matamoro-86JiKaHF4I8-unsplash-min.jpg'),
                'church' => $this->catalogue->categoryImage('church', '/images/site/clergy-wear.webp'),
            ],
        ]);
    }

    public function about(): View
    {
        $title = 'About Gownsea LTD | Graduation, Legal & Church Wear in Nairobi, Kenya';
        $description = 'Gownsea LTD supplies premium graduation gowns, legal attire, and church wear for hire and sale in Nairobi, Kenya. Visit Valji Building, Moktar Daddah Street for custom stitching, bulk hire, and ceremony support.';

        return view('pages.about', [
            'meta' => array_merge($this->meta($title, $description), [
                'og_image' => url('/images/site/hero.webp'),
                'twitter_image' => url('/images/site/hero.webp'),
            ]),
            'posts' => array_slice($this->journalPosts(), 0, 2),
            'faqs' => [
                'Where is Gownsea located?' => 'Our showroom is at Valji Building, Moktar Daddah Street, Nairobi. Visit Monday to Saturday, 8am–6pm, or contact us to plan a fitting or bulk collection.',
                'Do you hire and sell graduation gowns?' => 'Yes. Gownsea offers both hire and purchase for preschool through PhD sets, including gowns, caps, hoods, tassels, and stoles.',
                'Can institutions hire gowns in bulk?' => 'Yes. Universities, colleges, TVETs, and churches can request bulk hire with sizing support, delivery planning, and consistent academic colours.',
                'Do you make custom gowns?' => 'If a standard set does not match your institution, we provide custom stitching for graduation, legal, and church attire.',
                'What other ceremonial wear do you supply?' => 'Alongside graduation attire we supply courtroom-ready legal wear and church garments for clergy and choirs.',
            ],
        ]);
    }

    public function contact(): View
    {
        $title = 'Contact Gownsea LTD | Nairobi Showroom, Phone & WhatsApp';
        $description = 'Visit Gownsea at Valji Building, Moktar Daddah Street, Nairobi, or call +254 728 311537. Hire or buy graduation gowns, legal attire, and church wear. Open Monday to Saturday, 8am–6pm.';

        return view('pages.contact', [
            'meta' => array_merge($this->meta($title, $description), [
                'og_image' => url('/images/site/hero.webp'),
                'twitter_image' => url('/images/site/hero.webp'),
            ]),
            'faqs' => [
                'Where is the Gownsea showroom?' => 'We are at Valji Building, Moktar Daddah Street, Nairobi CBD. Use the map on this page or Google Maps for directions.',
                'What are your opening hours?' => 'Monday to Saturday, 8am–6pm. Call or WhatsApp if you need to plan a fitting or bulk collection outside peak hours.',
                'How do I hire or buy a gown?' => 'Send a message with the award level, quantity, and ceremony date. We will confirm hire or purchase options, sizing, and timelines.',
                'Can I get a quote for bulk hire?' => 'Yes. Use the form on this page or the bulk inquiry form with institution name, quantities, and event date.',
                'How can I reach you quickly?' => 'Call +254 728 311537, email hello@gownsea.com, or chat on WhatsApp for the fastest reply.',
            ],
        ]);
    }

    public function legalAttire(): View
    {
        return view('pages.legal-attire', [
            'meta' => $this->meta(
                'Legal Wear in Kenya | Barrister Wigs & Advocates Robes',
                'Premium legal attire for advocates, barristers, and institutions in Kenya.'
            ),
            'properties' => $this->catalogue->itemsByCategory('legal'),
            'bannerImage' => $this->catalogue->categoryImage('legal', '/images/site/Amazon-seller-lawyer-renaldo-matamoro-86JiKaHF4I8-unsplash-min.jpg'),
        ]);
    }

    public function graduationAttire(): View
    {
        return view('pages.graduation-attire', [
            'meta' => $this->meta(
                'Graduation Attire in Kenya | Gowns, Caps, Hoods & Sets',
                'University-standard graduation attire for hire and sale in Kenya.'
            ),
            'properties' => $this->catalogue->itemsByCategory('graduation'),
            'bannerImage' => $this->catalogue->categoryImage('graduation', '/images/site/graduation-attire.jpg'),
        ]);
    }

    public function churchWear(): View
    {
        return view('pages.church-wear', [
            'meta' => $this->meta(
                'Church Wear in Kenya | Clergy Robes, Cassocks & Vestments',
                'Premium church and choral wear for hire and sale in Kenya.'
            ),
            'properties' => $this->catalogue->itemsByCategory('church'),
            'faqs' => config('gownsea.hire_faqs', []),
            'bannerImage' => $this->catalogue->categoryImage('church', '/images/site/clergy-wear.webp'),
        ]);
    }

    public function gownForHire(): View
    {
        return view('pages.gown-for-hire', [
            'meta' => $this->meta(
                'Graduation Gowns for Hire in Kenya | Affordable Gown Rental',
                'Hire quality graduation gowns, caps, hoods, and accessories in Kenya.'
            ),
            'properties' => $this->catalogue->hireItems(),
            'faqs' => config('gownsea.hire_faqs', []),
        ]);
    }

    public function properties(): View
    {
        return view('pages.properties.index', [
            'meta' => $this->meta(
                'Available Collections | Graduation, Legal & Church Wear',
                'Browse Gownsea collections for graduation, legal, and church attire.'
            ),
            'properties' => $this->catalogue->featuredItems(),
        ]);
    }

    public function propertyShow(string $slug): View
    {
        return $this->productShow($slug);
    }

    public function productShow(string $slug): View
    {
        $property = $this->catalogue->enrich($this->catalogue->findBySlug($slug) ?? $this->syntheticProduct($slug));

        $related = collect($this->catalogue->itemsByCategory($property['category'] ?? 'graduation'))
            ->reject(fn (array $item) => ($item['slug'] ?? '') === $slug)
            ->take(4)
            ->values()
            ->all();

        return view('pages.properties.show', [
            'meta' => $this->meta($property['title'].' | Gownsea', $property['description']),
            'property' => $property,
            'related' => $related,
        ]);
    }

    public function journalIndex(): View
    {
        return view('pages.journal.index', [
            'meta' => $this->meta(
                'The Gown Journal | Gownsea Blog & Insights',
                'Read the latest Gownsea stories, tips, and ceremony planning insights.'
            ),
            'posts' => $this->journalPosts(),
        ]);
    }

    public function journalShow(string $slug): View
    {
        $record = JournalPost::published()->where('slug', $slug)->first();
        $post = $record?->toPublicPost();

        abort_if(! $post, 404);

        $seo = config('gownsea.journal_seo.'.$record->slug, []);
        $seoTitle = $record->seo_title ?: ($seo['title'] ?? $post['title'].' | The Gown Journal');
        $seoDescription = $record->seo_description ?: ($seo['description'] ?? $post['excerpt']);
        $post['display_title'] = $seo['heading'] ?? $post['title'];
        $meta = $this->meta($seoTitle, $seoDescription);
        $image = filled($post['image'] ?? null)
            ? (str_starts_with($post['image'], 'http') ? $post['image'] : url(ltrim($post['image'], '/')))
            : url('/images/site/hero.webp');
        $meta['og_type'] = 'article';
        $meta['og_image'] = $image;
        $meta['twitter_image'] = $image;
        $meta['canonical'] = route('journal.show', $record->slug);

        $publishedAt = $record->published_at ?? $record->created_at ?? now();
        $modifiedAt = $record->updated_at ?? $publishedAt;
        $keywords = $seo['keywords'] ?? array_filter([$post['category'], 'graduation regalia', 'graduation gowns in Kenya']);
        $author = $this->journalAuthor($post['body'] ?? '');
        $structuredData = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BlogPosting',
                    '@id' => $meta['canonical'].'#article',
                    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $meta['canonical']],
                    'headline' => $post['display_title'],
                    'description' => $seoDescription,
                    'image' => [$image],
                    'datePublished' => $publishedAt->toAtomString(),
                    'dateModified' => $modifiedAt->toAtomString(),
                    'author' => $author,
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => 'Gownsea LTD',
                        'url' => url('/'),
                        'logo' => ['@type' => 'ImageObject', 'url' => url('/favicon-rpimary.png')],
                    ],
                    'articleSection' => $post['category'],
                    'keywords' => array_values($keywords),
                    'wordCount' => str_word_count(strip_tags($post['body'] ?? '')),
                    'inLanguage' => 'en-KE',
                    'isPartOf' => ['@type' => 'Blog', 'name' => 'The Gown Journal', 'url' => route('journal.index')],
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'The Gown Journal', 'item' => route('journal.index')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $post['display_title'], 'item' => $meta['canonical']],
                    ],
                ],
            ],
        ];

        return view('pages.journal.show', [
            'meta' => $meta,
            'post' => $post,
            'publishedAt' => $publishedAt,
            'modifiedAt' => $modifiedAt,
            'articleAuthor' => $author,
            'articleKeywords' => array_values($keywords),
            'structuredData' => $structuredData,
        ]);
    }

    /** @return array<string, string> */
    private function journalAuthor(string $body): array
    {
        $text = strip_tags($body);
        if (preg_match('/\bBy\s+([\p{Lu}][\p{L}\p{M}.\x{2019}\x{2010}\x{2011}-]*(?:\s+[\p{Lu}][\p{L}\p{M}.\x{2019}\x{2010}\x{2011}-]*){1,3})/u', $text, $match) === 1) {
            return ['@type' => 'Person', 'name' => trim($match[1])];
        }

        return ['@type' => 'Organization', 'name' => 'Gownsea LTD'];
    }

    /** @return array<int, array<string, mixed>> */
    private function journalPosts(): array
    {
        return JournalPost::published()->orderByRaw('COALESCE(published_at, created_at) DESC')->orderByDesc('id')
            ->get()->map(fn (JournalPost $post) => $post->toPublicPost())->all();
    }

    public function privacyPolicy(): View
    {
        return view('pages.policies.privacy-policy', [
            'meta' => $this->meta('Privacy Policy | Gownsea LTD', 'How Gownsea handles data and privacy.'),
        ]);
    }

    public function returnPolicy(): View
    {
        return view('pages.policies.return-policy', [
            'meta' => $this->meta('Return Policy | Gownsea LTD', 'Returns and exchange guidelines for Gownsea orders.'),
        ]);
    }

    public function copyright(): View
    {
        return view('pages.policies.copyright', [
            'meta' => $this->meta('Copyright Statement | Gownsea LTD', 'Copyright policy and usage rights for Gownsea content.'),
        ]);
    }

    public function shopAttireCollection(string $slug): View
    {
        $title = $this->titleFromSlug($slug);

        $category = match ($slug) {
            'graduation-attire' => 'graduation',
            'legal-attire' => 'legal',
            'church-wear' => 'church',
            default => null,
        };

        $items = $category
            ? $this->catalogue->itemsByCategory($category)
            : [];

        return view('pages.shop.show', [
            'meta' => $this->meta($title.' | Gownsea LTD', 'Shop graduation and ceremonial attire at Gownsea.'),
            'heading' => $title,
            'subheading' => 'Explore our collection.',
            'items' => $items,
        ]);
    }

    public function shopAttireCategory(string $mainSlug, string $slug): View
    {
        $matched = $this->catalogue->findBySlug($slug);

        if ($matched) {
            return $this->productShow($slug);
        }

        $mainTitle = $this->titleFromSlug($mainSlug);
        $title = $this->titleFromSlug($slug);

        return view('pages.shop.show', [
            'meta' => $this->meta($title.' | Gownsea LTD', 'Find premium regalia for purchase and hire.'),
            'heading' => $title,
            'subheading' => $mainTitle.' collection',
            'items' => $this->catalogue->itemsByCategory($this->categoryFromSlug($mainSlug) ?? 'graduation'),
        ]);
    }

    public function ourProduct(string $slug): View
    {
        return $this->productShow($slug);
    }

    public function bulkInquiry(): View
    {
        $title = 'Bulk Gown Hire in Kenya | University & Institution Quotes | Gownsea';
        $description = 'Request a bulk graduation gown hire quote from Gownsea in Nairobi. Volume pricing, delivery planning, and custom colours for universities, colleges, TVETs, and churches.';

        return view('pages.bulk-inquiry', [
            'meta' => array_merge($this->meta($title, $description), [
                'og_image' => url('/images/site/hero.webp'),
                'twitter_image' => url('/images/site/hero.webp'),
            ]),
            'math' => \App\Support\InquiryFormGuard::mathChallenge(),
        ]);
    }

    public function termsAndConditions(): View
    {
        return view('pages.policies.terms-and-conditions', [
            'meta' => $this->meta('Terms and Conditions | Gownsea LTD', 'Terms and conditions for orders, hire, and returns.'),
        ]);
    }

    private function meta(string $title, string $description): array
    {
        return Arr::only([
            'title' => $title,
            'description' => $description,
            'og_title' => $title,
            'og_description' => $description,
            'og_type' => 'website',
            'og_image' => url('/favicon.ico'),
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $title,
            'twitter_description' => $description,
            'twitter_image' => url('/favicon.ico'),
            'canonical' => url()->current(),
        ], [
            'title',
            'description',
            'og_title',
            'og_description',
            'og_type',
            'og_image',
            'twitter_card',
            'twitter_title',
            'twitter_description',
            'twitter_image',
            'canonical',
        ]);
    }

    private function titleFromSlug(string $slug): string
    {
        return trim(ucwords(str_replace(['-', '_'], ' ', $slug)));
    }

    private function categoryFromSlug(string $slug): ?string
    {
        return match ($slug) {
            'graduation-attire' => 'graduation',
            'legal-attire' => 'legal',
            'church-wear' => 'church',
            default => null,
        };
    }

    private function syntheticProduct(string $slug): array
    {
        $title = $this->titleFromSlug($slug);

        return [
            'slug' => $slug,
            'title' => $title,
            'location' => 'Nairobi',
            'price' => 'Request quote',
            'cta' => 'Request Quote',
            'description' => 'Premium '.$title.' available for hire and sale through Gownsea.',
            'category' => 'graduation',
            'image' => '/images/site/hero.webp',
        ];
    }
}
