<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Authorize that the user is admin or staff.
     */
    protected function authorizeAdmin(): void
    {
        if (auth()->user()?->isBorrower()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Check if user is admin/staff, throw 403 if not.
     */
    protected function requireAdminOrStaff(): void
    {
        $this->authorizeAdmin();
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
