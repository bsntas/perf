<?php
/*
 * PERF website settings.
 *
 * This is the ONLY file you need to edit for contact details and most content.
 * Anything left empty ('' or []) is simply hidden on the website, so the site
 * never shows placeholder text. Fill values in as they become available.
 *
 * Most of these can also be changed online in the admin panel (https://perf.org.in/admin).
 * Values saved there take priority over this file (they live in storage/settings.json).
 */

return [

    /* ---------- Site ---------- */
    'site_url'  => 'https://perf.org.in',   // no trailing slash
    'name'      => 'Poudyal Education & Research Foundation',
    'short'     => 'PERF',
    'tagline'   => 'Education • Employment • Empowerment',
    'footer_text' => 'PERF guides students and families through education, careers and global opportunities.',

    // Announcement strip at the very top of every page. Leave 'text' empty to hide it.
    // 'until' (optional, YYYY-MM-DD) hides it automatically after that date.
    'announcement' => ['text' => '', 'link' => '', 'link_text' => 'Learn more', 'until' => ''],

    /* ---------- Contact details ---------- */
    // Phone as shown to visitors, and the same number in international format for links.
    'phone_display' => '',                  // e.g. '+91 98765 43210'
    'phone_intl'    => '',                  // e.g. '919876543210'  (digits only, with country code)
    'whatsapp_intl' => '',                  // e.g. '919876543210'  (leave empty to hide WhatsApp buttons)
    'whatsapp_text' => 'Hello PERF, I would like to know more about your programs.',
    'email'         => 'info@perf.org.in',
    'address'       => '1st Floor, Milestone Building, 2.5 Mile',  // add city, state and PIN code
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
    // Main heading at the top of the home page; 'hero_highlight' is added after it in gold.
    'hero_title'     => 'Your global education and career journey',
    'hero_highlight' => 'starts here',
    'hero_text'      => "We are bringing quality education and global careers within every student's reach, with honest guidance on counseling, medical admissions, studies abroad and skill development.",
    'hero_points'    => ['Free first session', 'India & abroad', 'End-to-end support'],

    // "Our core strengths" figures. 'text' is an optional short line under the label.
    // Example: ['value' => '500+', 'label' => 'Students guided', 'text' => 'Into medical, management and abroad programs.'],
    // Leave empty to show facts taken from the site content instead.
    'stats' => [
        ['value' => '500+', 'label' => 'Students guided', 'text' => 'Into international medical programs.'],
        ['value' => '10+', 'label' => 'Years of experience', 'text' => 'Advancing medical, professional and skill-based education.'],
        ['value' => '360°', 'label' => 'Psychometric assessment', 'text' => 'Aptitude, personality and skill-gap testing.'],
        ['value' => '2021', 'label' => 'Year founded', 'text' => 'Registered non-profit with MCA and NITI Aayog.'],
    ],

    // Upcoming events (seminars, webinars, university fairs). Leave empty to hide the section.
    // Example: ['date' => '2026-12-14', 'title' => 'MBBS Abroad Seminar', 'place' => 'PERF office / Online', 'text' => 'Meet counselors and get your questions answered.', 'link' => ''],
    // Events whose date has passed are hidden automatically.
    'events' => [],

    // Testimonials slider. Leave empty to hide the section.
    // Example: ['quote' => 'PERF helped me ...', 'name' => 'Asha K.', 'detail' => 'MBBS, Georgia'],
    'testimonials' => [],

    /* ---------- About page ---------- */
    // "Who we are" text. Separate paragraphs with a blank line.
    'about_intro' => "PERF is an organization working in education, career counseling, medical admissions, abroad studies, skill development and global opportunities. We help students and families make confident, well-informed decisions about their academic and professional future.\n\nFrom choosing a stream after school to securing admission in a recognised university abroad or a paid training program in Germany, our counselors stay with students through every step.",
    'about_extra' => "Formed in 2021 under the leadership of Mr. Hemant Poudyal, Poudyal Education & Research Foundation was officially incorporated as a non-profit organization with the Ministry of Corporate Affairs (MCA) and registered with NITI Aayog, Government of India, on June 29, 2023.\n\nPERF has guided over 500 Indian students into international medical programs, alongside providing psychometric career counseling, stream selection and global job placement assistance.",  // history, founding year, registration etc. Separate paragraphs with a blank line.
    'mission' => 'To make quality education and career guidance accessible to every student, helping them choose the right path and reach global opportunities with confidence.',
    'vision'  => 'To be a trusted global education and career transformation brand that empowers students to learn, work and lead anywhere in the world.',
    'values'  => ['Student first', 'Honest, transparent guidance', 'Excellence in education', 'Lifelong learning', 'Global outlook'],
    'certifications' => [
        'Incorporated as a non-profit with the Ministry of Corporate Affairs (CIN: U85500WB2023NPL263149)',
        'Registered with NITI Aayog, Government of India (June 29, 2023)',
    ],
    'partners'       => [],   // e.g. ['Tbilisi State Medical University, Georgia', 'XYZ Institute, Germany']

    'founder' => [
        'name'    => 'Mr. Hemant Poudyal',
        'title'   => 'Founder & Chairman, PERF',
        'photo'   => 'assets/img/team/hemant-poudyal.jpg',
        // Short profile shown above the message.
        'bio'     => 'Hailing from Pakyong District, Sikkim, Mr. Poudyal holds a background in Business Administration and has dedicated over a decade to advancing professional, medical and skill-based education for Indian youth. He has also been recognised by the Human Rights Council for his contributions to youth empowerment and educational access.',
        // Leave the message empty to hide the whole block.
        'message' => "At Poudyal Education & Research Foundation, we believe that financial or geographical barriers should never stand in the way of a student's ambition. Over the last decade, our mission has been to bridge the gap between aspiring Indian youth and world-class global education.\n\nWhether a student dreams of wearing a doctor's white coat, mastering a specialized vocational skill, or building an international career, we stand beside them at every step, from scientific psychometric counseling and university admissions to scholarships, education loans, and visa support.\n\nTogether, we are not just building careers; we are empowering a globally competitive generation.",
    ],

    /* ---------- Analytics (optional) ---------- */
    'ga_id' => '',            // Google Analytics 4 ID, e.g. 'G-XXXXXXXXXX'
];
