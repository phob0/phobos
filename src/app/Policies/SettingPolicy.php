<?php

namespace Phobos\Framework\App\Policies;

use Phobos\Framework\App\Models\Setting;
use App\PhobosUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
{
    use HandlesAuthorization;

    public function before(PhobosUser $user, $ability)
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any settings.
     *
     * @param  \App\PhobosUser  $user
     * @return mixed
     */
    public function viewAny(PhobosUser $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the setting.
     *
     * @param  \App\PhobosUser  $user
     * @param  \App\Setting  $setting
     * @return mixed
     */
    public function view(PhobosUser $user, Setting $setting)
    {
        return $setting->user_id == $user->id;
    }

    /**
     * Determine whether the user can create settings.
     *
     * @param  \App\PhobosUser  $user
     * @return mixed
     */
    public function create(PhobosUser $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the setting.
     *
     * @param  \App\PhobosUser  $user
     * @param  \App\Setting  $setting
     * @return mixed
     */
    public function update(PhobosUser $user, Setting $setting)
    {
        return $setting->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the setting.
     *
     * @param  \App\PhobosUser  $user
     * @param  \App\Setting  $setting
     * @return mixed
     */
    public function delete(PhobosUser $user, Setting $setting)
    {
        return false;
    }

}
