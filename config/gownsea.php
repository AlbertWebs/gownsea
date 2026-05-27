<?php

return [
    'brand' => [
        'name' => 'Gownsea LTD',
        'primary_color' => '#d42127',
        'phone' => '+254 728 311537',
        'email' => 'hello@gownsea.com',
        'address' => 'Valji Building, Moktar Daddah Street, Nairobi',
        'whatsapp' => '254728311537',
    ],

    'assistant' => [
        'admin_email' => env('ASSISTANT_ADMIN_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@gownsea.com')),
        'faqs' => [
            'Buying process' => 'How do I buy or hire a gown from Gownsea?',
            'Available properties' => 'What categories are currently available?',
            'Pricing' => 'How much does gown hire or purchase cost?',
            'Contact support' => 'How can I quickly contact support?',
        ],
    ],

    'protected_routes' => [
        '/',
        '/about-us',
        '/contact-us',
        '/legal-attire',
        '/shop-attire/graduation-attire',
        '/shop-attire/legal-attire',
        '/shop-attire/church-wear',
        '/bulk-inquiry',
        '/the-gown-journal',
        '/the-gown-journal/bulk-hiring-of-gowns-for-institutions-a-cost-effective-and-convenient-solution',
        '/the-gown-journal/why-renting-your-graduation-gown-is-a-smart-choice',
        '/the-gown-journal/what-your-grads-really-think-about-their-gowns-and-why-it-matters',
        '/privacy-policy',
        '/terms-and-conditions',
        '/return-policy',
        '/copyright',

        // Shop Attire (Graduation Attire dropdown)
        '/shop-attire-collection/graduation-attire/graduation-cap',
        '/our-products/graduation-tassels',
        '/our-products/undergraduate-academic-hoods',
        '/shop-attire-collection/graduation-attire/graduation-hoods',
        '/our-products/graduation-stoles',
        '/shop-attire-collection/graduation-attire/phd-caps',
        '/our-products/preschool-graduation',
        '/shop-attire-collection/graduation-attire/pre-school-gowns',
        '/shop-attire-collection/graduation-attire/certificate-gowns',
        '/shop-attire-collection/graduation-attire/diploma-gowns',
        '/shop-attire-collection/graduation-attire/masters-gowns',
        '/shop-attire-collection/graduation-attire/phd-gowns',
        '/our-products/phd-graduation-gown',
        '/shop-attire-collection/graduation-attire/degree-gown',
        '/our-products/degree-graduation-gowns',

    ],

    'properties' => [
        [
            'slug' => 'bachelors-graduation-gown-cap-hood-set',
            'title' => 'Bachelors Graduation Gown, Cap & Hood Set',
            'location' => 'Nairobi',
            'price' => 'KES 12,500',
            'cta' => 'Reserve Set',
            'description' => 'University-standard complete set for ceremonies and photo sessions.',
            'category' => 'graduation',
            'image' => 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=1200&q=70',
            'url' => '/shop-attire-collection/graduation-attire/degree-gown',
        ],
        [
            'slug' => 'traditional-barrister-wig-and-gown',
            'title' => 'Traditional Barrister Wig & Gown',
            'location' => 'Nairobi CBD',
            'price' => 'KES 38,000',
            'cta' => 'Request Quote',
            'description' => 'Premium courtroom-ready legal attire tailored for professional use.',
            'category' => 'legal',
            'image' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=1200&q=70',
            'url' => '/shop-attire/legal-attire',
        ],
        [
            'slug' => 'choral-and-church-attire-pack',
            'title' => 'Choral & Church Attire Pack',
            'location' => 'Nationwide Delivery',
            'price' => 'KES 8,700',
            'cta' => 'Order Pack',
            'description' => 'Elegant church wear with customized fitting for ministry teams.',
            'category' => 'church',
            'image' => 'https://images.unsplash.com/photo-1519491050282-cf00c82424b4?auto=format&fit=crop&w=1200&q=70',
            'url' => '/shop-attire/church-wear',
        ],
    ],

    'journal_posts' => [
        [
            'slug' => 'bulk-hiring-of-gowns-for-institutions-a-cost-effective-and-convenient-solution',
            'title' => 'Bulk Hiring of Gowns for Institutions: A Cost-Effective and Convenient Solution',
            'category' => 'Graduation Attire',
            'date' => '2025-07-11',
            'excerpt' => 'How institutions can reduce logistics pressure while improving graduation-day consistency.',
        ],
        [
            'slug' => 'why-renting-your-graduation-gown-is-a-smart-choice',
            'title' => 'Why Renting Your Graduation Gown is a Smart Choice',
            'category' => 'Graduation Attire',
            'date' => '2025-06-20',
            'excerpt' => 'A practical guide to balancing cost, convenience, and sustainability for graduates.',
        ],
        [
            'slug' => 'what-your-grads-really-think-about-their-gowns-and-why-it-matters',
            'title' => 'What Your Grads Really Think About Their Gowns - And Why It Matters',
            'category' => 'Graduation Attire',
            'date' => '2025-06-14',
            'excerpt' => 'Feedback insights that can improve satisfaction and institutional ceremony quality.',
        ],
    ],
];
