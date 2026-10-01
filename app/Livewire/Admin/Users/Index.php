<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Users')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function mount(): void
    {
        $this->authorize('users.manage');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('users.manage');

        $user = User::findOrFail($id);

        if ($user->id === auth()->id() && $user->is_active) {
            $this->addError('self', "You can't deactivate your own account.");

            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
    }

    public function render()
    {
        $users = User::query()
            ->with('roles')
            ->when($this->search !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.users.index', ['users' => $users]);
    }
}
