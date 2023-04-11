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

namespace App\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    public ?string $title;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $title = null)
    {
        if (!empty($title)) {
            $title .= ' - ' . config('app.name');
        }

        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('layouts.app');
    }
}
