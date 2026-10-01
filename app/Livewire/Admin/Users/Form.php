<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

/**
 * Guards against self-lockout: a super-admin can't strip their own
 * super-admin role or deactivate their own account through this form —
 * there'd be no one left with users.manage to undo it (radix:make-admin
 * still exists as a CLI escape hatch, but the UI shouldn't invite the
 * mistake).
 */
#[Layout('layouts::admin')]
#[Title('User')]
class Form extends Component
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $role = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $is_active = true;

    public function mount(?User $user = null): void
    {
        $this->authorize('users.manage');

        if ($user) {
            $this->user = $user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->roles->first()?->name ?? '';
            $this->is_active = (bool) $user->is_active;
        }
    }

    public function save(): void
    {
        $this->authorize('users.manage');

        $isSelf = $this->user && $this->user->id === auth()->id();

        if ($isSelf && $this->role !== 'super-admin') {
            $this->addError('role', "You can't remove your own super-admin role.");

            return;
        }

        if ($isSelf && ! $this->is_active) {
            $this->addError('is_active', "You can't deactivate your own account.");

            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user?->id)],
            'role' => ['required', Rule::in(Role::pluck('name'))],
            'password' => [$this->user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['boolean'],
        ]);

        $user = $this->user ?? new User;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_active = $validated['is_active'];

        if ($validated['password']) {
            $user->password = $validated['password'];
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        session()->flash('success', 'User saved.');

        $this->redirect(route('admin.users.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.users.form', [
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }
}
