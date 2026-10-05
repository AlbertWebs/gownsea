<?php

namespace App\Http\Controllers;

use App\Models\JournalPost;
use App\Models\Setting;
use App\Services\CatalogueService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
        return view('pages.legal-attire', $this->categoryPage('legal', [
            'title' => 'Legal Attire in Kenya | Advocate Robes, Wigs & Bibs | Gownsea',
            'description' => 'Shop legal attire in Kenya from Gownsea: advocate gowns, barrister wigs, bibs and shirts. Ask about sizing, hire or purchase, and institutional orders.',
            'heading' => 'Legal attire for advocates and legal institutions',
            'intro' => 'Explore courtroom attire from Gownsea, including advocate gowns, barrister wigs, bibs and shirts. Ask our Nairobi team about available sizes, hire or purchase options, and orders for law firms, institutions and individual advocates.',
            'banner' => '/images/site/Amazon-seller-lawyer-renaldo-matamoro-86JiKaHF4I8-unsplash-min.jpg',
        ]));
    }

    public function graduationAttire(): View
    {
        return view('pages.graduation-attire', $this->categoryPage('graduation', [
            'title' => 'Graduation Gowns, Caps & Hoods in Kenya | Gownsea',
            'description' => 'Find graduation gowns, caps, hoods, tassels and complete sets in Kenya. Explore Gownsea products and enquire about hire, purchase and bulk orders.',
            'heading' => 'Graduation gowns and ceremony attire in Kenya',
            'intro' => 'Browse graduation gowns, caps, academic hoods, tassels and ceremony sets for different award levels. Gownsea supports students, schools, colleges, universities and event teams with product guidance and enquiries for hire, purchase or bulk orders.',
            'banner' => '/images/site/graduation-attire.jpg',
        ]));
    }

    public function churchWear(): View
    {
        return view('pages.church-wear', $this->categoryPage('church', [
            'title' => 'Church Wear in Kenya | Clergy Robes & Choir Attire | Gownsea',
            'description' => 'Browse church wear in Kenya, including clergy robes and choral attire. Explore Gownsea products and ask about custom colours, sizing and group orders.',
            'heading' => 'Church and choral wear for congregations',
            'intro' => 'Explore church and choral attire for clergy, choir members and ministry teams. Share your preferred colours, sizes, quantity and event date with Gownsea to discuss suitable garments and group order options.',
            'banner' => '/images/site/clergy-wear.webp',
            'faqs' => [
                'What church and choir garments can I enquire about?' => 'Browse the products shown on this page, then contact Gownsea with the garment, quantity and intended use so the team can confirm the available options.',
                'Can church attire be coordinated by colour?' => 'Include your church or choir colours and any design requirements in your enquiry. Gownsea can advise on available colours and suitable options.',
                'Can I request attire for a choir or ministry group?' => 'Yes. Send the number of people, sizes if known, preferred colours and required date so the team can respond about a group order.',
            ],
        ]));
    }

    /** Build consistent metadata and structured data for a public category collection. */
    private function categoryPage(string $slug, array $content): array
    {
        $properties = $this->catalogue->itemsByCategory($slug);
        $canonical = route(match ($slug) {
            'graduation' => 'graduation-attire',
            'legal' => 'legal-attire',
            default => 'church-wear',
        });
        $category = $this->catalogue->category($slug);
        $title = filled($category?->seo_title) ? $category->seo_title : $content['title'];
        $description = filled($category?->seo_description) ? $category->seo_description : $content['description'];
        $image = $this->catalogue->categoryImage($slug, $content['banner']);
        $imageUrl = preg_match('/^https?:\/\//i', $image) ? $image : url('/'.ltrim($image, '/'));
        $meta = $this->meta($title, $description);
        $meta['canonical'] = $canonical;
        $meta['og_url'] = $canonical;
        $meta['og_image'] = $imageUrl;
        $meta['twitter_image'] = $imageUrl;
        $meta['robots'] = 'index,follow,max-image-preview:large';

        $itemList = [];
        foreach ($properties as $property) {
            $slugName = (string) ($property['slug'] ?? '');
            $itemUrl = (string) ($property['url'] ?? '');
            if ($slugName !== '' && ($itemUrl === '' || ! str_starts_with($itemUrl, '/') || str_starts_with($itemUrl, '//') || in_array(rtrim($itemUrl, '/'), ['/shop-attire/graduation-attire', '/shop-attire/legal-attire', '/shop-attire/church-wear'], true))) {
                $itemUrl = route('products.show', $slugName, false);
            }
            $itemImage = (string) (($property['gallery'][0] ?? null) ?: ($property['image'] ?? ''));
            $item = ['@type' => 'Product', 'name' => trim(strip_tags((string) ($property['title'] ?? '')))];
            if ($itemUrl !== '') $item['url'] = url($itemUrl);
            if ($itemImage !== '') $item['image'] = preg_match('/^https?:\/\//i', $itemImage) ? $itemImage : url('/'.ltrim($itemImage, '/'));
            $itemList[] = ['@type' => 'ListItem', 'position' => count($itemList) + 1, 'item' => $item];
        }

        return [
            'meta' => $meta,
            'properties' => $properties,
            'bannerImage' => $image,
            'categoryHeading' => $content['heading'],
            'categoryIntro' => $content['intro'],
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    ['@type' => 'CollectionPage', '@id' => $canonical.'#collection', 'url' => $canonical, 'name' => $title, 'description' => $description, 'inLanguage' => 'en-KE', 'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => $imageUrl], 'mainEntity' => ['@type' => 'ItemList', 'itemListElement' => $itemList]],
                    ['@type' => 'BreadcrumbList', 'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => $content['heading'], 'item' => $canonical],
                    ]],
                ],
            ],
            'faqs' => $content['faqs'] ?? [],
        ];
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
        $property = $this->catalogue->findBySlug($slug);
        abort_if($property === null, 404);
        $property = $this->catalogue->enrich($property);
        $productName = trim(html_entity_decode(strip_tags((string) ($property['title'] ?? $this->titleFromSlug($slug))), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $seoProductName = trim(preg_replace('/\\s*&\\s*/u', ' and ', $productName) ?? $productName);
        $categoryName = Str::headline((string) ($property['category'] ?? 'graduation'));
        $canonicalPath = (string) ($property['url'] ?? '');
        $categoryPaths = ['/shop-attire/graduation-attire', '/shop-attire/legal-attire', '/shop-attire/church-wear'];
        if ($canonicalPath === '' || ! str_starts_with($canonicalPath, '/') || str_starts_with($canonicalPath, '//') || in_array(rtrim($canonicalPath, '/'), $categoryPaths, true)) {
            $canonicalPath = route('products.show', $slug, false);
        }
        $canonical = url($canonicalPath);
        $seoTitle = filled($property['seo_title'] ?? null)
            ? trim(html_entity_decode((string) $property['seo_title'], ENT_QUOTES | ENT_HTML5, 'UTF-8'))
            : Str::limit($seoProductName.' in Kenya | Gownsea', 68, '');
        $summary = trim(preg_replace('/\\s+/u', ' ', html_entity_decode(strip_tags((string) ($property['description'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');
        $descriptionSuffix = ' Enquire with Gownsea in Kenya.';
        $descriptionRoom = max(20, 160 - mb_strlen($seoProductName.': ') - mb_strlen($descriptionSuffix) - 2);
        $summarySnippet = $summary;
        $summaryTruncated = mb_strlen($summarySnippet) > $descriptionRoom;
        if ($summaryTruncated) {
            $summarySnippet = mb_substr($summarySnippet, 0, $descriptionRoom - 1);
            $lastSpace = mb_strrpos($summarySnippet, ' ');
            $summarySnippet = rtrim(mb_substr($summarySnippet, 0, $lastSpace === false ? $descriptionRoom - 1 : $lastSpace), " \t\n\r\0\x0B.,;:!?").'…';
        } else {
            $summarySnippet = rtrim($summarySnippet, " \t\n\r\0\x0B.,;:!?");
        }
        $seoDescription = filled($property['seo_description'] ?? null)
            ? trim(html_entity_decode((string) $property['seo_description'], ENT_QUOTES | ENT_HTML5, 'UTF-8'))
            : $seoProductName.': '.$summarySnippet.($summaryTruncated ? '' : '.').$descriptionSuffix;
        if (mb_strlen($seoDescription) > 160) {
            $seoDescription = mb_substr($seoDescription, 0, 159);
            $lastSpace = mb_strrpos($seoDescription, ' ');
            $seoDescription = rtrim(mb_substr($seoDescription, 0, $lastSpace === false ? 159 : $lastSpace)).'…';
        }
        $primaryImage = (string) (($property['gallery'][0] ?? null) ?: ($property['image'] ?? '/images/site/hero.webp'));
        $primaryImage = preg_match('/^https?:\\/\\//i', $primaryImage) ? $primaryImage : url('/'.ltrim($primaryImage, '/'));
        $meta = $this->meta($seoTitle, $seoDescription);
        $meta['canonical'] = $canonical;
        $meta['og_url'] = $canonical;
        $meta['og_type'] = 'product';
        $meta['og_image'] = $primaryImage;
        $meta['twitter_image'] = $primaryImage;
        $meta['robots'] = 'index,follow,max-image-preview:large';
        $meta['product_sku'] = $property['sku'] ?? null;
        $meta['product_brand'] = (string) ($property['brand'] ?? 'Gownsea LTD');

        $productSchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                array_filter([
                    '@type' => 'Product',
                    '@id' => $canonical.'#product',
                    'name' => $productName,
                    'description' => trim(preg_replace('/\\s+/u', ' ', html_entity_decode(strip_tags(preg_replace('/<\\s*\\/?\\s*(p|li|br|h[1-6])\\b[^>]*>/i', ' ', (string) ($property['about'] ?? $summary))), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? $summary),
                    'image' => array_values(array_unique(array_map(
                        fn (string $image) => preg_match('/^https?:\\/\\//i', $image) ? $image : url('/'.ltrim($image, '/')),
                        array_filter((array) ($property['gallery'] ?? [$property['image'] ?? '/images/site/hero.webp']))
                    ))),
                    'sku' => $property['sku'] ?? null,
                    'category' => $categoryName,
                    'brand' => ['@type' => 'Brand', 'name' => (string) ($property['brand'] ?? 'Gownsea LTD')],
                    'url' => $canonical,
                ], fn ($value) => $value !== null && $value !== ''),
                [
                    '@type' => 'BreadcrumbList',
                    '@id' => $canonical.'#breadcrumb',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => $categoryName.' Attire', 'item' => route(match ($property['category'] ?? 'graduation') {
                            'legal' => 'legal-attire', 'church' => 'church-wear', default => 'graduation-attire',
                        })],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $productName, 'item' => $canonical],
                    ],
                ],
            ],
        ];
        $optionProperties = collect((array) ($property['options'] ?? []))
            ->map(function ($values, $label) {
                $values = is_array($values) ? $values : [$values];
                $value = implode(', ', array_filter(array_map('strval', $values)));

                return $value === '' ? null : [
                    '@type' => 'PropertyValue',
                    'name' => Str::headline((string) $label),
                    'value' => $value,
                ];
            })
            ->filter()
            ->values()
            ->all();
        if ($optionProperties !== []) {
            $productSchema['@graph'][0]['additionalProperty'] = $optionProperties;
        }

        // A product can be offered for hire without a purchase price. Keep a
        // priced Offer in structured data for that case as well.
        $amount = $property['sale_price_amount'] ?? $property['price_amount'] ?? $property['hire_price_amount'] ?? null;
        if ($amount === null) {
            $listedPrice = (string) ($property['sale_price'] ?? $property['price'] ?? $property['hire_price'] ?? '');
            $amount = preg_match('/(?:KES|KSh|KShs?)\\s*([0-9][0-9,]*(?:\\.\\d{1,2})?)/i', $listedPrice, $match)
                ? str_replace(',', '', $match[1])
                : null;
        }
        if (is_numeric($amount) && (float) $amount > 0) {
            $offer = [
                '@type' => 'Offer',
                'url' => $canonical,
                'priceCurrency' => strtoupper((string) Setting::getValue('currency', Setting::getValue('brand.currency', 'KES'))),
                'price' => number_format((float) $amount, 2, '.', ''),
                'itemCondition' => 'https://schema.org/NewCondition',
                'seller' => ['@type' => 'Organization', 'name' => 'Gownsea LTD', 'url' => route('home')],
            ];
            if (filled($property['availability'] ?? null)) {
                $availabilityKey = strtolower((string) $property['availability']);
                $offer['availability'] = match ($availabilityKey) {
                    'out_of_stock', 'sold_out' => 'https://schema.org/OutOfStock',
                    'preorder', 'pre_order' => 'https://schema.org/PreOrder',
                    default => 'https://schema.org/InStock',
                };
                $meta['product_availability'] = match ($availabilityKey) {
                    'out_of_stock', 'sold_out' => 'out of stock',
                    'preorder', 'pre_order' => 'preorder',
                    default => 'in stock',
                };
            }
            $productSchema['@graph'][0]['offers'] = $offer;
            $meta['product_price'] = number_format((float) $amount, 2, '.', '');
            $meta['product_currency'] = $offer['priceCurrency'];
            $meta['product_condition'] = 'new';
        }

        $related = collect($this->catalogue->itemsByCategory($property['category'] ?? 'graduation'))
            ->reject(fn (array $item) => ($item['slug'] ?? '') === $slug)
            ->take(4)
            ->values()
            ->all();

        return view('pages.properties.show', [
            'meta' => $meta,
            'productSchema' => $productSchema,
            'property' => $property,
            'related' => $related,
        ]);
    }

    public function journalIndex(): View
    {
        $posts = $this->journalPosts();
        $canonical = route('journal.index');
        $title = 'The Gown Journal | Graduation & Ceremony Guides | Gownsea';
        $description = 'Read practical guides from Gownsea on graduation gowns, academic regalia, legal attire, church wear and ceremony planning in Kenya.';
        $structuredData = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    '@id' => $canonical.'#journal',
                    'url' => $canonical,
                    'name' => $title,
                    'description' => $description,
                    'mainEntity' => [
                        '@type' => 'ItemList',
                        'itemListElement' => collect($posts)->values()->map(fn (array $post, int $index) => [
                            '@type' => 'ListItem',
                            'position' => $index + 1,
                            'url' => route('journal.show', $post['slug']),
                            'name' => $post['title'],
                        ])->all(),
                    ],
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'The Gown Journal', 'item' => $canonical],
                    ],
                ],
            ],
        ];

        return view('pages.journal.index', [
            'meta' => $this->meta($title, $description),
            'posts' => $posts,
            'structuredData' => $structuredData,
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
        $galleryImages = array_map(
            fn (string $path) => str_starts_with($path, 'http') ? $path : url(ltrim($path, '/')),
            $post['images'] ?? [],
        );
        $image = $galleryImages[0] ?? url('/images/site/hero.webp');
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
                    'image' => $galleryImages ?: [$image],
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

    public function shopAttireCollection(string $slug): \Illuminate\Http\RedirectResponse
    {
        $route = match ($slug) {
            'graduation-attire' => 'graduation-attire',
            'legal-attire' => 'legal-attire',
            'church-wear' => 'church-wear',
            default => null,
        };

        abort_if($route === null, 404);

        return redirect()->route($route, [], 301);
    }

    public function shopAttireCategory(string $mainSlug, string $slug): View
    {
        $matched = $this->catalogue->findBySlug($slug);

        if ($matched) {
            return $this->productShow($slug);
        }

        abort(404);
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
            'robots' => 'index,follow,max-image-preview:large',
            'og_title' => $title,
            'og_description' => $description,
            'og_type' => 'website',
            'og_image' => url('/images/site/hero.webp'),
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $title,
            'twitter_description' => $description,
            'twitter_image' => url('/images/site/hero.webp'),
            'canonical' => url()->current(),
        ], [
            'title',
            'description',
            'robots',
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

}
