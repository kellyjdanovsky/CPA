@extends('layouts.login_master')

@section('content')
    <div class="page-content login-cover">

        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Content area -->
            <div class="content d-flex justify-content-center align-items-center">

                <!-- Login card -->
                <form class="login-form login-form-custom" method="post" action="{{ route('login') }}">
                    @csrf
                    <div class="card mb-0 shadow-sm" style="min-width: 380px;">
                        <div class="card-body p-4" >
                            <div class="text-center mb-4">
        
                                <img src="/images/logo_avar.png" alt="Logo Avara" style="width: 280px; height: auto;" class="mb-3 mt-1 mx-auto d-block">
                                <h5 class="mb-0">Connectez-vous</h5>
                                <span class="d-block text-muted">Entrez vos identifiants</span>
                            </div>

                                @if ($errors->any())
                                <div class="alert alert-danger alert-styled-left alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                    <span class="font-weight-semibold">Oops!</span> {{ implode('<br>', $errors->all()) }}
                                </div>
                                @endif


                            <div class="form-group ">
                                <input type="text" class="form-control form-control-lg" name="identity" value="{{ old('identity') }}" placeholder="Email ou Nom d'utilisateur">
                            </div>

                            <div class="form-group ">
                                <input required name="password" type="password" class="form-control form-control-lg" placeholder="{{ __('Mot de passe') }}">

                            </div>

                            <div class="form-group d-flex align-items-center">
                                <div class="form-check mb-0">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="remember" class="form-input-styled" {{ old('remember') ? 'checked' : '' }} data-fouc>
                                        Se souvenir de moi
                                    </label>
                                </div>

                                <a href="{{ route('password.request') }}" class="ml-auto">Mot de passe oublié ?</a>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-block btn-lg">Se connecter <i class="icon-circle-right2 ml-2"></i></button>
                            </div>

                           {{-- <div class="form-group">
                                <a href="#" class="btn btn-light btn-block"><i class="icon-home"></i> Back to Home</a>
                            </div>--}}


                        </div>
                    </div>
                </form>

            </div>


        </div>

    </div>
    @endsection
