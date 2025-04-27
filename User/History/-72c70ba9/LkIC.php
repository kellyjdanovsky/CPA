@extends('layouts.master')
@section('page_title', 'Verification')
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-cash2 mr-2"></i>Verification</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <form method="post" action="{{ route('payments.select_class') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="my_class_id" class="col-form-label font-weight-bold">Classe :</label>
                                    <select required id="my_class_id" name="my_class_id" class="form-control select">
                                        <option id="my_class_id" value="">Choisir une Classe</option>
                                        @foreach($class as $c)
                                            <option  value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="payments" class="col-form-label font-weight-bold">Motif :</label>
                                    <select required id="payments" name="my_class_id" class="form-control select">
                                        <option value="">Choisir une payments</option>

                                            <option id="$payments" value="{{ $c->id }}">{{ $c->title }}</option>
                                        
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2 mt-4">
                                <div class="text-right mt-1">
                                    <button type="submit" class="btn btn-primary">Valider <i class="icon-paperplane ml-2"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('#my_class_id').change(function(){
                var classId = $(this).val();
                if(classId){
                    $.ajax({
                        type:"GET",
                        url:"{{ route('payments.select') }}/" + classId, // Pass the classId as a parameter
                        success:function(res){               
                            if(res){
                                $("#payments").empty();
                                $.each(res,function(key,value){
                                    $("#payments").append('<option value="'+value.id+'">'+value.title+'</option>');
                                });
    
                            }else{
                                $("#payments").empty();
                            }
                        }
                    });
                }else{
                   alert("Class not found");
                }
            });
        });
    </script>



    </script>

    {{-- @if($selected)
        <div class="card">
            <div class="card-body">
                <table class="table datatable-button-html5-columns">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Ref_No</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                            <td>{{ $s->user->name }}</td>
                            <td>{{ $s->adm_no }}</td>
                            <td>
                                <div class="dropdown">
                                    <a href="#" class=" btn btn-danger" data-toggle="dropdown"> Gestion par session <i class="icon-arrow-down5"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-left">
                                        <a href="{{ route('payments.invoice', [Qs::hash($s->user_id)]) }}" class="dropdown-item">Tous les paiements</a>
                                        @foreach(Pay::getYears($s->user_id) as $py)
                                            @if($py)
                                                <a href="{{ route('payments.invoice', [Qs::hash($s->user_id), $py]) }}" class="dropdown-item">{{ $py }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif --}}
@endsection
