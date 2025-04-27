@extends('layouts.master')
@section('page_title', 'My Dashboard')

@section('content')
    <h2>WELCOME {{ Auth::user()->name }}. Voilci nos tableau de bord</h2>
    @endsection