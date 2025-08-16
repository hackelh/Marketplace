<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto text-center">
        <div class="bg-white p-8 rounded-lg shadow-sm border">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-900">Commande non trouvée</h2>
            <p class="mt-2 text-gray-600">
                Nous n'avons pas trouvé de commande correspondant à la référence fournie.
                Vérifiez que vous avez bien saisi le numéro de commande correctement.
            </p>
            <div class="mt-6">
                <a href="{{ route('commandes.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Retour à mes commandes
                </a>
            </div>
            <div class="mt-4">
                <p class="text-sm text-gray-500">
                    Si vous pensez qu'il s'agit d'une erreur, n'hésitez pas à 
                    <a href="{{ route('contact') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        nous contacter
                    </a>.
                </p>
            </div>
        </div>
    </div>
</div>
