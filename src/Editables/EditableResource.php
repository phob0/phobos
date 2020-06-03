<?php


namespace Phobos\Framework\Editables;

use Illuminate\Http\Resources\Json\JsonResource;

class EditableResource extends JsonResource
{
    protected static $forList = false;

    public function emailHash($email)
    {
        return md5(strtolower(trim($email)));
    }

    public function formatSelect($collection) {
        return $collection ? [
            'label' => $collection['name'],
            'value' => $collection['id']
        ] : false;
    }

    /**
     * @param $params
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * no chain method on this one because ::collection() returns a new anonymous thus you don`t inherit properties from you resource because it`s not your resource anymore
     */
    public static function withoutTranslations($params) {
        self::$forList = true;
        return self::collection($params);
    }

    /**
     * @return $this
     *
     * noTranslation() should be called on ::make() method because it returns a new static of the same object which inherits your resource properties
     */
    public function noTranslation() {
        self::$forList = true;
        return $this;
    }
}
