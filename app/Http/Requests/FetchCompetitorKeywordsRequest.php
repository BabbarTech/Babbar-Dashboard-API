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

namespace App\Http\Requests;

use App\Rules\Hostname;
use Illuminate\Foundation\Http\FormRequest;

class FetchCompetitorKeywordsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'hostname' => [
                'required',
                new Hostname(),
            ],
            'type' => 'required|in:normal,full',
        ];
    }

    protected function prepareForValidation()
    {
        $hostname = parse_url(strval($this->hostname), PHP_URL_HOST) ?? $this->hostname;

        $this->merge([
            'hostname' => $hostname,
        ]);
    }
}
