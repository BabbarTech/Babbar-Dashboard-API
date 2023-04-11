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

namespace App\Enums\Traits;

trait HasDropDownOptionTrait
{
    static public function dropdownOptions(): array
    {
        $options = [];
        foreach (self::cases() as $value) {
            $options[] = [
                'label' => $value->label(),
                'value' => $value->name,
            ];
        }

        return $options;
    }

}
