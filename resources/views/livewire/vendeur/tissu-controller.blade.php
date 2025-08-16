<div class="container mx-auto px-4 py-8">
    <!-- Script de débogage -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            console.log('Livewire initialisé');
            
            // Écouter les changements de la propriété isOpen
            Livewire.on('isOpenUpdated', (value) => {
                console.log('isOpen a changé:', value);
            });
        });
    </script>
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestion des Tissus</h1>
        <button 
            wire:click="create" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
            Ajouter un Tissu
        </button>
    </div>

    <!-- Barre de recherche et filtre -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="w-full md:w-1/3">
                <input wire:model.live.debounce.300ms="search" type="text" 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="Rechercher un tissu...">
            </div>
            <div class="w-full md:w-1/4">
                <select wire:model.live="perPage" class="w-full px-4 py-2 border rounded-lg">
                    <option value="5">5 par page</option>
                    <option value="10">10 par page</option>
                    <option value="25">25 par page</option>
                    <option value="50">50 par page</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tableau des tissus -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Image
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Titre
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Catégorie
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Prix
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Quantité
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tissus as $tissu)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                @if($tissu->image)
                                    <img src="{{ asset('storage/' . $tissu->image) }}" alt="{{ $tissu->titre }}" class="w-16 h-16 object-cover rounded">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-gray-400 text-xs">Aucune image</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $tissu->titre }}</p>
                                <p class="text-gray-600 text-xs">{{ Str::limit($tissu->description, 50) }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $tissu->categorie }}
                                </span>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ number_format($tissu->prix, 2, ',', ' ') }} €</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <span class="px-2 py-1 text-xs rounded-full {{ $tissu->quantite > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $tissu->quantite }} disponible(s)
                                </span>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex space-x-2">
                                    <button wire:click="edit({{ $tissu->id }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    <button wire:click="$dispatch('confirm-delete', { id: {{ $tissu->id }} })" 
                                        class="text-red-600 hover:text-red-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                Aucun tissu trouvé. Commencez par en ajouter un !
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-5 py-3 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
            <div class="text-xs text-gray-500 mb-2 xs:mb-0">
                Affichage de {{ $tissus->firstItem() }} à {{ $tissus->lastItem() }} sur {{ $tissus->total() }} tissus
            </div>
            <div class="mt-2 xs:mt-0">
                {{ $tissus->links() }}
            </div>
        </div>
    </div>

    <!-- Modal d'ajout/édition -->
    <div x-data="{ open: @entangle('isOpen') }" x-show="open" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 @click="$wire.closeModal()"
                 aria-hidden="true">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $tissuId ? 'Modifier le tissu' : 'Ajouter un nouveau tissu' }}
                        </h3>
                        <div class="mt-5">
                            <div class="space-y-4">
                                <div>
                                    <x-label for="titre" value="Titre du tissu" />
                                    <x-input id="titre" type="text" class="mt-1 block w-full" wire:model="titre" />
                                    @error('titre')
                                        <x-input-error :messages="[$message]" class="mt-1" />
                                    @enderror
                                </div>

                                <div>
                                    <x-label for="description" value="Description" />
                                    <textarea id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" wire:model="description" rows="3"></textarea>
                                    @error('description')
                                        <x-input-error :messages="[$message]" class="mt-1" />
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-label for="prix" value="Prix (€)" />
                                        <x-input id="prix" type="number" step="0.01" class="mt-1 block w-full" wire:model="prix" />
                                        @error('prix')
                                            <x-input-error :messages="[$message]" class="mt-1" />
                                        @enderror
                                    </div>

                                    <div>
                                        <x-label for="quantite" value="Quantité disponible" />
                                        <x-input id="quantite" type="number" class="mt-1 block w-full" wire:model="quantite" />
                                        @error('quantite')
                                            <x-input-error :messages="[$message]" class="mt-1" />
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-label for="categorie" value="Catégorie" />
                                        <select id="categorie" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" wire:model="categorie">
                                            <option value="">Sélectionnez une catégorie</option>
                                            <option value="Coton">Coton</option>
                                            <option value="Soie">Soie</option>
                                            <option value="Laine">Laine</option>
                                            <option value="Lin">Lin</option>
                                            <option value="Synthétique">Synthétique</option>
                                        </select>
                                        @error('categorie')
                                            <x-input-error :messages="[$message]" class="mt-1" />
                                        @enderror
                                    </div>

                                    <div>
                                        <x-label for="couleur" value="Couleur" />
                                        <x-input id="couleur" type="text" class="mt-1 block w-full" wire:model="couleur" />
                                        @error('couleur')
                                            <x-input-error :messages="[$message]" class="mt-1" />
                                        @enderror
                                    </div>

                                    <div>
                                        <x-label for="matiere" value="Matière" />
                                        <x-input id="matiere" type="text" class="mt-1 block w-full" wire:model="matiere" />
                                        @error('matiere')
                                            <x-input-error :messages="[$message]" class="mt-1" />
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <x-label for="image" value="Image du tissu" />
                                    @if($image)
                                        <div class="mt-2">
                                            <img src="{{ $image->temporaryUrl() }}" class="h-32 w-32 object-cover rounded">
                                        </div>
                                    @endif
                                    <input id="image" type="file" class="mt-1 block w-full" wire:model="image" />
                                    @error('image')
                                        <x-input-error :messages="[$message]" class="mt-1" />
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="flex justify-end gap-3 mt-6">
                                <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                                    Annuler
                                </x-secondary-button>
                                <x-primary-button wire:click="store" wire:loading.attr="disabled">
                                    {{ $tissuId ? 'Mettre à jour' : 'Créer' }}
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation de suppression -->
    <x-confirmation-modal wire:model.live="confirmingTissuDeletion" name="confirm-delete-tissu">
        <x-slot name="title">
            Supprimer le tissu
        </x-slot>

        <x-slot name="content">
            Êtes-vous sûr de vouloir supprimer ce tissu ? Cette action est irréversible.
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingTissuDeletion', false)" wire:loading.attr="disabled">
                Annuler
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="delete" wire:loading.attr="disabled">
                Supprimer
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                @this.on('confirm-delete', (event) => {
                    if (confirm('Êtes-vous sûr de vouloir supprimer ce tissu ? Cette action est irréversible.')) {
                        @this.delete(event.id);
                    }
                });
            });
        </script>
    @endpush
</div>
