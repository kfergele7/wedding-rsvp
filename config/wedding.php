<?php

return [
    'colour_palettes' => [
        'magic_classic' => [
            'name' => 'Magic Classic',
            'mood' => 'Elegant, premium, timeless',
            'colours' => [
                'primary' => '#22363A',
                'secondary' => '#466369',
                'dark' => '#0F1B1D',
                'soft_background' => '#F7F5F2',
                'light' => '#FFFFFF',
            ],
        ],
        'rose_veil' => [
            'name' => 'Rose Veil',
            'mood' => 'Soft, romantic, feminine',
            'colours' => [
                'primary' => '#B9A3AA',
                'secondary' => '#967984',
                'dark' => '#4A363C',
                'soft_background' => '#EFE7E1',
                'light' => '#FAF8F5',
            ],
        ],
        'terracotta_bloom' => [
            'name' => 'Terracotta Bloom',
            'mood' => 'Warm, rustic, Mediterranean',
            'colours' => [
                'primary' => '#A9634D',
                'secondary' => '#D19A7C',
                'dark' => '#4A2C24',
                'soft_background' => '#EFE0D4',
                'light' => '#FAF4EF',
            ],
        ],
        'sage_garden' => [
            'name' => 'Sage Garden',
            'mood' => 'Natural, calm, countryside',
            'colours' => [
                'primary' => '#617465',
                'secondary' => '#9AAA8E',
                'dark' => '#2F3A32',
                'soft_background' => '#E8E4D8',
                'light' => '#FFFFFF',
            ],
        ],
        'lavender_haze' => [
            'name' => 'Lavender Haze',
            'mood' => 'Soft, elegant, modern romantic',
            'colours' => [
                'primary' => '#8E7A9B',
                'secondary' => '#B7A6C4',
                'dark' => '#3D3345',
                'soft_background' => '#EFEAF2',
                'light' => '#FFFFFF',
            ],
        ],
        'dusky_blue' => [
            'name' => 'Dusky Blue',
            'mood' => 'Cool, calm, sophisticated',
            'colours' => [
                'primary' => '#3D5361',
                'secondary' => '#7892A0',
                'dark' => '#1F2B33',
                'soft_background' => '#DDE6E8',
                'light' => '#F4F7F7',
            ],
        ],
        'champagne_silk' => [
            'name' => 'Champagne Silk',
            'mood' => 'Warm, elegant, softly luxurious',
            'colours' => [
                'primary' => '#6F6252',
                'secondary' => '#C8B58A',
                'dark' => '#2A241D',
                'soft_background' => '#F1E8D8',
                'light' => '#FFFFFF',
            ],
        ],
        'black_tie' => [
            'name' => 'Black Tie',
            'mood' => 'Formal, dramatic, evening',
            'colours' => [
                'primary' => '#111111',
                'secondary' => '#3A3A3A',
                'dark' => '#000000',
                'soft_background' => '#FFFFFF',
                'light' => '#F7F4ED',
            ],
        ],
    ],
    'rsvp_settings' => [
        'meal_mode' => 'set_menu',
        'menu_heading' => 'Wedding Menu',
        'menu_intro' => 'We cannot wait to share a beautiful meal with you.',
        'set_menu_description' => 'A chef-curated set menu will be served for all attending guests.',
        'menu_note_title' => 'Dining Notes',
        'menu_note_text' => '<p>If you have dietary requirements, please let us know in the RSVP.</p><p>All tables will include a bottle of red and white wine.</p>',
        'meal_options' => [],
        'kids_menu_enabled' => false,
        'kids_menu_items' => [],
        'menu_courses' => [
            [
                'id' => 'starter',
                'name' => 'Starter',
                'items' => [
                    [
                        'title' => 'Heirloom Tomato Tart',
                        'description' => 'Roasted heirloom tomatoes, whipped feta, and basil oil on crisp puff pastry.',
                    ],
                ],
            ],
            [
                'id' => 'main',
                'name' => 'Main',
                'items' => [
                    [
                        'title' => 'Seabass',
                        'description' => 'Pan seared seabass served on a bed of whipped mash with tender-stem broccoli and a white wine cream sauce.',
                    ],
                ],
            ],
            [
                'id' => 'dessert',
                'name' => 'Dessert',
                'items' => [
                    [
                        'title' => 'Lemon Posset',
                        'description' => 'Silky lemon posset with shortbread crumble and fresh berries.',
                    ],
                ],
            ],
        ],
    ],
    'homepage_content' => [
        'hero' => [
            'kicker' => "We're Getting Married",
            'names' => 'Sabrina & Kevin',
            'dateLine' => 'September 12, 2026',
            'locationLine' => 'Willow Creek Estate, City, State',
            'buttonLabel' => 'RSVP Soon',
            'image' => '/images/wedding/hero-couple.svg',
            'imageFocusX' => 50,
            'imageFocusY' => 50,
        ],
        'welcome' => [
            'heading' => 'Dear Family & Friends',
            'letter' => "We're deeply grateful to celebrate this day with the people who have shaped our lives. This website will share all wedding details as the date gets closer, and we can’t wait to make lasting memories together.",
            'signoff' => 'Sabrina & Kevin',
            'image' => '/images/wedding/welcome-couple.svg',
            'imageFocusX' => 50,
            'imageFocusY' => 50,
        ],
        'timeline' => [
            'heading' => 'Wedding Timeline',
            'dateAccent' => 'September 12, 2026',
            'items' => [
                ['time' => '3:30 PM', 'title' => 'Ceremony', 'description' => 'Join us as we say “I do.”'],
                ['time' => '4:00 PM', 'title' => 'Photos', 'description' => 'Family portraits and couple photos.'],
                ['time' => '5:00 PM', 'title' => 'Cocktails', 'description' => 'Sip, mingle, and toast with us.'],
                ['time' => '6:00 PM', 'title' => 'Dinner', 'description' => 'Dinner and dancing to follow.'],
            ],
        ],
        'story' => [
            'heading' => 'Our Story',
            'accent' => 'March 2016',
            'text' => 'Our paths crossed at a mutual friend’s gathering, and what began as a simple hello became years of laughter, support, and adventure. Through every season of life, we have built a home in each other. We are so excited for this next chapter and thankful to share it with everyone we love.',
            'image' => '/images/wedding/story-couple.svg',
            'imageFocusX' => 50,
            'imageFocusY' => 50,
        ],
        'details' => [
            'venue' => [
                'name' => 'Willow Creek Estate',
                'address' => '1024 Garden Lane, City, State 12345',
                'blurb' => 'Ceremony and reception will both be held onsite. Please arrive 20 minutes early for seating.',
            ],
            'travel' => 'For guests traveling in, we recommend staying near Downtown City. A room block is reserved at The Magnolia House and Riverfront Hotel through May 20, 2027.',
            'faqs' => [
                ['question' => 'Is there a dress code?', 'answer' => 'Formal attire requested. Think cocktail dresses and suits.'],
                ['question' => 'Can I bring a plus one?', 'answer' => 'Your invitation indicates the number of seats reserved for your party.'],
                ['question' => 'Are children invited?', 'answer' => 'We adore your little ones, but this will be an adults-focused evening.'],
            ],
            'image' => '/images/wedding/venue-map.svg',
            'imageFocusX' => 50,
            'imageFocusY' => 50,
        ],
        'countdown' => [
            'targetDateTime' => '2026-09-12T15:30',
        ],
        'guest_list' => [
            'responseDeadline' => '2026-08-15',
        ],
        'gallery' => [
            'heading' => "Photo's of us across the years",
            'items' => [],
        ],
        'cta' => [
            'title' => 'Ready to Celebrate With Us?',
            'text' => 'RSVP online in just a few moments using your invitation code.',
            'buttonLabel' => 'Go to RSVP',
        ],
        'theme' => [
            'palette' => 'magic_classic',
            'primary_color' => '#22363A',
            'button_color' => '#22363A',
            'layout' => 'classic',
        ],
        'section_visibility' => [
            'welcome' => true,
            'story' => true,
            'timeline' => true,
            'venue' => true,
            'travel' => true,
            'menu' => true,
            'faqs' => true,
            'countdown' => true,
            'gallery' => true,
        ],
    ],
    'admin_field_help_texts' => [
        'theme.colour_palette' => [
            'label' => 'Website Title and Theme Selection - Colour Palette',
            'default' => 'Choose a professionally designed colour palette. The website automatically handles button, section and text contrast.',
        ],
        'hero.couple_names' => [
            'label' => 'Hero - Couple Names',
            'default' => 'Example: Kyle & Nicole. This displays as the hero headline.',
        ],
        'hero.kicker' => [
            'label' => 'Hero - Hero Kicker',
            'default' => 'Example: We are getting married. Keep this short and warm.',
        ],
        'hero.wedding_date' => [
            'label' => 'Hero - Wedding Date',
            'default' => 'Example: 12 September 2026.',
        ],
        'hero.location_line' => [
            'label' => 'Hero - Location Line',
            'default' => 'Example: Ayrshire, Scotland.',
        ],
        'hero.rsvp_button_label' => [
            'label' => 'Hero - RSVP Button Label',
            'default' => 'Example: RSVP Now.',
        ],
        'hero.upload_image' => [
            'label' => 'Hero - Upload Hero Image',
            'default' => 'Upload your main hero photo (JPG/PNG/WEBP/SVG).',
        ],
        'hero.focus_x' => [
            'label' => 'Hero - Image Horizontal Focus Point',
            'default' => 'Set horizontal focal point. 50% keeps the centre in view.',
        ],
        'hero.focus_y' => [
            'label' => 'Hero - Image Vertical Focus Point',
            'default' => 'Set vertical focal point. 50% keeps the centre in view.',
        ],
        'welcome.heading' => [
            'label' => 'Welcome - Welcome Heading',
            'default' => 'Example: Dear Family & Friends.',
        ],
        'welcome.signoff' => [
            'label' => 'Welcome - Welcome Signoff',
            'default' => 'Example: Kyle & Nicole.',
        ],
        'welcome.letter' => [
            'label' => 'Welcome - Welcome Letter',
            'default' => 'Example: We are thrilled to celebrate with you on our wedding day.',
        ],
        'welcome.upload_image' => [
            'label' => 'Welcome - Upload Welcome Image',
            'default' => 'Upload the image that appears beside the welcome letter.',
        ],
        'welcome.focus_x' => [
            'label' => 'Welcome - Image Horizontal Focus Point',
            'default' => 'Adjust horizontal crop focus for the welcome image.',
        ],
        'welcome.focus_y' => [
            'label' => 'Welcome - Image Vertical Focus Point',
            'default' => 'Adjust vertical crop focus for the welcome image.',
        ],
        'timeline.heading' => [
            'label' => 'Timeline - Heading',
            'default' => 'Example: Wedding Timeline.',
        ],
        'timeline.accent' => [
            'label' => 'Timeline - Accent',
            'default' => 'Example: Saturday, 12 September 2026.',
        ],
        'timeline.time' => [
            'label' => 'Timeline Item - Time',
            'default' => 'Example: 3:30 PM.',
        ],
        'timeline.event' => [
            'label' => 'Timeline Item - Event',
            'default' => 'Example: Ceremony.',
        ],
        'timeline.description' => [
            'label' => 'Timeline Item - Description',
            'default' => 'Example: Join us as we say “I do.”',
        ],
        'story.heading' => [
            'label' => 'Our Story - Heading',
            'default' => 'Example: Our Story.',
        ],
        'story.accent' => [
            'label' => 'Our Story - Accent',
            'default' => 'Example: March 2016.',
        ],
        'story.text' => [
            'label' => 'Our Story - Text',
            'default' => 'Share a short story of how you met and your journey together.',
        ],
        'story.upload_image' => [
            'label' => 'Our Story - Upload Image',
            'default' => 'Upload the photo shown beside your story.',
        ],
        'story.focus_x' => [
            'label' => 'Our Story - Image Horizontal Focus Point',
            'default' => 'Adjust horizontal crop focus for the story image.',
        ],
        'story.focus_y' => [
            'label' => 'Our Story - Image Vertical Focus Point',
            'default' => 'Adjust vertical crop focus for the story image.',
        ],
        'details.venue_name' => [
            'label' => 'Venue - Venue Name',
            'default' => 'Example: Lochgreen House Hotel.',
        ],
        'details.venue_address' => [
            'label' => 'Venue - Venue Address',
            'default' => 'Example: Monktonhill Rd, Troon KA10 7EN.',
        ],
        'details.venue_blurb' => [
            'label' => 'Venue - Venue Information',
            'default' => 'Example: Ceremony and reception are both onsite.',
        ],
        'details.upload_image' => [
            'label' => 'Venue & Travel - Upload Venue Image',
            'default' => 'Upload the image shown alongside venue/travel details.',
        ],
        'details.focus_x' => [
            'label' => 'Venue & Travel - Venue Image Horizontal Focus Point',
            'default' => 'Adjust horizontal crop focus for the venue image.',
        ],
        'details.focus_y' => [
            'label' => 'Venue & Travel - Venue Image Vertical Focus Point',
            'default' => 'Adjust vertical crop focus for the venue image.',
        ],
        'details.travel_info' => [
            'label' => 'Travel - Travel Information',
            'default' => 'Example: Nearby hotels and transport details for out-of-town guests.',
        ],
        'menu.section_heading' => [
            'label' => 'Menu - Section Heading',
            'default' => 'Example: Wedding Menu.',
        ],
        'menu.meal_type' => [
            'label' => 'Menu - RSVP Meal Type',
            'default' => 'Choose between set menu and guest meal selections.',
        ],
        'menu.intro_text' => [
            'label' => 'Menu - Intro Text',
            'default' => 'Short intro above menu cards. Example: We cannot wait to share this meal with you.',
        ],
        'menu.notes_title' => [
            'label' => 'Menu - Notes Card Title',
            'default' => 'Example: Dining Notes.',
        ],
        'menu.notes_text' => [
            'label' => 'Menu - Notes Card Text',
            'default' => 'Example: Please include allergies and dietary needs in your RSVP.',
        ],
        'menu.course_name' => [
            'label' => 'Menu Course - Course Name',
            'default' => 'Example: Starter, Main, Dessert.',
        ],
        'menu.dish_title' => [
            'label' => 'Menu Course - Dish Title',
            'default' => 'Example: Pan Seared Seabass.',
        ],
        'menu.dish_description' => [
            'label' => 'Menu Course - Dish Description',
            'default' => 'Example: Served with whipped mash and tender-stem broccoli.',
        ],
        'menu.set_menu_description' => [
            'label' => 'Menu - Set Menu Description',
            'default' => 'Shown when set menu mode is enabled. Example: A chef-curated menu will be served.',
        ],
        'faq.question' => [
            'label' => 'FAQ - Question',
            'default' => 'Example: Is there parking at the venue?',
        ],
        'faq.answer' => [
            'label' => 'FAQ - Answer',
            'default' => 'Example: Yes, there is free onsite parking available.',
        ],
        'gallery.heading' => [
            'label' => 'Photo Gallery - Heading',
            'default' => "Example: Photo's of us across the years.",
        ],
        'gallery.upload_image' => [
            'label' => 'Photo Gallery - Upload Image',
            'default' => 'Upload a square-friendly photo for the gallery. Add at least 2 and up to 8 images.',
        ],
        'gallery.focus_x' => [
            'label' => 'Photo Gallery - Image Horizontal Focus Point',
            'default' => 'Adjust horizontal crop focus for this gallery image.',
        ],
        'gallery.focus_y' => [
            'label' => 'Photo Gallery - Image Vertical Focus Point',
            'default' => 'Adjust vertical crop focus for this gallery image.',
        ],
        'rsvp.title' => [
            'label' => 'Final RSVP Request - Title',
            'default' => 'Example: Ready to celebrate with us?',
        ],
        'rsvp.button_label' => [
            'label' => 'Final RSVP Request - Button Label',
            'default' => 'Example: Go to RSVP.',
        ],
        'rsvp.text' => [
            'label' => 'Final RSVP Request - Text',
            'default' => 'Example: Please RSVP using your invitation code.',
        ],
    ],
];
