<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role = '';
    public int $perPage = 10;

    // Form fields
    public ?int $editingId = null;
    #[Validate('required|string|min:3')]
    public string $name = '';
    #[Validate('required|email')]
    public string $email = '';
    // Conservé pour compat mais non modifiable par le CRUD (création toujours en admin)
    public string $roleField = 'admin';
    #[Validate('nullable|min:6|same:password_confirmation')]
    public ?string $password = null;
    public ?string $password_confirmation = null;

    public bool $showForm = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'role' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        // Force le rôle à admin pour toute création
        $this->roleField = 'admin';
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        // Ne pas exposer/éditer le rôle via le formulaire
        $this->password = null;
        $this->password_confirmation = null;
        $this->showForm = true;
    }

    public function save(): void
    {
        // Vérif admin
        abort_unless(auth()->user()?->isAdmin(), 403);

        if ($this->editingId) {
            // Update sans modification de rôle
            $this->validate([
                'name' => 'required|string|min:3',
                'email' => 'required|email|unique:users,email,' . $this->editingId,
                'password' => 'nullable|min:6|same:password_confirmation',
            ]);

            $user = User::findOrFail($this->editingId);
            $user->name = $this->name;
            $user->email = $this->email;
            // $user->role reste inchangé
            if ($this->password) {
                $user->password = Hash::make($this->password);
            }
            $user->save();
        } else {
            // Create: toujours en admin
            $this->validate([
                'name' => 'required|string|min:3',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|same:password_confirmation',
            ]);

            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'role' => 'admin',
                'password' => Hash::make($this->password),
            ]);
        }

        $this->dispatch('notify', type: 'success', message: 'Utilisateur enregistré');
        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        if (auth()->id() === $id) {
            $this->dispatch('notify', type: 'warning', message: 'Action refusée: vous ne pouvez pas vous supprimer.');
            return;
        }
        $user = User::findOrFail($id);
        $user->delete();
        $this->dispatch('notify', type: 'success', message: 'Utilisateur supprimé');
        $this->resetPage();
    }

    public function block(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        if (auth()->id() === $id) {
            $this->dispatch('notify', type: 'warning', message: 'Action refusée: vous ne pouvez pas vous bloquer.');
            return;
        }
        $user = User::findOrFail($id);
        $user->is_blocked = true;
        $user->save();
        $this->dispatch('notify', type: 'success', message: 'Utilisateur bloqué');
        $this->resetPage();
    }

    public function unblock(int $id): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $user = User::findOrFail($id);
        $user->is_blocked = false;
        $user->save();
        $this->dispatch('notify', type: 'success', message: 'Utilisateur débloqué');
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->roleField = 'admin';
        $this->password = null;
        $this->password_confirmation = null;
    }

    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $query = User::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }
        if ($this->role !== '') {
            $query->where('role', $this->role);
        }

        $users = $query->orderByDesc('created_at')->paginate($this->perPage);

        return view('livewire.components.admin.users-index', [
            'users' => $users,
        ]);
    }
}
