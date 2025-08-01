<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Vendeur de Tissus') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation des onglets -->
            <div class="mb-8">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <button onclick="showTab('gestion')" id="tab-gestion" 
                            class="tab-button active whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Gestion des Tissus
                    </button>
                    <button onclick="showTab('categories')" id="tab-categories" 
                            class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Catégories
                    </button>
                    <button onclick="showTab('statistiques')" id="tab-statistiques" 
                            class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Statistiques
                    </button>
                </nav>
            </div>

            <!-- Contenu des onglets -->
            <div id="content-gestion" class="tab-content">
                <livewire:vendeur.gestion-tissus />
            </div>

            <div id="content-categories" class="tab-content hidden">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Gestion des Catégories</h3>
                    <p class="text-gray-600">Cette fonctionnalité sera développée prochainement.</p>
                    <p class="text-sm text-gray-500 mt-2">
                        Vous pourrez créer et gérer vos propres catégories de tissus.
                    </p>
                </div>
            </div>

            <div id="content-statistiques" class="tab-content hidden">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques de Vente</h3>
                    <p class="text-gray-600">Cette fonctionnalité sera développée prochainement.</p>
                    <p class="text-sm text-gray-500 mt-2">
                        Vous pourrez consulter vos statistiques de vente, revenus, et analyses de performance.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tab-button {
            @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300;
        }
        .tab-button.active {
            @apply border-indigo-500 text-indigo-600;
        }
    </style>

    <script>
        function showTab(tabName) {
            // Masquer tous les contenus
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Désactiver tous les boutons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Afficher le contenu sélectionné
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            // Activer le bouton sélectionné
            document.getElementById('tab-' + tabName).classList.add('active');
        }
    </script>
</x-app-layout>
