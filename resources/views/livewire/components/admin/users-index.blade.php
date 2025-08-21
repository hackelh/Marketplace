<div>
  <div class="card mb-3">
    <div class="card-header d-flex flex-column flex-md-row gap-2 align-items-md-center justify-content-between">
      <div class="d-flex gap-2 flex-wrap">
        <input type="text" class="form-control" style="min-width:240px" placeholder="Rechercher (nom, email)" wire:model.live.debounce.300ms="search">
        <select class="form-select" wire:model.live="role">
          <option value="">Tous les rôles</option>
          <option value="admin">Admin</option>
          <option value="vendeur">Vendeur</option>
          <option value="tailleur">Tailleur</option>
          <option value="client">Client</option>
        </select>
      </div>
      <div class="mt-2 mt-md-0">
        <button class="btn btn-primary" wire:click="create">
          <i class="bi bi-plus-lg me-1"></i> Ajouter
        </button>
      </div>
    </div>

    @if($showForm)
      <div class="card-body border-top">
        <form wire:submit.prevent="save" class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.live="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model.live="email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          {{-- Rôle supprimé du CRUD: création uniquement en Admin, pas de modification de rôle --}}
          <div class="col-md-4">
            <label class="form-label">Rôle</label>
            <input type="text" class="form-control" value="Admin" disabled>
            <div class="form-text">Les nouveaux utilisateurs créés ici seront des administrateurs. Le rôle ne peut pas être modifié.</div>
          </div>

          <div class="col-md-4">
            <label class="form-label">Mot de passe</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model.live="password" autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" wire:model.live="password_confirmation" autocomplete="new-password">
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check2 me-1"></i> Enregistrer
            </button>
            <button type="button" class="btn btn-outline-secondary" wire:click="$set('showForm', false)">
              Annuler
            </button>
          </div>
        </form>
      </div>
    @endif
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Nom</th>
              <th>Email</th>
              <th style="width:220px">Rôle / Statut</th>
              <th style="width:170px">Créé le</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $u)
              <tr>
                <td class="fw-semibold">{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge text-bg-secondary">{{ $u->role ?? 'client' }}</span>
                    @if($u->is_blocked)
                      <span class="badge text-bg-danger">Bloqué</span>
                    @else
                      <span class="badge text-bg-success">Actif</span>
                    @endif
                  </div>
                </td>
                <td>{{ $u->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-primary" wire:click="edit({{ $u->id }})">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="window.dispatchEvent(new CustomEvent('confirm-delete', { detail: { id: {{ $u->id }} } }))">
                      <i class="bi bi-trash"></i>
                    </button>
                    @if(!$u->is_blocked)
                      <button class="btn btn-outline-warning" wire:click="block({{ $u->id }})" title="Bloquer">
                        <i class="bi bi-slash-circle"></i>
                      </button>
                    @else
                      <button class="btn btn-outline-success" wire:click="unblock({{ $u->id }})" title="Débloquer">
                        <i class="bi bi-unlock"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4">Aucun utilisateur trouvé.</td>
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
    // Confirmation suppression (réceptionne l'event Livewire dispatch('confirm-delete', {id}))
    window.addEventListener('confirm-delete', (e) => {
      const id = e.detail?.id;
      if (!id) return;
      if (confirm('Supprimer cet utilisateur ?')) {
        // Appeler l'action Livewire
        const component = document.querySelector('[wire\:id]')?.__livewire;
        if (component) component.call('delete', id);
        else window.Livewire?.all()[0]?.call('delete', id);
      }
    });
  </script>
</div>
