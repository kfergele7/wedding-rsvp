<?php

return [
    'rsvp_settings' => [
        'meal_mode' => 'options',
        'menu_heading' => 'Wedding Menu',
        'menu_intro' => 'We cannot wait to share a beautiful meal with you.',
        'set_menu_description' => 'A chef-curated set menu will be served for all attending guests.',
        'menu_note_title' => 'Dining Notes',
        'menu_note_text' => '<p>If you have dietary requirements, please let us know in the RSVP.</p><p>All tables will include a bottle of red and white wine.</p>',
        'meal_options' => [],
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
                    [
                        'title' => 'Beef Fillet',
                        'description' => 'Grass-fed beef fillet, dauphinoise potatoes, seasonal greens, and red wine jus.',
                    ],
                    [
                        'title' => 'Wild Mushroom Risotto',
                        'description' => 'Creamy arborio risotto with wild mushrooms, parmesan, and truffle oil.',
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
            'heading' => 'The Big Day',
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
        'cta' => [
            'title' => 'Ready to Celebrate With Us?',
            'text' => 'RSVP online in just a few moments using your invitation code.',
            'buttonLabel' => 'Go to RSVP',
        ],
        'theme' => [
            'primary_color' => '#22363A',
            'button_color' => '#22363A',
        ],
        'section_visibility' => [
            'welcome' => true,
            'story' => true,
            'timeline' => true,
            'venue' => true,
            'travel' => true,
            'menu' => true,
            'faqs' => true,
        ],
    ],
];
