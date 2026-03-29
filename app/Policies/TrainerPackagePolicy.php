<?php

namespace App\Policies;

use App\Models\Trainer;
use App\Models\TrainerPackage;
use App\Models\User;

class TrainerPackagePolicy
{
    /**
     * Only the trainer who owns the package can modify it.
     */
    private function ownsPackage(User $user, TrainerPackage $trainerPackage): bool
    {
        return $user->trainerProfile?->id === $trainerPackage->trainer_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->isTrainer();
    }

    public function view(User $user, TrainerPackage $trainerPackage): bool
    {
        return $this->ownsPackage($user, $trainerPackage);
    }

    public function create(User $user): bool
    {
        return $user->isTrainer();
    }

    public function update(User $user, TrainerPackage $trainerPackage): bool
    {
        return $this->ownsPackage($user, $trainerPackage);
    }

    public function delete(User $user, TrainerPackage $trainerPackage): bool
    {
        // Cannot delete a package that has active or confirmed bookings
        return $this->ownsPackage($user, $trainerPackage)
            && ! $trainerPackage->bookings()
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();
    }

    public function toggleActive(User $user, TrainerPackage $trainerPackage): bool
    {
        return $this->ownsPackage($user, $trainerPackage);
    }
}
