@extends('layouts.adminlte')

@section('title', 'Gestion des Tissus')
@section('breadcrumb')
    <li class="breadcrumb-item active">Gestion des Tissus</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Barre de recherche et filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un tissu...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="categoryFilter" class="form-select">
                                <option value="">Toutes les catégories</option>
                                @foreach(\App\Models\Categorie::all() as $categorie)
                                    <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="priceFilter" class="form-select">
                                <option value="">Tous les prix</option>
                                <option value="0-1000">0 - 1 000 CFA</option>
                                <option value="1000-5000">1 000 - 5 000 CFA</option>
                                <option value="5000-10000">5 000 - 10 000 CFA</option>
                                <option value="10000+">10 000+ CFA</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button id="resetFilters" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\Tissu::where('disponible', true)->where('stock', '>', 0)->count() }}</h3>
                    <p>Tissus Disponibles</p>
                </div>
                <div class="icon">
                    <i class="bi bi-box"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\Categorie::count() }}</h3>
                    <p>Catégories</p>
                </div>
                <div class="icon">
                    <i class="bi bi-tags"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\Tissu::where('stock', '>', 0)->sum('stock') }}</h3>
                    <p>Stock Total</p>
                </div>
                <div class="icon">
                    <i class="bi bi-archive"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format(\App\Models\Tissu::where('stock', '>', 0)->sum(\DB::raw('prix * stock')), 0) }}</h3>
                    <p>Valeur Stock (CFA)</p>
                </div>
                <div class="icon">
                    <i class="bi bi-currency-exchange"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des tissus -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestion des Tissus</h3>
                    <div class="card-tools">
                        <a href="{{ route('vendeur.ajouter-tissu') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Ajouter un Tissu
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tissusTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Couleur</th>
                                    <th>Disponible</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Tissu::with('categorie')->where('user_id', auth()->id())->get() as $tissu)
                                <tr class="tissu-row" 
                                    data-category="{{ $tissu->categorie_id }}" 
                                    data-price="{{ $tissu->prix }}"
                                    data-name="{{ strtolower($tissu->nom) }}">
                                    <td>{{ $tissu->id }}</td>
                                    <td>
                                        @if($tissu->image)
                                            <img src="{{ asset('storage/' . $tissu->image) }}" alt="{{ $tissu->nom }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $tissu->nom }}</td>
                                    <td>
                                        @if($tissu->categorie)
                                            <span class="badge" style="background-color: {{ $tissu->categorie->couleur_hex ?? '#007bff' }}; color: white;">
                                                {{ $tissu->categorie->nom }}
                                            </span>
                                        @else
                                            <span class="text-muted">Aucune catégorie</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($tissu->prix, 2) }} CFA</td>
                                    <td>
                                        @if($tissu->stock > 0)
                                            <span class="badge bg-success">{{ $tissu->stock }}</span>
                                        @else
                                            <span class="badge bg-danger">Rupture</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $tissu->couleur }}; color: white;">
                                            {{ $tissu->couleur }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($tissu->disponible)
                                            <span class="badge bg-success">Disponible</span>
                                        @else
                                            <span class="badge bg-warning">Indisponible</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('vendeur.tissu.show', $tissu->id) }}" class="btn btn-sm btn-info" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('vendeur.tissu.edit', $tissu->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" title="Supprimer" onclick="deleteTissu({{ $tissu->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary" title="Ajouter des métrages" onclick="openMetreModal({{ $tissu->id }}, '{{ $tissu->nom }}')">
                                                <i class="bi bi-plus-circle"></i> Mètres
                                            </button>
                                            <a href="{{ route('vendeur.historique-tissu', $tissu->id) }}" class="btn btn-sm btn-info" title="Voir l'historique">
                                                <i class="bi bi-clock-history"></i> Historique
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="py-4">
                                            <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2">Aucun tissu trouvé</p>
                                            <a href="{{ route('vendeur.ajouter-tissu') }}" class="btn btn-primary">
                                                <i class="bi bi-plus"></i> Ajouter votre premier tissu
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale d'ajout de métrages -->
    <div class="modal fade" id="metreModal" tabindex="-1" aria-labelledby="metreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="metreModalLabel">Ajouter des métrages</h5>
                    <button type="button" class="btn-close" onclick="closeMetreModal()" aria-label="Close"></button>
                </div>
                <form id="addMetreForm" method="POST" action="">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="tissu_id" id="metreTissuId">
                        <div class="mb-3">
                            <label for="metreTissuNom" class="form-label">Tissu</label>
                            <input type="text" class="form-control" id="metreTissuNom" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="metreAjout" class="form-label">Nombre de mètres à ajouter</label>
                            <input type="number" class="form-control" name="metre_ajout" id="metreAjout" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeMetreModal()">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Filtrage et recherche pour le tableau des tissus
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceFilter = document.getElementById('priceFilter');
    const resetFilters = document.getElementById('resetFilters');
    const tissuRows = document.querySelectorAll('.tissu-row');

    function filterTissus() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedPrice = priceFilter.value;

        tissuRows.forEach(row => {
            let show = true;

            // Filtre par recherche
            if (searchTerm) {
                const name = row.dataset.name;
                if (!name.includes(searchTerm)) {
                    show = false;
                }
            }

            // Filtre par catégorie
            if (selectedCategory && row.dataset.category !== selectedCategory) {
                show = false;
            }

            // Filtre par prix
            if (selectedPrice) {
                const price = parseInt(row.dataset.price);
                const [min, max] = selectedPrice.split('-').map(p => p === '+' ? Infinity : parseInt(p));
                
                if (selectedPrice === '10000+') {
                    if (price < 10000) show = false;
                } else {
                    if (price < min || price > max) show = false;
                }
            }

            row.style.display = show ? '' : 'none';
        });
    }

    // Événements
    searchInput.addEventListener('input', filterTissus);
    categoryFilter.addEventListener('change', filterTissus);
    priceFilter.addEventListener('change', filterTissus);
    
    resetFilters.addEventListener('click', function() {
        searchInput.value = '';
        categoryFilter.value = '';
        priceFilter.value = '';
        tissuRows.forEach(row => row.style.display = '');
    });
});

