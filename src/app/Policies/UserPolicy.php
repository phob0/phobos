<?php

namespace Phobos\Framework\App\Policies;

use Phobos\Framework\App\Models\PhobosUser;

class UserPolicy
{
    public function before(PhobosUser $user, $ability)
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\PhobosUser $user
     * @param string|null $targetType
     * @param int|null $targetId
     * @return mixed
     */
    public function viewAny(PhobosUser $user, string $targetType, int $targetId)
    {
        return $user->role['target_type'] === $targetType && $user->role['target_id'] === $targetId;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\PhobosUser $user
     * @param \App\PhobosUser $model
     * @return mixed
     */
    public function view(PhobosUser $user, PhobosUser $model)
    {
        return $user->id === $model->id
            || ($user->role['target_type'] === $model->role['target_type']
                && $user->role['target_id'] === $model->role['target_id']
                && !in_array($user->role['name'], ['patient', 'caregiver']));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\PhobosUser $user
     * @param string|null $targetType
     * @param int|null $targetId
     * @return mixed
     */
    public function create(PhobosUser $user, ?string $targetType = 'application', ?int $targetId = 0)
    {
        return $this->viewAny($user, $targetType, $targetId);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\PhobosUser $user
     * @param \App\PhobosUser $model
     * @return mixed
     */
    public function update(PhobosUser $user, PhobosUser $model)
    {
        return $this->view($user, $model);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\PhobosUser $user
     * @param \App\PhobosUser $model
     * @return mixed
     */
    public function delete(PhobosUser $user, PhobosUser $model)
    {
        return $this->view($user, $model);
    }

}
