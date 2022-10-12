<?php

return [
    'bot' => [
        'start' => [
            'description' => "Restart bot.",
            'welcome' => "Relocation Digital Bot welcomes you.",
            'manual' => "To ask a question, type the \:command command or click on the corresponding menu button."
        ],
        'question' => [
            'description' => "Ask a question.",
            'start' => "Please write your question.",
            'unsupported' => "This message format is not yet supported.",
            'end' => "Thanks for your question! You will be notified as soon as a response is received. To submit another question, type the /:command command. To see other people's questions, follow the link - :link",
        ],
        'fallback' => "Sorry, I don't understand the command.",
        'exception' => "<b>Whoops!\nSomething went wrong!</b>",
    ],
];
