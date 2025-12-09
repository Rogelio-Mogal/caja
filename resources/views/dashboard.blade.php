@extends('layouts.app')

@section('content')

    @php
        use Carbon\Carbon;
    @endphp

    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            @if(auth()->check())
                <h3>Bienvenido, {{ auth()->user()->name }}</h3>
            @endif

            <div class="card-tools">
                <!-- Buttons, labels, and many other things can be placed here! -->
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <p class="text-center"><img class="masthead-avatar mb-5" src="{{ asset('image/caja.png') }}" height="625px" alt="SSPO_logo" /></p>
        </div>
    </div>
@stop


@section('js')
    <script>

    </script>
@stop
