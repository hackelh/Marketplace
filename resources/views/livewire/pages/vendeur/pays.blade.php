@extends('layouts.sidebare')

@section('title', 'Gestion des Pays')
@section('breadcrumb', 'Gestion des Pays')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-0">Gestion des Pays</h1>
            <small class="text-muted">Gérez la liste des pays d'origine</small>
        </div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('form-create-pays').classList.toggle('d-none')">
            <i class="fas fa-plus"></i> Nouveau Pays
        </button>
    </div>

    <!-- Statistiques (style dashboard small-box) -->
    @php
        $dernierPays = \App\Models\Pays::latest('id')->first();
        $distinctOrigines = \App\Models\Tissu::whereNotNull('origine')->distinct('origine')->count('origine');
        $tissusAvecOrigine = \App\Models\Tissu::whereNotNull('origine')->count();
    @endphp
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\Pays::count() }}</h3>
                    <p>Total Pays</p>
                </div>
                <i class="small-box-icon fas fa-globe"></i>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>{{ $distinctOrigines }}</h3>
                    <p>Origines distinctes (tissus)</p>
                </div>
                <i class="small-box-icon fas fa-flag"></i>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>{{ $tissusAvecOrigine }}</h3>
                    <p>Tissus avec origine</p>
                </div>
                <i class="small-box-icon fas fa-box"></i>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h3>{{ $dernierPays ? $dernierPays->nom : '—' }}</h3>
                    <p>Dernier pays ajouté</p>
                </div>
                <i class="small-box-icon fas fa-clock"></i>
            </div>
        </div>
    </div>

    <!-- Formulaire création pays -->
    <div id="form-create-pays" class="card d-none">
        <div class="card-body">
            <form action="{{ route('vendeur.pays.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Nom du pays *</label>
                    <input type="text" name="nom" class="form-control" placeholder="Ex: Bénin" value="{{ old('nom') }}" required>
                    @error('nom')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('form-create-pays').classList.add('d-none')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages flash -->
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Liste des pays -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Liste des Pays</h3>
        </div>
        <div class="card-body">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                @forelse(\App\Models\Pays::orderBy('nom')->get() as $pays)
                    <div class="col">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <h5 class="mb-1">{{ $pays->nom }}</h5>
                                    <small class="text-muted">Créé le {{ $pays->created_at?->format('d/m/Y') }}</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" onclick="toggleEditPays('{{ $pays->id }}')" aria-label="Modifier" title="Modifier" class="btn btn-outline-warning rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('vendeur.pays.delete', $pays->id) }}" method="POST" onsubmit="return confirm('Supprimer ce pays ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Supprimer" title="Supprimer" class="btn btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div id="edit-pays-{{ $pays->id }}" class="mt-3 d-none">
                                <form action="{{ route('vendeur.pays.update', $pays->id) }}" method="POST" class="row g-2">
                                    @csrf
                                    @method('PUT')
                                    <div class="col-12 col-md-8">
                                        <label class="form-label small mb-1">Nom *</label>
                                        <input type="text" name="nom" value="{{ old('nom', $pays->nom) }}" class="form-control" required>
                                    </div>
                                    <div class="col-12 col-md-4 d-flex align-items-end justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary" onclick="toggleEditPays('{{ $pays->id }}')">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">Aucun pays pour le moment.</div>
                @endforelse
            </div>
        </div>
    </div>



@endsection

@push('scripts')
<script>
function toggleEditPays(id) {
    const el = document.getElementById('edit-pays-' + id);
    if (el) el.classList.toggle('d-none');
}
</script>
@endpush