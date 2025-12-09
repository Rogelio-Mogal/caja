@extends('adminlte::page')

@section('title', 'Eliminar Rol')


@section('content')
<br/>
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Eliminar Rol</h3>
            <div class="card-tools">
                <!-- Buttons, labels, and many other things can be placed here! -->
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="register-box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12" align="center">
                            <div class="input-group mb-3">
                                <img src="{{url($rutaImg)}}" id="profile-img-tag" width="230px" height="auto" align="center" />
                            </div>
                        </div>
                    </div>        
                    <div class="row">
                        <div class="col-6">
                            <label>Producto</label>
                            <div class="input-group mb-3">
                                <p class="form-control">{{$productos->Producto}}</p>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-6">
                            <label>Código</label>
                            <div class="input-group mb-3">
                                <p class="form-control">{{$productos->Codigo}}</p>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label>Familia</label>
                            <div class="input-group mb-3">
                                <p class="form-control">{{$familia[0]->Familia}}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <label>Sub Familia</label>
                            <div class="input-group mb-3">
                                @forelse ($subfamilia as $product)
                                    <p class="form-control">
                                        {{$product->SubFamilia}}
                                    </p>
                                @empty
                                    <p class="form-control"></p>
                                @endforelse

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label>Descripción</label>
                            <div class="input-group mb-3">
                                {{$productos->Descripcion}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" align="center">
                            {!! Form::open(['method'=>'DELETE', 'route'=> ['admin.servicios.destroy',$productos ->IdProducto]]) !!}
                                <div class="form-group">
                                    {!! Form::submit('Elimina Registro',['class'=>'btn btn-raised btn-danger']) !!} 
                                </div>
                            {!! Form::close() !!} 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
<!-- /.card -->
@stop

@section('css')
   <!-- <link rel="stylesheet" href="/css/admin_custom.css"> -->
@stop

@section('js')

    @if(session('fail') == 'Error elimina producto')
        <script type="text/javascript">
            Swal.fire(
                '¡Aviso!',
                'Aun existen piezas de este producto, por lo tanto no se puede eliminar.',
                'error'
            );
        </script>
    @endif

    <script>
$(document).ready(function() {
    var url = "{{ $currentURL }}";
    // for sidebar menu entirely but not cover treeview
        $('ul.nav-sidebar a').filter(function() {
            return this.href == url;
        }).addClass('active');

        // for treeview SUBMENUS
        $('ul.nav-treeview a').filter(function() {
            return this.href == url;
        }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');       

    })
    </script>
@stop