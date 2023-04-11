<?php
/*
 * Babbar Dashboard API
 *
 * Licensed under the MIT license. See LICENSE file in the project root for details.
 *
 * @copyright Copyright (c) 2023 Babbar
 * @license   https://opensource.org/license/mit/ MIT License
 *
 */

namespace App\View\Components\Form;

use Illuminate\Support\Str;
use Illuminate\View\Component;

abstract class AbstractInput extends Component
{
    public string $label;

    public string $name;

    public string $sanitizedName;

    public mixed $value;

    public ?string $help;

    public string $type = 'text';

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        string $name,
        mixed $value = null,
        string $label = null,
        string $help = null,
        string $type = 'text'
    ) {
        $this->name = $name;
        $this->sanitizedName = Str::slug($name);
        $this->value = old($name, $value);
        $this->label = $label ?? Str::ucfirst($name);
        $this->help = $help;
        $this->type = $type;
    }
}
