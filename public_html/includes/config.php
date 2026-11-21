<?php
/*
 * PERF website settings.
 *
 * This is the ONLY file you need to edit for contact details and most content.
 * Anything left empty ('' or []) is simply hidden on the website, so the site
 * never shows placeholder text. Fill values in as they become available.
 */

return [

    /* ---------- Site ---------- */
    'site_url'  => 'https://perf.org.in',   // no trailing slash
    'name'      => 'Poudyal Education & Research Foundation',
    'short'     => 'PERF',
    'tagline'   => 'Education • Employment • Empowerment',

    /* ---------- Contact details ---------- */
    // Phone as shown to visitors, and the same number in international format for links.
    'phone_display' => '',                  // e.g. '+91 98765 43210'
    'phone_intl'    => '',                  // e.g. '919876543210'  (digits only, with country code)
    'whatsapp_intl' => '',                  // e.g. '919876543210'  (leave empty to hide WhatsApp buttons)
    'whatsapp_text' => 'Hello PERF, I would like to know more about your programs.',
    'email'         => 'info@perf.org.in',
    'address'       => '',                  // e.g. 'Office 12, ABC Complex, Main Road, City, State 123456'
    'hours'         => 'Mon – Sat, 10:00 AM – 6:00 PM',
    // Google Map: leave empty to build the map from 'address' automatically,
    // or paste the src="..." URL from Google Maps > Share > Embed a map.
    'map_embed_url' => '',

    'social' => [                           // leave any empty to hide it
        'facebook'  => '',
        'instagram' => '',
        'youtube'   => '',
        'linkedin'  => '',
    ],

    /* ---------- Enquiry form (enquiry.php) ---------- */
    'form_to'   => 'info@perf.org.in',      // inbox that receives enquiries
    'form_from' => 'no-reply@perf.org.in',  // create this mailbox in hPanel > Emails for reliable delivery
    // Every enquiry is also saved to storage/leads.csv (not web-accessible), even if email fails.

    /* ---------- Home page ---------- */
    // Success highlights. Leave the list empty to hide the section.
    // Example: ['value' => '500+', 'label' => 'Students guided'],
    'stats' => [],

    // Testimonials slider. Leave empty to hide the section.
    // Example: ['quote' => 'PERF helped me ...', 'name' => 'Asha K.', 'detail' => 'MBBS, Georgia'],
    'testimonials' => [],

    /* ---------- About page ---------- */
    'about_extra' => '',  // extra paragraph: history, founding year, registration etc.
    'mission' => 'To make quality education and career guidance accessible to every student, helping them choose the right path and reach global opportunities with confidence.',
    'vision'  => 'To be a trusted global education and career transformation brand that empowers students to learn, work and lead anywhere in the world.',
    'values'  => ['Student first', 'Honest, transparent guidance', 'Excellence in education', 'Lifelong learning', 'Global outlook'],
    'certifications' => [],   // e.g. ['Registered under the Societies Registration Act, 2025']
    'partners'       => [],   // e.g. ['Tbilisi State Medical University, Georgia', 'XYZ Institute, Germany']

    'founder' => [
        'name'    => '',      // e.g. 'Mr. ... Poudyal'
        'title'   => 'Founder, PERF',
        'photo'   => '',      // e.g. 'assets/img/team/founder.jpg'
        'message' => '',      // founder's message; leave empty to hide the message block
    ],

    /* ---------- Analytics (optional) ---------- */
    'ga_id' => '',            // Google Analytics 4 ID, e.g. 'G-XXXXXXXXXX'
];
