<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Tailleur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <x-application-logo class="block h-12 w-auto" />

                    <h1 class="mt-8 text-2xl font-medium text-gray-900">
                        Bienvenue Tailleur !
                    </h1>

                    <p class="mt-6 text-gray-500 leading-relaxed">
                        Vous êtes connecté en tant que tailleur. 
                        Depuis cet espace, vous pourrez gérer vos services de couture, 
                        recevoir des commandes de confection et collaborer avec les vendeurs de tissus.
                    </p>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
                    <div>
                        <div class="flex items-center">
                            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8 text-gray-500">
                                <path d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM7 3H5a2 2 0 00-2 2v12a4 4 0 004 4h2a2 2 0 002-2V5a2 2 0 00-2-2H7z"></path>
                                <path d="m22 22-4.5-4.5m0 0L13 13l4.5 4.5zM18.5 17.5l4.5 4.5M13 13l4.5 4.5"></path>
                            </svg>
                            <h2 class="ml-3 text-xl font-semibold text-gray-900">
                                Services de couture
                            </h2>
                        </div>

                        <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                            Gérez vos services de confection et définissez vos tarifs de couture.
                        </p>
                    </div>

                    <div>
                        <div class="flex items-center">
                            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8 text-gray-500">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h2 class="ml-3 text-xl font-semibold text-gray-900">
                                Commandes en cours
                            </h2>
                        </div>

                        <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                            Suivez l'avancement de vos commandes de confection et gérez vos délais.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
