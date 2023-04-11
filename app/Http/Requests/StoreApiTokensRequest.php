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

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class StoreApiTokensRequest extends FormRequest
{
    protected Collection $gatewaysConfig;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function getGatewaysConfig(): Collection
    {
        if (! isset($this->gatewaysConfig)) {
            $this->gatewaysConfig = collect((array) config('gateways', []))
                ->keyBy(function ($item, $key) {
                    return 'gateways.' . $key . '.api_token';
                });
        }

        return $this->gatewaysConfig;
    }

    public function rules(): array
    {
        return $this->getGatewaysConfig()
            ->map(function ($provider) {
                /** @var array $provider */
                $isRequired = (bool) Arr::get($provider, 'required', false);

                return [
                    $isRequired ? 'required' : 'nullable',
                    'string',
                    'size:60',
                ];
            })->toArray();
    }

    public function attributes(): array
    {
        return $this->getGatewaysConfig()
            ->map(function ($provider, $key) {
                /** @var array $provider */
                $label = Arr::get($provider, 'label', $key);

                return $label . ' Token';
            })->toArray();
    }
}
