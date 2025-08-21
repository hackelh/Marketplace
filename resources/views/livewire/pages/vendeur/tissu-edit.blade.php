@extends('layouts.sidebare')

@section('title', 'Modifier le Tissu')
@section('breadcrumb', 'Modifier le Tissu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier le Tissu</h3>
                    <div class="card-tools">
                        <a href="{{ route('vendeur.gestion-tissus') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendeur.tissu.update', $tissu->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nom" class="form-label">Nom du tissu *</label>
                                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                                   id="nom" name="nom" value="{{ old('nom', $tissu->nom) }}" required>
                                            @error('nom')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="prix" class="form-label">Prix (CFA) *</label>
                                            <input type="number" step="0.01" class="form-control @error('prix') is-invalid @enderror" 
                                                   id="prix" name="prix" value="{{ old('prix', $tissu->prix) }}" required>
                                            @error('prix')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Stock (mètres) *</label>
                                            <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                                   id="stock" name="stock" value="{{ old('stock', $tissu->stock) }}" required>
                                            @error('stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="categorie_id" class="form-label">Catégorie *</label>
                                            <select class="form-select @error('categorie_id') is-invalid @enderror" 
                                                    id="categorie_id" name="categorie_id" required>
                                                <option value="">Sélectionner une catégorie</option>
                                                @foreach($categories as $categorie)
                                                    <option value="{{ $categorie->id }}" 
                                                            {{ old('categorie_id', $tissu->categorie_id) == $categorie->id ? 'selected' : '' }}>
                                                        {{ $categorie->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('categorie_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="origine" class="form-label">Origine (pays)</label>
                                            @php($paysList = \App\Models\Pays::orderBy('nom')->get())
                                            @php($currentOrigine = old('origine', $tissu->origine))
                                            <select class="form-select @error('origine') is-invalid @enderror" id="origine" name="origine">
                                                <option value="">Sélectionnez un pays</option>
                                                @foreach($paysList as $p)
                                                    <option value="{{ $p->nom }}" {{ $currentOrigine === $p->nom ? 'selected' : '' }}>{{ $p->nom }}</option>
                                                @endforeach
                                            </select>
                                            @error('origine')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="composition" class="form-label">Composition</label>
                                    <input type="text" class="form-control @error('composition') is-invalid @enderror" 
                                           id="composition" name="composition" value="{{ old('composition', $tissu->composition) }}">
                                    @error('composition')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description', $tissu->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="disponible" name="disponible" 
                                               value="1" {{ old('disponible', $tissu->disponible) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="disponible">
                                            Disponible à la vente
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image</label>
                                    @if($tissu->image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $tissu->image) }}" alt="Image actuelle" 
                                                 class="img-thumbnail" style="max-width: 200px;">
                                            <p class="text-muted small">Image actuelle</p>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 2MB</div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('vendeur.gestion-tissus') }}" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Couleur supprimée: JS retiré --}}
@endsection 