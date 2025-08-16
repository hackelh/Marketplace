@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="md:flex">
                    <!-- Images du produit -->
                    <div class="md:w-1/2 p-4">
                        @if($product->images->isNotEmpty())
                            <div class="mb-4">
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                     alt="{{ $product->titre }}" 
                                     class="w-full h-auto rounded-lg shadow-md">
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($product->images as $image)
                                    <div class="border rounded overflow-hidden">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             alt="{{ $product->titre }}" 
                                             class="w-full h-24 object-cover cursor-pointer hover:opacity-75">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-100 rounded-lg flex items-center justify-center" style="height: 400px;">
                                <span class="text-gray-400">Aucune image disponible</span>
                            </div>
                        @endif
                    </div>

                    <!-- Détails du produit -->
                    <div class="md:w-1/2 p-4">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->titre }}</h1>
                        
                        <div class="flex items-center mb-4">
                            <span class="text-2xl font-semibold text-indigo-600">{{ number_format($product->prix, 2, ',', ' ') }} €</span>
                            @if($product->quantite > 0)
                                <span class="ml-4 px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                    En stock ({{ $product->quantite }} disponible(s))
                                </span>
                            @else
                                <span class="ml-4 px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                                    Rupture de stock
                                </span>
                            @endif
                        </div>

                        <div class="mb-6">
                            <h2 class="text-lg font-semibold mb-2">Description</h2>
                            <p class="text-gray-700">{{ $product->description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Catégorie</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->categorie->nom ?? 'Non spécifiée' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Matière</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->matiere ?? 'Non spécifiée' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Couleur</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->couleur ?? 'Non spécifiée' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Vendeur</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->vendeur->name ?? 'Non spécifié' }}</p>
                            </div>
                        </div>

                        @if($product->quantite > 0)
                            <form action="{{ route('panier.ajouter', $product) }}" method="POST">
                                @csrf
                                <div class="flex items-center mb-4">
                                    <label for="quantite" class="mr-4">Quantité :</label>
                                    <select name="quantite" id="quantite" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        @for($i = 1; $i <= min(10, $product->quantite); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-6 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Ajouter au panier
                                </button>
                            </form>
                        @else
                            <button disabled class="w-full bg-gray-400 text-white py-3 px-6 rounded-md cursor-not-allowed">
                                Produit indisponible
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
