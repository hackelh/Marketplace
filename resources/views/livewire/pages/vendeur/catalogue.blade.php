@extends('layouts.sidebare')

@section('title', 'Catalogue des Tissus')
@section('breadcrumb', 'Catalogue')

@section('content')
<div class="container-fluid">
    <!-- Header avec recherche et filtres -->
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



    <!-- Grille des tissus -->
    <div class="row" id="tissusGrid">
        @forelse(\App\Models\Tissu::with('categorie')->where('disponible', true)->where('stock', '>', 0)->get() as $tissu)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 tissu-card" 
             data-category="{{ $tissu->categorie_id }}" 
             data-price="{{ $tissu->prix }}"
             data-name="{{ strtolower($tissu->nom) }}">
            <div class="card h-100 shadow-sm hover-shadow">
                <!-- Image du tissu -->
                <div class="position-relative">
                    @if($tissu->image)
                        <img src="{{ asset('storage/' . $tissu->image) }}" 
                             class="card-img-top" 
                             alt="{{ $tissu->nom }}"
                             style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    
                    <!-- Badge de catégorie -->
                    @if($tissu->categorie)
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge" style="background-color: {{ $tissu->categorie->couleur_hex ?? '#007bff' }}; color: white;">
                                {{ $tissu->categorie->nom }}
                            </span>
                        </div>
                    @endif
                    
                    <!-- Badge de stock -->
                    <div class="position-absolute top-0 end-0 m-2">
                        @if($tissu->stock > 0)
                            <span class="badge bg-success">{{ $tissu->stock }} en stock</span>
                        @else
                            <span class="badge bg-danger">Rupture</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $tissu->nom }}</h5>
                    
                    @if($tissu->description)
                        <p class="card-text text-muted small">{{ Str::limit($tissu->description, 100) }}</p>
                    @endif
                    
                    <div class="mt-auto">
                        <!-- Couleur -->
                        <div class="mb-2">
                            <span class="badge" style="background-color: {{ $tissu->couleur }}; color: white;">
                                {{ $tissu->couleur }}
                            </span>
                        </div>
                        
                        <!-- Prix -->
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">{{ number_format($tissu->prix, 0) }} CFA</span>
                            <small class="text-muted">/m²</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-transparent border-top-0">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewTissu({{ $tissu->id }})">
                            <i class="bi bi-eye"></i> Voir détails
                        </button>
                        @if($tissu->stock > 0)
                            <button class="btn btn-success btn-sm" onclick="addToCart({{ $tissu->id }})">
                                <i class="bi bi-cart-plus"></i> Ajouter au panier
                            </button>
                        @else
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="bi bi-cart-x"></i> Indisponible
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">Aucun tissu disponible</h4>
                <p class="text-muted">Aucun tissu n'est actuellement disponible dans le catalogue.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<script>
// Filtrage et recherche
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceFilter = document.getElementById('priceFilter');
    const resetFilters = document.getElementById('resetFilters');
    const tissusGrid = document.getElementById('tissusGrid');
    const tissuCards = document.querySelectorAll('.tissu-card');

    function filterTissus() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedPrice = priceFilter.value;

        tissuCards.forEach(card => {
            let show = true;

            // Filtre par recherche
            if (searchTerm) {
                const name = card.dataset.name;
                if (!name.includes(searchTerm)) {
                    show = false;
                }
            }

            // Filtre par catégorie
            if (selectedCategory && card.dataset.category !== selectedCategory) {
                show = false;
            }

            // Filtre par prix
            if (selectedPrice) {
                const price = parseInt(card.dataset.price);
                const [min, max] = selectedPrice.split('-').map(p => p === '+' ? Infinity : parseInt(p));
                
                if (selectedPrice === '10000+') {
                    if (price < 10000) show = false;
                } else {
                    if (price < min || price > max) show = false;
                }
            }

            card.style.display = show ? 'block' : 'none';
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
        tissuCards.forEach(card => card.style.display = 'block');
    });
});

// Fonctions pour les actions
function viewTissu(id) {
    // TODO: Implémenter la vue détaillée
    alert('Vue détaillée du tissu ' + id + ' à implémenter');
}

function addToCart(id) {
    // TODO: Implémenter l'ajout au panier
    alert('Ajout au panier du tissu ' + id + ' à implémenter');
}
</script>

<style>
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    transition: all 0.3s ease;
}

.card {
    transition: all 0.3s ease;
}

.tissu-card {
    transition: all 0.3s ease;
}
</style>
@endsection 