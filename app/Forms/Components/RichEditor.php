<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Concerns;
use Filament\Forms\Components\Contracts;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class RichEditor extends Field implements Contracts\CanBeLengthConstrained, Contracts\HasFileAttachments
{
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasFileAttachments;
    use Concerns\HasPlaceholder;
    use Concerns\InteractsWithToolbarButtons;
    use HasExtraAlpineAttributes;

    protected string $view = 'forms.components.rich-editor';

    protected array | Closure $toolbarButtons = [
        'attachFiles',
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'h2',
        'h3',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'undo',
        'moreSeparator',
    ];
}
