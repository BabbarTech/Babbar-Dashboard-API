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

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class EncryptedAndMasked implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return Crypt::decryptString($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (empty($value)) {
            return null;
        }

        $currentEncryptedValue = $attributes[$key] ?? null;

        if ($currentEncryptedValue) {
            $decryptedValue = $this->decryptValue($currentEncryptedValue);

            if ($value === $decryptedValue) {
                return $currentEncryptedValue;
            }

            // Do not crypt value if it is masked version
            if ($value === $this->maskDecrypted($decryptedValue ?? '')) {
                return $currentEncryptedValue;
            }
        }

        return Crypt::encryptString($value);
    }


    public static function mask(?string $encryptedValue): ?string
    {
        if (empty($encryptedValue)) {
            return null;
        }

        $decryptedApiToken = Crypt::decryptString($encryptedValue);

        return (new self())->maskDecrypted($decryptedApiToken);
    }

    protected function maskDecrypted(string $decryptedApiToken): ?string
    {
        $apiTokenLength = strlen($decryptedApiToken);
        $obfuscationStart = (int) floor($apiTokenLength / 4);
        $obfuscationLength = ($apiTokenLength - $obfuscationStart - 4);

        return Str::mask($decryptedApiToken, '*', $obfuscationStart, $obfuscationLength);
    }

    protected function decryptValue(string $encryptedValue): ?string
    {
        try {
            $decryptedValue = Crypt::decryptString($encryptedValue);
        } catch (DecryptException $e) {
            $decryptedValue = null;
        }

        return $decryptedValue;
    }
}
