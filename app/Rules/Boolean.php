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

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Boolean implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $acceptable = [true, false, 0, 1, '0', '1', 'true', 'false'];

        return in_array($value, $acceptable, true);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        /** @var string $defaultMessage */
        $defaultMessage = __('validation.boolean');

        return $defaultMessage;
    }
}
