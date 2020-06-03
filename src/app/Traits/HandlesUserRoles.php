<?php


namespace Phobos\Framework\app\Traits;


use App\Models\UserRole;

trait HandlesUserRoles
{
    public function __construct(array $attributes = [])
    {
        $this->with[] = 'roles';

        parent::__construct($attributes);
    }

    public function roles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function isSuperAdmin()
    {
        return $this->roles->first(function($item) { return $item->role === 'superadmin'; });
    }

    /** this will be deprecated */
    public function isAdmin()
    {
        return $this->roles->first(function($item) { return $item->role === 'admin'; });
    }
    /**/

    public function isOrganizer()
    {
        return $this->roles->first(function($item) { return $item->role === 'organizer'; });
    }

    public function isGuide()
    {
        return $this->roles->first(function($item) { return $item->role === 'guide'; });
    }

    public function getLocation() {
        return !$this->isSuperAdmin() ? $this->roles->first()->target : false;
    }

    public function getRoleAttribute()
    {
        $role = $this->isSuperAdmin() ?: ($this->isOrganizer() ?: $this->isGuide());
        if ($role) {
            return [
                'name' => $role->role,
                'location_id' => $role->target_type === 'location' ? $role->target_id : 0
            ];
        }

        return [
            'name' => 'user',
        ];
    }
}
