<?php

namespace App\Policies;

use App\Models\FlowerType;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use MoonShine\Models\MoonshineUser;

class FlowerTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(MoonshineUser $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(MoonshineUser $user, FlowerType $flowerType): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(MoonshineUser $user): bool
    {
        return $user->moonshine_user_role_id === 1;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(MoonshineUser $user, FlowerType $flowerType): bool
    {
        return $user->moonshine_user_role_id === 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(MoonshineUser $user, FlowerType $flowerType): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(MoonshineUser $user, FlowerType $flowerType): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(MoonshineUser $user, FlowerType $flowerType): bool
    {
        return false;
    }

    public function massDelete(MoonshineUser $user): bool
    {
        return false;
    }
}
