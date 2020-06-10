<?php


namespace Phobos\Framework\App\Traits;


use App\UserRole;

trait HandlesUserRoles
{
    public function __construct(array $attributes = [])
    {
        $this->with[] = 'roles';

        parent::__construct($attributes);
    }

    public function roles()
    {
        return $this->hasMany(UserRole::class, 'user_id');
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
                'name' => $role->role
            ];
        }

        return [
            'name' => 'user',
        ];
    }
}
