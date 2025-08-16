<div>
    <h2 class="text-2xl font-bold mb-4">Mes commandes</h2>

    <!-- Filtres et recherche -->
    <div class="flex mb-4 space-x-4">
        <select wire:model="statutFiltre" class="border rounded p-2">
            <option value="tous">Tous</option>
            <option value="en_attente">En attente</option>
            <option value="en_preparation">En préparation</option>
            <option value="expediee">Expédiée</option>
            <option value="annulee">Annulée</option>
        </select>

        <input type="text" wire:model.debounce.500ms="recherche" placeholder="Rechercher..." class="border rounded p-2 flex-1" />

        <button wire:click="reinitialiserFiltres" class="bg-gray-200 px-4 py-2 rounded">Réinitialiser</button>
    </div>

    @if($commandes->count())
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">Référence</th>
                    <th class="p-2 border">Statut</th>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Total</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commandes as $commande)
                    <tr>
                        <td class="p-2 border">{{ $commande->reference }}</td>
                        <td class="p-2 border">{{ ucfirst($commande->statut) }}</td>
                        <td class="p-2 border">{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-2 border">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</td>
                        <td class="p-2 border space-x-2">
                            <a href="{{ route('commandes.show', $commande->reference) }}" class="text-blue-500 hover:underline">Voir</a>

                            @if(in_array($commande->statut, ['en_attente', 'en_preparation']))
                                <button wire:click="annulerCommande({{ $commande->id }})" class="text-red-500 hover:underline">
                                    Annuler
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $commandes->links() }}
        </div>
    @else
        <p>Aucune commande trouvée.</p>
    @endif
</div>
