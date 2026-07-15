<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

/** Управление пользователями панели доступно только роли admin. */
final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        // Себя удалить нельзя.
        return $user->isAdmin() && ! $user->is($model);
    }
}