function deleteTissu(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce tissu ?')) {
        // Créer un formulaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("vendeur.tissu.delete", ":id") }}'.replace(':id', id);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function openMetreModal(id, nom) {
    document.getElementById('metreTissuId').value = id;
    document.getElementById('metreTissuNom').value = nom;
    document.getElementById('metreAjout').value = '';
    
    // Mettre à jour l'action du formulaire
    const form = document.getElementById('addMetreForm');
    form.action = '{{ route("vendeur.tissu.add-metres", ":id") }}'.replace(':id', id);
    
    // Utiliser jQuery si disponible, sinon vanilla JS
    if (typeof $ !== 'undefined') {
        $('#metreModal').modal('show');
    } else if (typeof bootstrap !== 'undefined') {
        var metreModal = new bootstrap.Modal(document.getElementById('metreModal'));
        metreModal.show();
    } else {
        // Fallback vanilla JS
        document.getElementById('metreModal').style.display = 'block';
        document.getElementById('metreModal').classList.add('show');
        document.body.classList.add('modal-open');
    }
}

function closeMetreModal() {
    // Utiliser jQuery si disponible, sinon vanilla JS
    if (typeof $ !== 'undefined') {
        $('#metreModal').modal('hide');
    } else if (typeof bootstrap !== 'undefined') {
        var metreModal = bootstrap.Modal.getInstance(document.getElementById('metreModal'));
        if (metreModal) {
            metreModal.hide();
        }
    } else {
        // Fallback vanilla JS
        document.getElementById('metreModal').style.display = 'none';
        document.getElementById('metreModal').classList.remove('show');
        document.body.classList.remove('modal-open');
    }
}
</script>
@endsection 