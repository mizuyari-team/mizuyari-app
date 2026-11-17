<?php

namespace App\Policies;

use App\Models\Plant;
use App\Models\User;

class PlantPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Plant $plant): bool
    {
        return $this->owns($user, $plant);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Plant $plant): bool
    {
        return $this->owns($user, $plant);
    }

    public function delete(User $user, Plant $plant): bool
    {
        return $this->owns($user, $plant);
    }

    // 農園化するときは、この1か所だけ直す
    private function owns(User $user, Plant $plant): bool
    {
        return $user->id === $plant->user_id;
    }
}
