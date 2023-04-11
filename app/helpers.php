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

if (! function_exists('get_domain')) {
    function get_domain(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host)) {
            return null;
        }

        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,63})$/i', $host, $regs)) {
            return $regs['domain'];
        }

        return null;
    }
}

if (! function_exists('___')) {
    function ___(string $key = null, array $replace = [], string $locale = null): string
    {
        $translation = __($key, $replace, $locale);

        if (! is_string($translation)) {
            throw new \Exception("Translation '{$key}' must be a string");
        }

        return $translation;
    }
}
