<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Catalogue des Tissus</h1>
                        <p class="text-gray-600">Tissus disponibles publiés par les vendeurs</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <div class="relative flex-1">
                            <input id="searchInput" type="text" placeholder="Rechercher un tissu..." class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" />
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <select id="categoryFilter" class="sm:w-64 px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Toutes les catégories</option>
                            @foreach(\App\Models\Categorie::all() as $categorie)
                                <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="p-6 lg:p-8">
                <div id="tissusGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse(\App\Models\Tissu::with('categorie')->where('disponible', true)->where('stock', '>', 0)->latest()->get() as $tissu)
                        <div class="tissu-card border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition" data-category="{{ $tissu->categorie_id }}" data-name="{{ strtolower($tissu->nom) }}" data-price="{{ $tissu->prix }}">
                            @if($tissu->image)
                                <img src="{{ asset('storage/' . $tissu->image) }}" alt="{{ $tissu->nom }}" class="w-full h-48 object-cover" />
                            @else
                                <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M3 7l6 6-6 6M21 7l-6 6 6 6M3 7l6 6 6-6 6 6" />
                                    </svg>
                                </div>
                            @endif

                            <div class="p-4 flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $tissu->nom }}</h3>
                                    @if($tissu->categorie)
                                        <span class="text-xs px-2 py-1 rounded" style="background-color: {{ $tissu->categorie->couleur_hex ?? '#6366f1' }}; color: white;">{{ $tissu->categorie->nom }}</span>
                                    @endif
                                </div>

                                @if($tissu->description)
                                    <p class="text-sm text-gray-600">{{ Str::limit($tissu->description, 90) }}</p>
                                @endif

                                <div class="mt-2 flex items-center justify-between">
                                    <div class="text-indigo-600 font-bold">{{ number_format($tissu->prix, 0, ',', ' ') }} FCFA</div>
                                    <div class="text-xs text-green-600">{{ $tissu->stock }} en stock</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-500 py-12">
                            Aucun tissu disponible pour le moment.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const cards = document.querySelectorAll('.tissu-card');

        function filter() {
            const term = (searchInput.value || '').toLowerCase();
            const cat = categoryFilter.value || '';

            cards.forEach(card => {
                const name = card.dataset.name || '';
                const category = card.dataset.category || '';
                const matchName = !term || name.includes(term);
                const matchCat = !cat || cat === category;
                card.style.display = (matchName && matchCat) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filter);
        categoryFilter.addEventListener('change', filter);
    });
</script>