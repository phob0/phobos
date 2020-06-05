<?php

namespace Phobos\Framework\App\Resources;

use Phobos\Framework\Editables\EditableResource;



class SettingResource extends EditableResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'setting' => $this->setting,
            'value' => $this->value,
            'history' => $this->history,
            'secured' => $this->secured,
            'setting_type' => $this->setting_type,
            'updated_at' => $this->updated_at->toDateString(),
        ];
    }
}
