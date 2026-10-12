<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->ensureAdministrator($request);

        $users = User::where('type', 'user')->with('roles.permissions')->orderBy('name')->get();
        $roles = Role::with('permissions')->withCount('users')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.users', compact('users', 'roles', 'permissions'));
    }

    public function storeUser(Request $request)
    {
        $this->ensureAdministrator($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);
        $this->ensureAssignableRole((int) $data['role_id']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => 'user',
            'active_session_id' => $request->user()->active_session_id,
            // Kept empty so the old per-user access model cannot override roles.
            'permissions' => '',
        ]);
        $user->roles()->sync([$data['role_id']]);

        return back()->with('success', 'User created and role assigned successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $this->ensureAdministrator($request);
        abort_if($user->isAdmin() || $user->isSuperAdmin(), 403, 'Super Administrator accounts cannot be modified from the user directory.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'new_password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);
        $this->ensureAssignableRole((int) $data['role_id']);

        $updates = ['name' => $data['name'], 'email' => $data['email'], 'permissions' => ''];
        if (!empty($data['new_password'])) {
            $updates['password'] = Hash::make($data['new_password']);
        }
        $user->update($updates);
        $user->roles()->sync([$data['role_id']]);

        return back()->with('success', 'User and role updated successfully.');
    }

    public function destroyUser(Request $request, User $user)
    {
        $this->ensureAdministrator($request);
        abort_if($user->isAdmin() || $user->isSuperAdmin(), 403, 'Super Administrator accounts cannot be deleted.');

        if ($request->user()->id === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User removed successfully.');
    }

    public function storeRole(Request $request)
    {
        $this->ensureAdministrator($request);
        $data = $this->validateRole($request);
        $role = Role::create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
        ]);
        $role->permissions()->sync($data['permissions']);

        return back()->with('success', 'Role created successfully.');
    }

    public function updateRole(Request $request, Role $role)
    {
        $this->ensureAdministrator($request);
        abort_if($role->slug === 'administrator', 403, 'The Administrator role cannot be edited here.');
        $data = $this->validateRole($request, $role);
        $role->update([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name'], $role),
            'description' => $data['description'] ?? null,
        ]);
        $role->permissions()->sync($data['permissions']);

        return back()->with('success', 'Role updated successfully.');
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('roles', 'name')->ignore($role?->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);
    }

    private function uniqueSlug(string $name, ?Role $except = null): string
    {
        $base = Str::slug($name) ?: 'role';
        $slug = $base;
        $number = 2;
        while (Role::where('slug', $slug)->when($except, fn ($query) => $query->where('id', '!=', $except->id))->exists()) {
            $slug = $base . '-' . $number++;
        }
        return $slug;
    }

    private function ensureAdministrator(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }

    private function ensureAssignableRole(int $roleId): void
    {
        if (Role::whereKey($roleId)->value('slug') === 'administrator') {
            throw ValidationException::withMessages([
                'role_id' => 'The Administrator role is reserved for administrator accounts.',
            ]);
        }
    }
}
