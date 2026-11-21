<?php
/* Services and FAQs. Edit text here; the slugs are used in links and the enquiry form. */

return [

    'categories' => [
        'career'  => 'Career',
        'medical' => 'Medical',
        'abroad'  => 'Study Abroad',
        'skills'  => 'Skills & Online',
    ],

    'services' => [
        'career-counseling' => [
            'cat' => 'career', 'icon' => 'compass', 'home' => true,
            'title' => 'Career Counseling',
            'text'  => 'One-to-one guidance to choose the right stream, course and country based on your strengths, interests and goals.',
            'points' => ['Aptitude & interest assessment', 'Stream and course selection after 10th / 12th', 'Personalised career roadmap', 'Guidance for parents'],
        ],
        'medical-education' => [
            'cat' => 'medical', 'icon' => 'stethoscope',
            'title' => 'Medical Education',
            'text'  => 'Admission support and mentoring for aspiring doctors and healthcare professionals in India and abroad.',
            'points' => ['NEET guidance & counseling', 'College and seat selection', 'Documentation support', 'Mentoring through the course'],
        ],
        'mbbs-abroad' => [
            'cat' => 'medical', 'icon' => 'globe', 'home' => true,
            'title' => 'MBBS Abroad',
            'text'  => 'End-to-end guidance on recognised universities, eligibility, fees, documentation, visa and travel.',
            'points' => ['NMC / WHO-recognised universities', 'Transparent fee structure', 'Admission letter & visa assistance', 'Pre-departure and on-arrival support'],
        ],
        'other-medical-courses' => [
            'cat' => 'medical', 'icon' => 'heart',
            'title' => 'Other Medical Courses',
            'text'  => 'BDS, nursing, pharmacy, physiotherapy, allied health sciences and other medical programs.',
            'points' => ['BDS, B.Sc Nursing, B.Pharm', 'Physiotherapy & allied health', 'India and abroad options', 'Eligibility & admission support'],
        ],
        'ausbildung-germany' => [
            'cat' => 'abroad', 'icon' => 'briefcase', 'home' => true,
            'title' => 'Ausbildung Programs (Germany)',
            'text'  => 'Paid vocational training in Germany that combines classroom learning with on-the-job experience.',
            'points' => ['Nursing, hospitality, IT & technical trades', 'German language preparation (A1–B2)', 'Employer matching & contracts', 'Visa and relocation guidance'],
        ],
        'online-education' => [
            'cat' => 'skills', 'icon' => 'laptop', 'home' => true,
            'title' => 'Online Education',
            'text'  => 'Flexible online degrees and certifications from recognised institutions, so you can learn while you work.',
            'points' => ['UGC-entitled online degrees', 'Professional certifications', 'Study at your own pace', 'Admission & exam support'],
        ],
        'management-it' => [
            'cat' => 'skills', 'icon' => 'chart',
            'title' => 'Management & IT Courses',
            'text'  => 'Programs in business management, IT and emerging technologies that employers are looking for.',
            'points' => ['BBA / MBA / BCA / MCA', 'Data, cloud & digital marketing', 'Industry-aligned curriculum', 'Placement guidance'],
        ],
        'abroad-studies' => [
            'cat' => 'abroad', 'icon' => 'plane', 'home' => true,
            'title' => 'Abroad Studies (Non-Medical)',
            'text'  => 'Study options in engineering, business, IT, humanities and more across leading global destinations.',
            'points' => ['Country & university shortlisting', 'SOP, LOR and application support', 'Scholarship guidance', 'Student visa assistance'],
        ],
        'skill-development' => [
            'cat' => 'skills', 'icon' => 'tools', 'home' => true,
            'title' => 'Skill Development Programs',
            'text'  => 'Job-oriented training that builds practical, employable skills and confidence.',
            'points' => ['Communication & soft skills', 'Language training', 'Interview preparation', 'Employment-linked programs'],
        ],
    ],

    // "Find your path" helper on the home page: visitors tap a goal and see the matching services.
    // 'services' lists service slugs from above, best match first.
    'goals' => [
        ['icon' => 'stethoscope', 'title' => 'Become a doctor',        'text' => 'MBBS, BDS, nursing and more',       'services' => ['mbbs-abroad', 'medical-education', 'other-medical-courses']],
        ['icon' => 'plane',       'title' => 'Study abroad',           'text' => 'Universities around the world',     'services' => ['abroad-studies', 'mbbs-abroad', 'career-counseling']],
        ['icon' => 'briefcase',   'title' => 'Work & train in Germany','text' => 'Paid Ausbildung programs',          'services' => ['ausbildung-germany', 'skill-development']],
        ['icon' => 'laptop',      'title' => 'Learn while I work',     'text' => 'Online degrees and certificates',   'services' => ['online-education', 'management-it']],
        ['icon' => 'tools',       'title' => 'Get job-ready skills',   'text' => 'Practical, employable training',    'services' => ['skill-development', 'management-it', 'online-education']],
        ['icon' => 'compass',     'title' => "I'm not sure yet",       'text' => 'Talk it through with a counselor',  'services' => ['career-counseling']],
    ],

    'why' => [
        ['icon' => 'globe',  'title' => 'Global opportunities',            'text' => 'Study, training and career pathways across multiple countries and disciplines.'],
        ['icon' => 'grad',   'title' => 'Holistic student development',    'text' => 'Support that covers academics, skills, confidence and career readiness together.'],
        ['icon' => 'users',  'title' => 'Experienced mentors',             'text' => 'Counselors and mentors who know the path and guide you at every stage.'],
        ['icon' => 'link',   'title' => 'Industry & university connections','text' => 'Partnerships that open doors to institutions and employers in India and abroad.'],
        ['icon' => 'book',   'title' => 'Lifelong learning support',       'text' => 'We stay connected beyond admission as your goals and career evolve.'],
        ['icon' => 'shield', 'title' => 'Honest, transparent guidance',    'text' => 'Clear information on eligibility, fees and outcomes, with no hidden surprises.'],
    ],

    'process' => [
        ['title' => 'Free counseling',     'text' => 'Talk to a counselor about your interests, marks, budget and goals.'],
        ['title' => 'Choose your path',    'text' => 'Get a shortlist of courses, countries and institutions that fit you.'],
        ['title' => 'Apply & documents',   'text' => 'We help with applications, documentation, entrance tests and interviews.'],
        ['title' => 'Admission & beyond',  'text' => 'Visa, travel and ongoing support once you start your journey.'],
    ],

    // FAQs grouped by topic. Answers may contain simple HTML.
    'faqs' => [
        'General' => [
            ['Who can approach PERF for guidance?', 'Students from class 10 onwards, graduates, working professionals and parents looking for guidance on courses, careers and studying or working abroad.'],
            ['Is the first counseling session free?', 'Yes. Your first counseling session is free. You can visit our office, call us, or request a session through the enquiry form.'],
            ['Do you offer online counseling?', 'Yes. Counseling can be done over a phone or video call, so you can get guidance from anywhere.'],
        ],
        'Admission process' => [
            ['What is the admission process with PERF?', 'It usually follows four steps: (1) a free counseling session, (2) shortlisting the right course, country and institution, (3) application, documentation and entrance or interview preparation, and (4) admission, visa and travel support.'],
            ['What documents are generally required?', 'Typically mark sheets and certificates (10th, 12th and graduation where relevant), a valid passport for abroad programs, passport-size photographs and entrance scores such as NEET or IELTS where required. We share an exact checklist for your chosen program.'],
            ['How early should I start the process?', 'Ideally 6–12 months before the intake you are targeting. This leaves time for entrance exams, language preparation, applications and visas.'],
        ],
        'Eligibility & fees' => [
            ['What are the eligibility criteria?', 'Eligibility depends on the course and country. For example, MBBS abroad generally requires Physics, Chemistry and Biology in class 12 along with a qualifying NEET score for Indian students. Contact us for the criteria of your chosen program.'],
            ['How much does it cost?', 'Fees vary widely by course, country and institution. During counseling we give you a clear breakdown of tuition, living and other costs so you can plan with confidence.'],
            ['Are scholarships or education loans available?', 'Many universities offer scholarships, and education loans are available from banks. We guide you on options you may be eligible for.'],
        ],
        'Study & work abroad' => [
            ['Is studying abroad safe and worthwhile?', 'With the right choice of country and institution, studying abroad is a safe and valuable experience. We recommend only recognised universities and brief students on safety, living costs and degree recognition before they decide.'],
            ['Will a medical degree from abroad be valid in India?', 'Graduates must meet the National Medical Commission (NMC) requirements, including clearing the licensing exam. We guide students only towards programs that meet current NMC guidelines.'],
            ['What is the Ausbildung program in Germany?', 'Ausbildung is a 2–3.5 year vocational training program in Germany that combines classroom learning with paid on-the-job training. It requires German language skills (usually B1–B2) and can lead to long-term employment in Germany.'],
        ],
    ],
];
