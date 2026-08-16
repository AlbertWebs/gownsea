<?php

return [
    'roles' => [
        'super_admin' => ['*'],
        'admin' => [
            'dashboard', 'catalogue', 'inquiries', 'leads', 'customers', 'sales',
            'activities', 'reports', 'users', 'settings',
        ],
        'sales_manager' => [
            'dashboard', 'inquiries', 'leads', 'customers', 'sales', 'activities', 'reports',
        ],
        'sales_rep' => [
            'dashboard', 'inquiries', 'leads', 'customers', 'sales', 'activities',
        ],
        'catalogue_manager' => [
            'dashboard', 'catalogue',
        ],
    ],

    'restricted_to_assigned' => ['sales_rep'],

    'lead_stages' => ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation', 'won', 'lost'],
    'inquiry_statuses' => ['new', 'in_progress', 'responded', 'converted', 'closed'],
    'sale_statuses' => ['draft', 'quotation', 'pending', 'confirmed', 'processing', 'completed', 'cancelled'],
    'payment_statuses' => ['unpaid', 'partial', 'paid', 'refunded'],
    'activity_types' => ['call', 'meeting', 'email', 'whatsapp', 'follow_up', 'site_visit', 'demo', 'proposal', 'note', 'other'],
    'sources' => ['website', 'catalogue', 'whatsapp', 'phone', 'email', 'referral', 'walk_in', 'social', 'manual'],
];
