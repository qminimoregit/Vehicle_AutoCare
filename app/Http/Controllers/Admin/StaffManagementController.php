<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StaffManagementController extends Controller
{
    /**
     * Display a listing of system users (staff and administrators).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $role = $request->input('role');
        $status = $request->input('status');

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (in_array($role, ['admin', 'staff'])) {
            $query->where('role', $role);
        }

        if (in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.staff.index', compact('users', 'search', 'role', 'status'));
    }

    /**
     * Show the form for creating a new staff account.
     */
    public function create(): View
    {
        return view('admin.staff.create');
    }

    /**
     * Store a newly created staff account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:admin,staff'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['required', 'string', Password::min(8)],
        ], [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already in use by another account.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        ActivityLog::log(
            action: 'created',
            description: "Created new user account: {$user->name} ({$user->email}, Role: {$user->role}).",
            module: 'Staff Management'
        );

        return redirect()->route('admin.staff.index')
            ->with('success', "Account for '{$user->name}' created successfully.");
    }

    /**
     * Show the form for editing an existing staff account.
     */
    public function edit(User $staff): View
    {
        return view('admin.staff.edit', compact('staff'));
    }

    /**
     * Update an existing staff account.
     */
    public function update(Request $request, User $staff): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:admin,staff'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'string', Password::min(8)],
        ], [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Prevent self-demotion or self-deactivation
        if ($staff->id === Auth::id()) {
            if ($validated['role'] !== 'admin') {
                return back()->withErrors(['role' => 'You cannot remove your own administrator privileges.']);
            }
            if ($validated['status'] !== 'active') {
                return back()->withErrors(['status' => 'You cannot deactivate your own account.']);
            }
        }

        $staff->update($validated);

        ActivityLog::log(
            action: 'updated',
            description: "Updated user account: {$staff->name} ({$staff->email}).",
            module: 'Staff Management'
        );

        return redirect()->route('admin.staff.index')
            ->with('success', "Account for '{$staff->name}' updated successfully.");
    }

    /**
     * Toggle user active/inactive status.
     */
    public function toggleStatus(User $staff): RedirectResponse
    {
        if ($staff->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own logged-in account.']);
        }

        $newStatus = $staff->status === 'active' ? 'inactive' : 'active';
        $staff->update(['status' => $newStatus]);

        ActivityLog::log(
            action: 'status_changed',
            description: "Changed user status for {$staff->name} to {$newStatus}.",
            module: 'Staff Management'
        );

        $message = $newStatus === 'active'
            ? "User '{$staff->name}' has been activated."
            : "User '{$staff->name}' has been deactivated.";

        return back()->with('success', $message);
    }
}
