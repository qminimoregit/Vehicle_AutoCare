<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BranchContext
{
    /**
     * Get the active branch ID for the current user and session.
     */
    public static function getActiveBranchId(): ?int
    {
        $user = Auth::user();
        if (! $user) {
            return static::getDefaultMainBranchId();
        }

        $sessionBranchId = session('active_branch_id');

        // Check if session branch exists and user has permission
        if ($sessionBranchId && $user->canAccessBranch($sessionBranchId)) {
            return (int) $sessionBranchId;
        }

        // Fallback to first accessible branch
        $firstAccessible = $user->getAccessibleBranches()->first();
        if ($firstAccessible) {
            session(['active_branch_id' => $firstAccessible->id]);
            return (int) $firstAccessible->id;
        }

        // Fallback to main branch
        $mainId = static::getDefaultMainBranchId();
        if ($mainId) {
            session(['active_branch_id' => $mainId]);
            return $mainId;
        }

        return null;
    }

    /**
     * Get the active Branch model instance.
     */
    public static function getActiveBranch(): ?Branch
    {
        $id = static::getActiveBranchId();
        if (! $id) {
            return null;
        }

        return Branch::with('manager')->find($id);
    }

    /**
     * Set the active branch ID in session if authorized.
     */
    public static function setActiveBranchId(int $branchId, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        if (! $user) {
            return false;
        }

        if (! $user->canAccessBranch($branchId)) {
            return false;
        }

        session(['active_branch_id' => $branchId]);
        return true;
    }

    /**
     * Get accessible branches for current user.
     *
     * @return Collection<int, Branch>
     */
    public static function getAccessibleBranches(?User $user = null): Collection
    {
        $user = $user ?? Auth::user();
        if (! $user) {
            return collect();
        }

        return $user->getAccessibleBranches();
    }

    /**
     * Check if user has more than 1 accessible branch to switch between.
     */
    public static function shouldShowBranchSelector(?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        if (! $user) {
            return false;
        }

        return static::getAccessibleBranches($user)->count() > 1;
    }

    /**
     * Get the ID of the default main branch.
     */
    protected static function getDefaultMainBranchId(): ?int
    {
        return Branch::where('is_main_branch', true)->where('is_active', true)->value('id')
            ?? Branch::where('is_active', true)->value('id');
    }
}
