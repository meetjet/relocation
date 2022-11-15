<?php

namespace App\Traits;

use Illuminate\Support\HtmlString;
use JetBrains\PhpStorm\Pure;

trait PageListHelpers
{
    /**
     * @param string $href
     * @param string $title
     * @return HtmlString
     */
    #[Pure] public static function externalLink(string $href, string $title): HtmlString
    {
        return new HtmlString('<a class="transition hover:underline hover:text-gray-400 focus:underline focus:text-gray-400" href="' . $href . '" target="_blank" onclick="window.event.stopPropagation();">'
            . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="inline mr-1 w-3 h-3 text-primary-700">'
            . '<path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5z" clip-rule="evenodd" />'
            . '<path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.194a.75.75 0 00-.053 1.06z" clip-rule="evenodd" />'
            . '</svg>' . $title . '</a>');
    }

    /**
     * @param string $href
     * @param string $title
     * @return HtmlString
     */
    #[Pure] public static function link(string $href, string $title): HtmlString
    {
        return new HtmlString('<a class="transition hover:underline hover:text-gray-400 focus:underline focus:text-gray-400" href="' . $href . '" onclick="window.event.stopPropagation();">' . $title . '</a>');
    }
}
