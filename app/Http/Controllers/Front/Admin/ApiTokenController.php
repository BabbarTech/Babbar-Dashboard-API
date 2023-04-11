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

namespace App\Http\Controllers\Front\Admin;

use App\Http\Requests\StoreApiTokensRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;

class ApiTokenController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $userGateways = $request->user()?->gateways()->get()->pluck('maskedApiToken', 'name');
        } catch (DecryptException $e) {
            $userGateways = [];
            $request->session()->flash('danger', __('error.decrypt_exception'));
        }

        $gatewaysCollection = collect((array) config('gateways', []))
            ->map(function ($gateway, $key) use ($userGateways) {
                /** @var array $gateway */
                $gateway['obfuscated_api_token'] = $userGateways[$key] ?? null;
                return $gateway;
            });

        return view('admin.api_tokens', [
            'gatewayCollection' => $gatewaysCollection,
        ]);
    }

    public function store(StoreApiTokensRequest $request): RedirectResponse
    {
        $gateways = (array) $request->validated('gateways');

        foreach ($gateways as $gatewayKey => $params) {
            /** @var array $params */
            $request->user()?->gateways()->updateOrCreate([
                'name' => $gatewayKey
            ], [
                'api_token' => $params['api_token'],
            ]);
        }

        return redirect(RouteServiceProvider::HOME)
            ->withSuccess(__('Api token(s) saved successfully !'));

        //return redirect()->route('admin.api_tokens');
    }
}
