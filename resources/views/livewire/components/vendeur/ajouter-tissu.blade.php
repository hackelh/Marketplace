<div>
    <!-- En-tête -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Ajouter un Tissu</h1>
                    <p class="text-gray-600">Ajoutez un nouveau tissu à votre catalogue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages flash -->
    @if (session()->has('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Formulaire -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informations du Tissu</h3>
            </div>
            
            <form action="{{ route('vendeur.ajouter-tissu.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom du tissu -->
                    <div class="md:col-span-2">
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom du tissu *</label>
                        <input type="text" name="nom" id="nom" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Ex: Tissu en coton">
                        @error('nom')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Description détaillée du tissu"></textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix -->
                    <div>
                        <label for="prix" class="block text-sm font-medium text-gray-700 mb-2">Prix (FCFA) *</label>
                        <input type="number" name="prix" id="prix" step="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="0.00">
                        @error('prix')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock (mètres) *</label>
                        <input type="number" name="stock" id="stock" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="0">
                        @error('stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Couleur -->
                    <div>
                        <label for="couleur" class="block text-sm font-medium text-gray-700 mb-2">Couleur *</label>
                        <select name="couleur" id="couleur-select" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="toggleCustomColor(this.value)">
                            <option value="">Sélectionnez une couleur</option>
                            <option value="Bleu">Bleu</option>
                            <option value="Rouge">Rouge</option>
                            <option value="Vert">Vert</option>
                            <option value="Jaune">Jaune</option>
                            <option value="Noir">Noir</option>
                            <option value="Blanc">Blanc</option>
                            <option value="Autre">Autre...</option>
                        </select>
                        <input type="text" name="couleur" id="custom-couleur" placeholder="Entrez une couleur personnalisée"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 mt-2 hidden">
                        @error('couleur')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label for="categorie_id" class="block text-sm font-medium text-gray-700 mb-2">Catégorie *</label>
                        <select name="categorie_id" id="categorie_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionnez une catégorie</option>
                            @foreach(\App\Models\Categorie::all() as $categorie)
                                <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                            @endforeach
                        </select>
                        @error('categorie_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Origine -->
                    <div>
                        <label for="origine" class="block text-sm font-medium text-gray-700 mb-2">Origine</label>
                        <input type="text" name="origine" id="origine"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Ex: Chine, Inde, France...">
                        @error('origine')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Image du tissu</label>
                        <input type="file" name="image" id="image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-sm text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF (max 2MB)</p>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Disponible -->
                    <div class="md:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="disponible" id="disponible" value="1" checked
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="disponible" class="ml-2 block text-sm text-gray-900">
                                Tissu disponible à la vente
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('vendeur.gestion-tissus') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Ajouter le Tissu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleCustomColor(val) {
    const custom = document.getElementById('custom-couleur');
    if (val === 'Autre') {
        custom.classList.remove('hidden');
        custom.required = true;
        custom.name = 'couleur';
        document.getElementById('couleur-select').name = '';
    } else {
        custom.classList.add('hidden');
        custom.required = false;
        custom.value = '';
        custom.name = '';
        document.getElementById('couleur-select').name = 'couleur';
    }
}
</script>
