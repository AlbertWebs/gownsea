<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'user' => new User(['role' => 'sales_rep', 'status' => 'active']),
            'roles' => array_keys(config('admin.roles')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        $user = User::query()->create($data);
        AuditLog::record($request->user(), 'user.created', $user);

        return redirect()->route('admin.users.index')->with('status', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', [
            'user' => $user,
            'roles' => array_keys(config('admin.roles')),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);

        return back()->with('status', 'User updated successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'unique:users,email,'.($user?->id ?: 'NULL')],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', 'in:'.implode(',', array_keys(config('admin.roles')))],
            'status' => ['required', 'in:active,disabled'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
        ]);
    }
}
