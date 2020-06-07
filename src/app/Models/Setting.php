<?php

namespace Phobos\Framework\App\Models;

use Phobos\Framework\Editables\EditableModel;

class Setting extends EditableModel
{
    protected $fillable = ['setting', 'setting_type', 'value', 'secured'];

    protected $casts = [
        'history' => 'array',
        'secured' => 'boolean'
    ];
}
