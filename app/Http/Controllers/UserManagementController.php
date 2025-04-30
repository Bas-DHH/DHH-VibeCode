<?php

namespace App\Http\Controllers;

use App\Services\UserManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function __construct(
        private readonly UserManagementService $userManagementService
    ) {}

    public function index(Request $request)
    {
        $business = $request->user()->business;
        $filter = $request->input('filter', 'all');

        $users = match ($filter) {
            'active' => $this->userManagementService->getActiveUsers($business),
            'inactive' => $this->userManagementService->getInactiveUsers($business),
            'role' => $this->userManagementService->getUsersByRole($business, $request->input('role')),
            default => $this->userManagementService->getUsersByBusiness($business),
        };

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['filter', 'role']),
            'roles' => $this->userManagementService->getAvailableRoles(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => $this->userManagementService->getAvailableRoles(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->userManagementService->validateUserData($request->all())
        );

        $this->userManagementService->createUser($validated, $request->user()->business);

        return redirect()->route('users.index')
            ->with('success', __('User created successfully.'));
    }

    public function edit(string $id)
    {
        $user = Auth::user()->business->users()->findOrFail($id);

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => $this->userManagementService->getAvailableRoles(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = Auth::user()->business->users()->findOrFail($id);

        $validated = $request->validate(
            $this->userManagementService->validateUserData([...$request->all(), 'id' => $id])
        );

        $this->userManagementService->updateUser($user, $validated);

        return redirect()->route('users.index')
            ->with('success', __('User updated successfully.'));
    }

    public function destroy(string $id)
    {
        $user = Auth::user()->business->users()->findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', __('You cannot delete your own account.'));
        }

        $this->userManagementService->deleteUser($user);

        return redirect()->route('users.index')
            ->with('success', __('User deleted successfully.'));
    }

    public function toggleStatus(string $id)
    {
        $user = Auth::user()->business->users()->findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', __('You cannot change your own status.'));
        }

        $this->userManagementService->toggleUserStatus($user);

        return redirect()->back()
            ->with('success', __('User status updated successfully.'));
    }

    public function updateRole(Request $request, string $id)
    {
        $user = Auth::user()->business->users()->findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', __('You cannot change your own role.'));
        }

        $validated = $request->validate([
            'role' => 'required|string|in:admin,manager,user',
        ]);

        $this->userManagementService->updateUserRole($user, $validated['role']);

        return redirect()->back()
            ->with('success', __('User role updated successfully.'));
    }
} 