@extends('layouts.master')
@section('page_title', 'Modifier un paramètre')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title"><i class="icon-pencil7 mr-2"></i>Modifier un paramètre</h5>
        <div class="header-elements">
            <div class="list-icons">
                <a href="{{ route('settings') }}" class="btn btn-secondary">
                    <i class="icon-arrow-left8 mr-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('settings.update', $setting->id) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="key">Clé:</label>
                <input type="text" class="form-control @error('key') is-invalid @enderror" id="key" name="key" value="{{ $setting->key }}" required>
                @error('key')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="value">Valeur:</label>
                <input type="text" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ $setting->value }}" required>
                @error('value')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="icon-paperplane mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
