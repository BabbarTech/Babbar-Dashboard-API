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

class Select extends AbstractInput
{
    public array $options = [];

    public function __construct(
        string $name,
        array $options,
        mixed $value = null,
        string $label = null,
        string $help = null,
        string $type = 'text'
    ) {
        parent::__construct($name, $value, $label, $help, $type);
        $this->options = $options;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.select');
    }
}
