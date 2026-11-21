<?php
if (!defined('PERF')) { http_response_code(403); exit; }
/*
 * What the admin panel can edit. Each section is one page in the admin menu.
 *   'store'  => 'config' (config.php) or 'data' (data.php): where the default value comes from.
 *   'fields' => key => field. Field types:
 *       text, textarea, email, url, map, digits, date, select, checkbox, icon, html,
 *       lines (one item per line -> list), services (tick boxes of service slugs),
 *       image (upload into 'dir', stores the path), object (sub-'fields'),
 *       rows (repeatable list of sub-'fields'; 'key' = field used as the array key,
 *             'group' = field used to group rows, e.g. FAQ topics).
 * Sections with 'page' instead of 'fields' have their own screen in admin.php.
 */

$social = [];
foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'linkedin' => 'LinkedIn'] as $k => $l)
    $social[$k] = ['type' => 'url', 'label' => $l, 'placeholder' => "https://www.$k.com/..."];

return [

    /* ---------- Quick edits ---------- */
    'contact' => [
        'menu' => 'Quick edits', 'title' => 'Contact details', 'icon' => 'phone', 'store' => 'config',
        'intro' => 'Shown in the top bar, footer, contact page and WhatsApp buttons. Leave a field empty to hide it on the website.',
        'fields' => [
            'phone_display' => ['type' => 'text', 'label' => 'Phone (as shown to visitors)', 'placeholder' => '+91 98765 43210'],
            'phone_intl'    => ['type' => 'digits', 'label' => 'Phone for the "call" button', 'help' => 'Digits only, with country code, e.g. 919876543210.'],
            'whatsapp_intl' => ['type' => 'digits', 'label' => 'WhatsApp number', 'help' => 'Digits only, with country code. Leave empty to hide all WhatsApp buttons.'],
            'whatsapp_text' => ['type' => 'text', 'label' => 'WhatsApp starting message', 'help' => 'Pre-filled text when a visitor taps a WhatsApp button.'],
            'email'         => ['type' => 'email', 'label' => 'Email shown on the website'],
            'address'       => ['type' => 'textarea', 'rows' => 2, 'label' => 'Office address'],
            'hours'         => ['type' => 'text', 'label' => 'Office hours', 'placeholder' => 'Mon – Sat, 10:00 AM – 6:00 PM'],
            'map_embed_url' => ['type' => 'map', 'label' => 'Google Map', 'help' => 'Optional. In Google Maps: Share → Embed a map → Copy HTML, and paste it here. Leave empty to build the map from the address.'],
            'social'        => ['type' => 'object', 'label' => 'Social media links', 'fields' => $social],
        ],
    ],
    'announcement' => [
        'menu' => 'Quick edits', 'title' => 'Announcement', 'icon' => 'spark', 'store' => 'config',
        'intro' => 'A gold strip at the top of every page, e.g. "Admissions open for 2027 intake". Clear the text to hide it.',
        'fields' => [
            'announcement' => ['type' => 'object', 'label' => '', 'fields' => [
                'text'      => ['type' => 'text', 'label' => 'Message'],
                'link'      => ['type' => 'url', 'label' => 'Link (optional)', 'placeholder' => 'https://... or /contact'],
                'link_text' => ['type' => 'text', 'label' => 'Link text', 'placeholder' => 'Learn more'],
                'until'     => ['type' => 'date', 'label' => 'Hide automatically after (optional)'],
            ]],
        ],
    ],
    'events' => [
        'menu' => 'Quick edits', 'title' => 'Events', 'icon' => 'clock', 'store' => 'config',
        'intro' => 'Upcoming seminars, webinars and fairs on the home page. Events whose date has passed are hidden automatically, so there is no need to delete them straight away.',
        'fields' => [
            'events' => ['type' => 'rows', 'label' => 'Events', 'add' => 'Add event', 'title_field' => 'title', 'fields' => [
                'date'  => ['type' => 'date', 'label' => 'Date'],
                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                'place' => ['type' => 'text', 'label' => 'Place', 'placeholder' => 'PERF office / Online'],
                'text'  => ['type' => 'textarea', 'rows' => 2, 'label' => 'Short description'],
                'link'  => ['type' => 'url', 'label' => 'Registration link (optional)', 'help' => 'Leave empty to send visitors to the enquiry form.'],
            ]],
        ],
    ],
    'gallery'  => ['menu' => 'Quick edits', 'title' => 'Gallery photos', 'icon' => 'star', 'page' => 'gallery'],
    'leads'    => ['menu' => 'Quick edits', 'title' => 'Enquiries', 'icon' => 'mail', 'page' => 'leads'],

    /* ---------- Home page ---------- */
    'home' => [
        'menu' => 'Home page', 'title' => 'Top banner & figures', 'icon' => 'grad', 'store' => 'config',
        'fields' => [
            'hero_title'     => ['type' => 'text', 'label' => 'Main heading', 'required' => true],
            'hero_highlight' => ['type' => 'text', 'label' => 'Highlighted words after the heading (gold)'],
            'hero_text'      => ['type' => 'textarea', 'rows' => 3, 'label' => 'Introduction'],
            'hero_points'    => ['type' => 'lines', 'label' => 'Short points under the buttons', 'help' => 'One per line. Three works best.'],
            'stats' => ['type' => 'rows', 'label' => '"Our core strengths" figures', 'add' => 'Add figure', 'title_field' => 'label',
                'help' => 'Leave the list empty to show facts taken from the site (number of programs, destinations, steps).', 'fields' => [
                'value' => ['type' => 'text', 'label' => 'Figure', 'placeholder' => '500+', 'required' => true],
                'label' => ['type' => 'text', 'label' => 'Label', 'placeholder' => 'Students guided', 'required' => true],
                'text'  => ['type' => 'text', 'label' => 'Short line (optional)'],
            ]],
        ],
    ],
    'testimonials' => [
        'menu' => 'Home page', 'title' => 'Testimonials', 'icon' => 'users', 'store' => 'config',
        'intro' => 'Quotes from students and parents. The section is hidden while the list is empty.',
        'fields' => [
            'testimonials' => ['type' => 'rows', 'label' => 'Testimonials', 'add' => 'Add testimonial', 'title_field' => 'name', 'fields' => [
                'quote'  => ['type' => 'textarea', 'rows' => 3, 'label' => 'Quote', 'required' => true],
                'name'   => ['type' => 'text', 'label' => 'Name', 'required' => true],
                'detail' => ['type' => 'text', 'label' => 'Detail (optional)', 'placeholder' => 'MBBS, Georgia'],
            ]],
        ],
    ],
    'goals' => [
        'menu' => 'Home page', 'title' => '"Find your path"', 'icon' => 'compass', 'store' => 'data',
        'intro' => 'Goals visitors can tap on the home page, and the services shown for each one.',
        'fields' => [
            'goals' => ['type' => 'rows', 'label' => 'Goals', 'add' => 'Add goal', 'title_field' => 'title', 'fields' => [
                'icon'     => ['type' => 'icon', 'label' => 'Icon'],
                'title'    => ['type' => 'text', 'label' => 'Goal', 'required' => true],
                'text'     => ['type' => 'text', 'label' => 'Short line'],
                'services' => ['type' => 'services', 'label' => 'Matching services', 'required' => true],
            ]],
        ],
    ],
    'destinations' => [
        'menu' => 'Home page', 'title' => 'Study destinations', 'icon' => 'globe', 'store' => 'data',
        'intro' => 'Countries on the home page and in the footer.',
        'fields' => [
            'destinations' => ['type' => 'rows', 'label' => 'Destinations', 'add' => 'Add destination', 'title_field' => 'name', 'fields' => [
                'code'     => ['type' => 'text', 'label' => 'Badge (2 letters)', 'maxlength' => 3, 'required' => true],
                'name'     => ['type' => 'text', 'label' => 'Country', 'required' => true],
                'text'     => ['type' => 'text', 'label' => 'Short line'],
                'services' => ['type' => 'services', 'label' => 'Matching services (first one is linked)', 'required' => true],
            ]],
        ],
    ],
    'support' => [
        'menu' => 'Home page', 'title' => 'End-to-end support', 'icon' => 'check', 'store' => 'data',
        'fields' => [
            'support' => ['type' => 'rows', 'label' => 'Support items', 'add' => 'Add item', 'title_field' => 'title', 'fields' => [
                'icon'  => ['type' => 'icon', 'label' => 'Icon'],
                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                'text'  => ['type' => 'text', 'label' => 'Short line'],
            ]],
        ],
    ],
    'process' => [
        'menu' => 'Home page', 'title' => 'How it works (steps)', 'icon' => 'arrow', 'store' => 'data',
        'fields' => [
            'process' => ['type' => 'rows', 'label' => 'Steps', 'add' => 'Add step', 'title_field' => 'title', 'fields' => [
                'title' => ['type' => 'text', 'label' => 'Step', 'required' => true],
                'text'  => ['type' => 'text', 'label' => 'Short line'],
            ]],
        ],
    ],

    /* ---------- Pages ---------- */
    'services' => [
        'menu' => 'Pages', 'title' => 'Services', 'icon' => 'briefcase', 'store' => 'data',
        'intro' => 'Programs on the services page, home page and enquiry form. The web address part is used in links: change it only if you have to.',
        'fields' => [
            'services' => ['type' => 'rows', 'label' => 'Services', 'add' => 'Add service', 'title_field' => 'title', 'key' => 'slug', 'fields' => [
                'title'  => ['type' => 'text', 'label' => 'Name', 'required' => true],
                'slug'   => ['type' => 'text', 'label' => 'Web address part', 'help' => 'Lowercase letters, numbers and dashes. Filled in from the name if left empty.'],
                'cat'    => ['type' => 'select', 'label' => 'Category', 'options' => 'categories'],
                'icon'   => ['type' => 'icon', 'label' => 'Icon'],
                'home'   => ['type' => 'checkbox', 'label' => 'Show on the home page'],
                'text'   => ['type' => 'textarea', 'rows' => 2, 'label' => 'Description'],
                'points' => ['type' => 'lines', 'label' => 'Key points (one per line)'],
            ]],
        ],
    ],
    'categories' => [
        'menu' => 'Pages', 'title' => 'Service categories', 'icon' => 'search', 'store' => 'data',
        'intro' => 'Filter buttons on the services page.',
        'fields' => [
            'categories' => ['type' => 'rows', 'label' => 'Categories', 'add' => 'Add category', 'title_field' => 'label', 'key' => 'slug', 'flat' => 'label', 'fields' => [
                'label' => ['type' => 'text', 'label' => 'Name', 'required' => true],
                'slug'  => ['type' => 'text', 'label' => 'Code', 'help' => 'Lowercase letters, numbers and dashes. Filled in from the name if left empty.'],
            ]],
        ],
    ],
    'brochure' => ['menu' => 'Pages', 'title' => 'Brochure (PDF)', 'icon' => 'download', 'page' => 'brochure'],
    'about' => [
        'menu' => 'Pages', 'title' => 'About page', 'icon' => 'book', 'store' => 'config',
        'fields' => [
            'about_intro'    => ['type' => 'textarea', 'rows' => 6, 'label' => '"Who we are"', 'help' => 'Separate paragraphs with a blank line.'],
            'about_extra'    => ['type' => 'textarea', 'rows' => 3, 'label' => 'Extra paragraph (history, founding year, registration...)'],
            'mission'        => ['type' => 'textarea', 'rows' => 3, 'label' => 'Mission'],
            'vision'         => ['type' => 'textarea', 'rows' => 3, 'label' => 'Vision'],
            'values'         => ['type' => 'lines', 'label' => 'Values (one per line)'],
            'certifications' => ['type' => 'lines', 'label' => 'Registrations & certifications (one per line)'],
            'partners'       => ['type' => 'lines', 'label' => 'Partner institutions (one per line)'],
            'founder'        => ['type' => 'object', 'label' => "Founder's message", 'help' => 'The block is hidden while the message is empty.', 'fields' => [
                'name'    => ['type' => 'text', 'label' => 'Name'],
                'title'   => ['type' => 'text', 'label' => 'Title', 'placeholder' => 'Founder, PERF'],
                'photo'   => ['type' => 'image', 'label' => 'Photo', 'dir' => 'assets/img/team'],
                'message' => ['type' => 'textarea', 'rows' => 6, 'label' => 'Message', 'help' => 'Separate paragraphs with a blank line.'],
            ]],
        ],
    ],
    'why' => [
        'menu' => 'Pages', 'title' => 'Why PERF', 'icon' => 'shield', 'store' => 'data',
        'fields' => [
            'why' => ['type' => 'rows', 'label' => 'Reasons', 'add' => 'Add reason', 'title_field' => 'title', 'fields' => [
                'icon'  => ['type' => 'icon', 'label' => 'Icon'],
                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                'text'  => ['type' => 'textarea', 'rows' => 2, 'label' => 'Description'],
            ]],
        ],
    ],
    'faqs' => [
        'menu' => 'Pages', 'title' => 'FAQs', 'icon' => 'search', 'store' => 'data',
        'intro' => 'Questions are grouped by topic, in the order of first appearance. Answers may use simple formatting: <b>, <i>, <a href="...">, <br>.',
        'fields' => [
            'faqs' => ['type' => 'rows', 'label' => 'Questions', 'add' => 'Add question', 'title_field' => 'q', 'group' => 'topic', 'fields' => [
                'topic' => ['type' => 'text', 'label' => 'Topic', 'placeholder' => 'General', 'required' => true],
                'q'     => ['type' => 'text', 'label' => 'Question', 'required' => true],
                'a'     => ['type' => 'html', 'rows' => 3, 'label' => 'Answer', 'required' => true],
            ]],
        ],
    ],

    /* ---------- Settings ---------- */
    'site' => [
        'menu' => 'Settings', 'title' => 'Site & enquiry form', 'icon' => 'tools', 'store' => 'config',
        'fields' => [
            'name'        => ['type' => 'text', 'label' => 'Organisation name', 'required' => true],
            'short'       => ['type' => 'text', 'label' => 'Short name', 'required' => true],
            'tagline'     => ['type' => 'text', 'label' => 'Tagline'],
            'footer_text' => ['type' => 'textarea', 'rows' => 2, 'label' => 'Footer text'],
            'form_to'     => ['type' => 'email', 'label' => 'Send enquiries to', 'help' => 'Inbox that receives website enquiries. Every enquiry is also saved under "Enquiries".'],
            'form_from'   => ['type' => 'email', 'label' => 'Send enquiries from', 'help' => 'Must be a mailbox on perf.org.in (create it in hPanel → Emails) or emails may land in spam.'],
            'ga_id'       => ['type' => 'text', 'label' => 'Google Analytics 4 ID (optional)', 'placeholder' => 'G-XXXXXXXXXX', 'pattern' => '/^G-[A-Z0-9]{4,}$/i'],
        ],
    ],
    'account' => ['menu' => 'Settings', 'title' => 'Password & backups', 'icon' => 'shield', 'page' => 'account'],
];
