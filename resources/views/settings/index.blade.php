@extends('layouts.app')

@section('title', 'Paramètres')

@section('content')
    <h1>Paramètres de l\'application</h1>
    <div class="mb-4">
        <a href="{{ route('settings.create') }}" class="btn btn-primary">Ajouter un paramètre</a>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Valeur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($settings as $setting)
                <tr>
                    <td>{{ $setting->key }}</td>
                    <td>{{ $setting->value }}</td>
                    <td>
                        <form action="{{ route('settings.destroy', $setting->id) }}" method="POST" style="display: inline-block;">
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                        <a href="{{ route('settings.edit', $setting->id) }}" class="btn btn-info btn-sm">Modifier</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
