@extends('layouts.master')
@section('page_title', 'My Dashboard')

@section('content')
    <h2>Bienvenue {{ Auth::user()->name }}. Voici nos tableau de bord</h2>
    @endsection