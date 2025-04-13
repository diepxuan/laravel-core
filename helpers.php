<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2025-04-13 15:38:04
 */

if (!function_exists('maskKey')) {
    /**
     * Mask a key with asterisks, leaving the last $visible characters visible.
     *
     * @param string $key
     * @param int    $visible
     * @param string $mask
     *
     * @return string
     */
    function maskKey($key, $visible = 4, $mask = '*')
    {
        return str_repeat($mask, max(0, strlen($key) - $visible)) . substr($key, -$visible);
    }
}
