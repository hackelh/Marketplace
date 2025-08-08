@extends('layouts.sidebare')

@section('title', 'Ajouter une Catégorie')
@section('breadcrumb', 'Ajouter une Catégorie')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-plus-circle"></i>
                    Ajouter une nouvelle catégorie
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('vendeur.ajouter-categorie.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" 
                                       name="nom" 
                                       value="{{ old('nom') }}" 
                                       required 
                                       placeholder="Ex: Coton, Soie, Laine...">
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="couleur_hex" class="form-label">Couleur</label>
                                <input type="color" 
                                       class="form-control @error('couleur_hex') is-invalid @enderror" 
                                       id="couleur_hex" 
                                       name="couleur_hex" 
                                       value="{{ old('couleur_hex', '#007bff') }}">
                                @error('couleur_hex')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mt-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Description de la catégorie...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Créer la catégorie
                        </button>
                        <a href="{{ route('vendeur.categories') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 