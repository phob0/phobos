<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @param string $time
 * @param string $tz
 * @return \Illuminate\Support\Carbon
 */
function carbon($time = 'now', $tz = null)
{
    try {
        return new \Illuminate\Support\Carbon($time, $tz);
    } catch (Exception $e) {
        report($e);
        return null;
    }
}

/**
 * Dumps one or more variables to the log
 *
 * @param mixed ...$vars
 */
function ldump(...$vars)
{
    foreach ($vars as $var) {
        app('log')->debug(print_r($var, true));
    }
}

function cdd(...$vars)
{
    ini_set('xdebug.overload_var_dump', 'off');

    foreach ($vars as $v) {
        var_dump($v);
    }
    $content = ob_get_clean();

    $response = response($content, 500);

    abort($response);
}
