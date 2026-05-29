<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Authorize that the user is an admin.
     */
    protected function authorizeAdmin(): void
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Admin access required.');
        }
    }

    /**
     * Check if current user is admin/staff or the requested user (for borrowers viewing their own profile).
     */
    protected function authorizeBorrowerOrSelf($targetUserId): void
    {
        if (auth()->id() !== $targetUserId && auth()->user()?->isBorrower()) {
            abort(403, 'You can only view your own profile.');
        }
    }
}
