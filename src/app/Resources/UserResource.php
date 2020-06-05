<?php


namespace Phobos\Framework\App\Resources;

use App\Models\PhobosUser;
use Phobos\Framework\Editables\EditableResource;


class UserResource extends EditableResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $ret = [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->first_name . ' ' . $this->last_name,
            'phone' => $this->phone,
            'role' => $this->role,
            'email_hash' => $this->emailHash($this->email),
        ];

        return $ret;
    }
}
