<div>
  <div class="card mb-3">
    <div class="card-header d-flex flex-column flex-md-row gap-2 align-items-md-center justify-content-between">
      <div class="d-flex gap-2 flex-wrap">
        <input type="text" class="form-control" style="min-width:260px" placeholder="Rechercher (nom, email)" wire:model.live.debounce.300ms="search">
        <select class="form-select" wire:model.live="role">
          <option value="">Tous les rôles</option>
          <option value="admin">Admin</option>
          <option value="vendeur">Vendeur</option>
          <option value="tailleur">Tailleur</option>
          <option value="client">Client</option>
        </select>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary" wire:click="create"><i class="bi bi-plus-lg me-1"></i> Nouvel utilisateur</button>
      </div>
    </div>
  </div>

  @if($showForm)
    <div class="card mb-3">
      <div class="card-header">
        <strong>{{ $editingId ? 'Modifier' : 'Créer' }} un utilisateur</strong>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Rôle</label>
            <select class="form-select @error('roleField') is-invalid @enderror" wire:model.defer="roleField">
              <option value="admin">Admin</option>
              <option value="vendeur">Vendeur</option>
              <option value="tailleur">Tailleur</option>
              <option value="client">Client</option>
            </select>
            @error('roleField')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Mot de passe {{ $editingId ? '(laisser vide pour ne pas changer)' : '' }}</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model.defer="password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" wire:model.defer="password_confirmation">
          </div>
        </div>
      </div>
      <div class="card-footer d-flex gap-2 justify-content-end">
        <button class="btn btn-secondary" wire:click="$set('showForm', false)">Annuler</button>
        <button class="btn btn-success" wire:click="save"><i class="bi bi-save me-1"></i> Enregistrer</button>
      </div>
    </div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:70px">#</th>
              <th>Nom</th>
              <th>Email</th>
              <th style="width:140px">Rôle</th>
              <th style="width:180px">Inscription</th>
              <th style="width:160px">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $i => $u)
              <tr>
                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $i + 1 }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td><span class="badge text-bg-primary text-uppercase">{{ $u->role }}</span></td>
                <td>{{ $u->created_at?->format('d/m/Y H:i') }}</td>
                <td class="d-flex gap-2">
                  <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $u->id }})"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $u->id }})"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4">Aucun utilisateur trouvé.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
      {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  </div>

  <script>
    document.addEventListener('livewire:init', () => {
      Livewire.on('confirm-delete', ({ id }) => {
        if (confirm('Confirmer la suppression de cet utilisateur ?')) {
          Livewire.dispatch('delete', { id });
        }
      });
      Livewire.on('notify', ({ type, message }) => {
        console.log(`[${type}]`, message);
      });
    });
  </script>
</div>
