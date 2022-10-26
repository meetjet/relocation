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
    'armenian' => [
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
        'listing-add' => [
            'description' => "To add an announcement",
            'ask-title' => "Please write the title of the announcement.",
            'ask-image' => "Would you like to attach image?",
            'ask-more-image' => "Would you like to attach another image?",
            'attach-image' => "Please attach one image. To cancel write \":command\".",
            'attach-image-successfully' => "The image have been successfully attached: \"<b>:image</b>\".",
            'attach-image-canceled' => "Image attachment has been cancelled.",
            'end' => "Your announcement has been successfully added.",
        ],
    ],
];
