<?php

return [
    'fallback' => "Sorry, I don't understand the command.",
    'exception' => "<b>Whoops!\nSomething went wrong!</b>",
    'default' => [
        'start' => [
            'description' => "Restart bot",
            'welcome' => "Relocation Digital Bot welcomes you.",
            'manual' => "To ask a question, type the \:command command or click on the corresponding menu button."
        ],
        'question' => [
            'description' => "Ask a question",
            'start' => "Please write your question.",
            'unsupported' => "This message format is not yet supported.",
            'end' => "Thanks for your question! You will be notified as soon as a response is received. To submit another question, type the /:command command. To see other people's questions, follow the link - :link",
            'reply' => "You got the answer to the question asked earlier! To view it, follow the link: :link",
        ],
    ],
    'armenia' => [
        'start' => [
            'description' => "Restart bot",
            'welcome' => "Relocation Digital Bot Armenia welcomes you.",
            'manual' => "To add an announcement, type the \:command command or click on the corresponding menu button.\n\nTo see other people's announcements, follow the link - :link"
        ],
        'question' => [
            'description' => "Ask a question",
            'start' => "Please write your question.",
            'unsupported' => "This message format is not yet supported.",
            'end' => "Thanks for your question! You will be notified as soon as a response is received. To submit another question, type the /:command command. To see other people's questions, follow the link - :link",
            'reply' => "You got the answer to the question asked earlier! To view it, follow the link: :link",
        ],
        'listing-add' => [
            'description' => "To add an announcement",
            'start' => "To add an announcement, you need to follow a few simple steps.",
            'ask-location' => "Please choose a location:",
            'ask-location-error' => "Error, you must select a location from the list.",
            'ask-location-chosen' => "Chosen location: \":location\".",
            'ask-category' => "Please choose a category:",
            'ask-category-error' => "Error, you must select a category from the list.",
            'ask-category-chosen' => "Chosen category: \":category\".",
            'ask-title' => "Please write the title of the announcement:",
            'ask-title-error' => "Error, title must be in <b>text format</b>.",
            'ask-description' => "Please write the text of the announcement:",
            'ask-description-error' => "Error, announcement text must be in <b>text format</b>.",
            'ask-picture' => "Please add one picture:",
            'ask-picture-error' => "Error, only images in the following format are allowed: <b>:mime_types</b>.",
            'ask-picture-more' => "Picture added successfully. Would you like to add another picture?",
            'ask-price' => "Please write the price (Armenian dram):",
            'ask-price-error' => "Error, price must be a <b>positive integer</b>.",
            'ask-contact' => "Your Telegram profile is hidden by privacy settings. Add a contact phone number or email where you could be contacted:",
            'ask-contact-error-format' => "Error, contact must be in <b>text format</b>.",
            'ask-contact-error-value' => "Error, contact must be <b>a phone number or email</b>.",
            'announcement-preview' => "<b>Your announcement</b>\n\n<i>Location:</i> :location\n\n<i>Category:</i> :category\n\n<i>Title:</i> :title\n\n<i>Text:</i> :description\n\n<i>Price (Armenian dram):</i> :price\n\n<i>Added images:</i> :images\n\n<i>Contact:</i> :contact",
            'ask-confirmation' => "Add this announcement?",
            'confirmation-successful' => "Thank you, your announcement has been successfully added. You will be notified as soon as it is published. To see other people's announcements, follow the link - :link",
            'confirmation-canceled' => "You canceled adding an announcement.",
            'published' => "Your announcement has been posted! To view it, follow the link: :link",
            'rejected' => "Your announcement has been rejected.",
            'send-to-channel' => ":text\n\n&#128176; Price: :price\n\n&#128681; Location: :location\n\n&#128172; Contact: :contact\n\n:link",
        ],
        'event-add' => [
            'send-to-channel' => "&#128197; Date and time: :datetime\n\n:text\n\n&#128681; Address: :address\n\n&#128176; Price: :price\n\n&#128172; Organizer: :organizer\n\n:link",
        ],
    ],
    'georgia' => [
        'start' => [
            'description' => "Restart bot",
            'welcome' => "Relocation Digital Bot Georgia welcomes you.",
            'manual' => "To add an announcement, type the \:command command or click on the corresponding menu button.\n\nTo see other people's announcements, follow the link - :link"
        ],
        'question' => [
            'description' => "Ask a question",
            'start' => "Please write your question.",
            'unsupported' => "This message format is not yet supported.",
            'end' => "Thanks for your question! You will be notified as soon as a response is received. To submit another question, type the /:command command. To see other people's questions, follow the link - :link",
            'reply' => "You got the answer to the question asked earlier! To view it, follow the link: :link",
        ],
        'listing-add' => [
            'description' => "To add an announcement",
            'start' => "To add an announcement, you need to follow a few simple steps.",
            'ask-location' => "Please choose a location:",
            'ask-location-error' => "Error, you must select a location from the list.",
            'ask-location-chosen' => "Chosen location: \":location\".",
            'ask-category' => "Please choose a category:",
            'ask-category-error' => "Error, you must select a category from the list.",
            'ask-category-chosen' => "Chosen category: \":category\".",
            'ask-title' => "Please write the title of the announcement:",
            'ask-title-error' => "Error, title must be in <b>text format</b>.",
            'ask-description' => "Please write the text of the announcement:",
            'ask-description-error' => "Error, announcement text must be in <b>text format</b>.",
            'ask-picture' => "Please add one picture:",
            'ask-picture-error' => "Error, only images in the following format are allowed: <b>:mime_types</b>.",
            'ask-picture-more' => "Picture added successfully. Would you like to add another picture?",
            'ask-price' => "Please write the price (lari):",
            'ask-price-error' => "Error, price must be a <b>positive integer</b>.",
            'ask-contact' => "Your Telegram profile is hidden by privacy settings. Add a contact phone number or email where you could be contacted:",
            'ask-contact-error-format' => "Error, contact must be in <b>text format</b>.",
            'ask-contact-error-value' => "Error, contact must be <b>a phone number or email</b>.",
            'announcement-preview' => "<b>Your announcement</b>\n\n<i>Location:</i> :location\n\n<i>Category:</i> :category\n\n<i>Title:</i> :title\n\n<i>Text:</i> :description\n\n<i>Price (lari):</i> :price\n\n<i>Added images:</i> :images\n\n<i>Contact:</i> :contact",
            'ask-confirmation' => "Add this announcement?",
            'confirmation-successful' => "Thank you, your announcement has been successfully added. You will be notified as soon as it is published. To see other people's announcements, follow the link - :link",
            'confirmation-canceled' => "You canceled adding an announcement.",
            'published' => "Your announcement has been posted! To view it, follow the link: :link",
            'rejected' => "Your announcement has been rejected.",
            'send-to-channel' => ":text\n\n&#128176; Price: :price\n\n&#128681; Location: :location\n\n&#128172; Contact: :contact\n\n:link",
        ],
        'event-add' => [
            'send-to-channel' => "&#128197; Date and time: :datetime\n\n:text\n\n&#128681; Address: :address\n\n&#128176; Price: :price\n\n&#128172; Organizer: :organizer\n\n:link",
        ],
    ],
    'lithuania' => [
        'start' => [
            'description' => "Restart bot",
            'welcome' => "Relocation Digital Bot Lithuania welcomes you.",
            'manual' => "To add an announcement, type the \:command command or click on the corresponding menu button.\n\nTo see other people's announcements, follow the link - :link"
        ],
        'question' => [
            'description' => "Ask a question",
            'start' => "Please write your question.",
            'unsupported' => "This message format is not yet supported.",
            'end' => "Thanks for your question! You will be notified as soon as a response is received. To submit another question, type the /:command command. To see other people's questions, follow the link - :link",
            'reply' => "You got the answer to the question asked earlier! To view it, follow the link: :link",
        ],
        'listing-add' => [
            'description' => "To add an announcement",
            'start' => "To add an announcement, you need to follow a few simple steps.",
            'ask-location' => "Please choose a location:",
            'ask-location-error' => "Error, you must select a location from the list.",
            'ask-location-chosen' => "Chosen location: \":location\".",
            'ask-category' => "Please choose a category:",
            'ask-category-error' => "Error, you must select a category from the list.",
            'ask-category-chosen' => "Chosen category: \":category\".",
            'ask-title' => "Please write the title of the announcement:",
            'ask-title-error' => "Error, title must be in <b>text format</b>.",
            'ask-description' => "Please write the text of the announcement:",
            'ask-description-error' => "Error, announcement text must be in <b>text format</b>.",
            'ask-picture' => "Please add one picture:",
            'ask-picture-error' => "Error, only images in the following format are allowed: <b>:mime_types</b>.",
            'ask-picture-more' => "Picture added successfully. Would you like to add another picture?",
            'ask-price' => "Please write the price (euro):",
            'ask-price-error' => "Error, price must be a <b>positive integer</b>.",
            'ask-contact' => "Your Telegram profile is hidden by privacy settings. Add a contact phone number or email where you could be contacted:",
            'ask-contact-error-format' => "Error, contact must be in <b>text format</b>.",
            'ask-contact-error-value' => "Error, contact must be <b>a phone number or email</b>.",
            'announcement-preview' => "<b>Your announcement</b>\n\n<i>Location:</i> :location\n\n<i>Category:</i> :category\n\n<i>Title:</i> :title\n\n<i>Text:</i> :description\n\n<i>Price (euro):</i> :price\n\n<i>Added images:</i> :images\n\n<i>Contact:</i> :contact",
            'ask-confirmation' => "Add this announcement?",
            'confirmation-successful' => "Thank you, your announcement has been successfully added. You will be notified as soon as it is published. To see other people's announcements, follow the link - :link",
            'confirmation-canceled' => "You canceled adding an announcement.",
            'published' => "Your announcement has been posted! To view it, follow the link: :link",
            'rejected' => "Your announcement has been rejected.",
            'send-to-channel' => ":text\n\n&#128176; Price: :price\n\n&#128681; Location: :location\n\n&#128172; Contact: :contact\n\n:link",
        ],
        'event-add' => [
            'send-to-channel' => "&#128197; Date and time: :datetime\n\n:text\n\n&#128681; Address: :address\n\n&#128176; Price: :price\n\n&#128172; Organizer: :organizer\n\n:link",
        ],
    ],
    'thailand' => [
        'start' => [
            'description' => "Restart bot",
            'welcome' => "Relocation Digital Bot Thailand welcomes you.",
            'manual' => "To add an announcement, type the \:command command or click on the corresponding menu button.\n\nTo see other people's announcements, follow the link - :link"
        ],
        'question' => [
            'description' => "Ask a question",
            'start' => "Please write your question.",
            'unsupported' => "This message format is not yet supported.",
            'end' => "Thanks for your question! You will be notified as soon as a response is received. To submit another question, type the /:command command. To see other people's questions, follow the link - :link",
            'reply' => "You got the answer to the question asked earlier! To view it, follow the link: :link",
        ],
        'listing-add' => [
            'description' => "To add an announcement",
            'start' => "To add an announcement, you need to follow a few simple steps.",
            'ask-location' => "Please choose a location:",
            'ask-location-error' => "Error, you must select a location from the list.",
            'ask-location-chosen' => "Chosen location: \":location\".",
            'ask-category' => "Please choose a category:",
            'ask-category-error' => "Error, you must select a category from the list.",
            'ask-category-chosen' => "Chosen category: \":category\".",
            'ask-title' => "Please write the title of the announcement:",
            'ask-title-error' => "Error, title must be in <b>text format</b>.",
            'ask-description' => "Please write the text of the announcement:",
            'ask-description-error' => "Error, announcement text must be in <b>text format</b>.",
            'ask-picture' => "Please add one picture:",
            'ask-picture-error' => "Error, only images in the following format are allowed: <b>:mime_types</b>.",
            'ask-picture-more' => "Picture added successfully. Would you like to add another picture?",
            'ask-price' => "Please write the price (baht):",
            'ask-price-error' => "Error, price must be a <b>positive integer</b>.",
            'ask-contact' => "Your Telegram profile is hidden by privacy settings. Add a contact phone number or email where you could be contacted:",
            'ask-contact-error-format' => "Error, contact must be in <b>text format</b>.",
            'ask-contact-error-value' => "Error, contact must be <b>a phone number or email</b>.",
            'announcement-preview' => "<b>Your announcement</b>\n\n<i>Location:</i> :location\n\n<i>Location:</i> :location\n\n<i>Category:</i> :category\n\n<i>Title:</i> :title\n\n<i>Text:</i> :description\n\n<i>Price (baht):</i> :price\n\n<i>Added images:</i> :images\n\n<i>Contact:</i> :contact",
            'ask-confirmation' => "Add this announcement?",
            'confirmation-successful' => "Thank you, your announcement has been successfully added. You will be notified as soon as it is published. To see other people's announcements, follow the link - :link",
            'confirmation-canceled' => "You canceled adding an announcement.",
            'published' => "Your announcement has been posted! To view it, follow the link: :link",
            'rejected' => "Your announcement has been rejected.",
            'send-to-channel' => ":text\n\n&#128176; Price: :price\n\n&#128681; Location: :location\n\n&#128172; Contact: :contact\n\n:link",
        ],
        'event-add' => [
            'send-to-channel' => "&#128197; Date and time: :datetime\n\n:text\n\n&#128681; Address: :address\n\n&#128176; Price: :price\n\n&#128172; Organizer: :organizer\n\n:link",
        ],
    ],
    'turkey' => [
        'start' => [
            'description' => "Restart bot",
            'welcome' => "Relocation Digital Bot Turkey welcomes you.",
            'manual' => "To add an announcement, type the \:command command or click on the corresponding menu button.\n\nTo see other people's announcements, follow the link - :link"
        ],
        'question' => [
            'description' => "Ask a question",
            'start' => "Please write your question.",
            'unsupported' => "This message format is not yet supported.",
            'end' => "Thanks for your question! You will be notified as soon as a response is received. To submit another question, type the /:command command. To see other people's questions, follow the link - :link",
            'reply' => "You got the answer to the question asked earlier! To view it, follow the link: :link",
        ],
        'listing-add' => [
            'description' => "To add an announcement",
            'start' => "To add an announcement, you need to follow a few simple steps.",
            'ask-location' => "Please choose a location:",
            'ask-location-error' => "Error, you must select a location from the list.",
            'ask-location-chosen' => "Chosen location: \":location\".",
            'ask-category' => "Please choose a category:",
            'ask-category-error' => "Error, you must select a category from the list.",
            'ask-category-chosen' => "Chosen category: \":category\".",
            'ask-title' => "Please write the title of the announcement:",
            'ask-title-error' => "Error, title must be in <b>text format</b>.",
            'ask-description' => "Please write the text of the announcement:",
            'ask-description-error' => "Error, announcement text must be in <b>text format</b>.",
            'ask-picture' => "Please add one picture:",
            'ask-picture-error' => "Error, only images in the following format are allowed: <b>:mime_types</b>.",
            'ask-picture-more' => "Picture added successfully. Would you like to add another picture?",
            'ask-price' => "Please write the price (Turkish lira):",
            'ask-price-error' => "Error, price must be a <b>positive integer</b>.",
            'ask-contact' => "Your Telegram profile is hidden by privacy settings. Add a contact phone number or email where you could be contacted:",
            'ask-contact-error-format' => "Error, contact must be in <b>text format</b>.",
            'ask-contact-error-value' => "Error, contact must be <b>a phone number or email</b>.",
            'announcement-preview' => "<b>Your announcement</b>\n\n<i>Location:</i> :location\n\n<i>Category:</i> :category\n\n<i>Title:</i> :title\n\n<i>Text:</i> :description\n\n<i>Price (Turkish lira):</i> :price\n\n<i>Added images:</i> :images\n\n<i>Contact:</i> :contact",
            'ask-confirmation' => "Add this announcement?",
            'confirmation-successful' => "Thank you, your announcement has been successfully added. You will be notified as soon as it is published. To see other people's announcements, follow the link - :link",
            'confirmation-canceled' => "You canceled adding an announcement.",
            'published' => "Your announcement has been posted! To view it, follow the link: :link",
            'rejected' => "Your announcement has been rejected.",
            'send-to-channel' => ":text\n\n&#128176; Price: :price\n\n&#128681; Location: :location\n\n&#128172; Contact: :contact\n\n:link",
        ],
        'event-add' => [
            'send-to-channel' => "&#128197; Date and time: :datetime\n\n:text\n\n&#128681; Address: :address\n\n&#128176; Price: :price\n\n&#128172; Organizer: :organizer\n\n:link",
        ],
    ],
];
