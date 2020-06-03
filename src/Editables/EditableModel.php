<?php

namespace Phobos\Framework\Editables;

use Illuminate\Database\Eloquent\Model;

class EditableModel extends Model
{
    const STATUS_ACTIVE = 'A';
    const STATUS_INACTIVE = 'I';

    protected $perPage = 10;

    public static function STATUSES()
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ];
    }

    public static function activeStatuses()
    {
        return [
            self::STATUS_ACTIVE,
        ];
    }
}
