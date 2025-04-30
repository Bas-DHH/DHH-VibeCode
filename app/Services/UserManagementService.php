<?php

namespace App\Services;

use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    public function createUser(array $data, Business $business): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'business_id' => $business->id,
        ]);
    }

    public function updateUser(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'role' => $data['role'] ?? $user->role,
        ]);

        if (isset($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
    }

    public function getUsersByBusiness(Business $business): Collection
    {
        return User::where('business_id', $business->id)
            ->orderBy('name')
            ->get();
    }

    public function getUsersByRole(Business $business, string $role): Collection
    {
        return User::where('business_id', $business->id)
            ->where('role', $role)
            ->orderBy('name')
            ->get();
    }

    public function getActiveUsers(Business $business): Collection
    {
        return User::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getInactiveUsers(Business $business): Collection
    {
        return User::where('business_id', $business->id)
            ->where('is_active', false)
            ->orderBy('name')
            ->get();
    }

    public function toggleUserStatus(User $user): User
    {
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return $user;
    }

    public function updateUserRole(User $user, string $role): User
    {
        $user->update([
            'role' => $role,
        ]);

        return $user;
    }

    public function getAvailableRoles(): array
    {
        return [
            'admin' => __('Administrator'),
            'manager' => __('Manager'),
            'user' => __('User'),
        ];
    }

    public function validateUserData(array $data): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:admin,manager,user',
            'password' => 'required|string|min:8|confirmed',
        ];

        if (isset($data['id'])) {
            $rules['email'] = 'required|string|email|max:255|unique:users,email,' . $data['id'];
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }
} 