<?php


namespace Phobos\Framework\App\Traits;


trait OutputsConsoleData
{
    public function error($string, $verbosity = null)
    {
        parent::error(date_format(date_create(), 'Y-m-d H:i:s.v') . ' ' . $string, $verbosity);
    }

    public function warn($string, $verbosity = null)
    {
        parent::warn(date_format(date_create(), 'Y-m-d H:i:s.v') . ' ' . $string, $verbosity);
    }

    public function info($string, $verbosity = null)
    {
        parent::info(date_format(date_create(), 'Y-m-d H:i:s.v') . ' ' . $string, $verbosity);
    }
}
