@extends('layouts.app')
@section('title', 'Recepción')

@section('css')
    <style type="text/css">

    </style>
@stop

@section('content')
    @php
        use Carbon\Carbon;
        $total = 0;
    @endphp
    @forelse($detalle as $row)
        @php
            $total += $row->importe;
        @endphp
    @empty
        @php
            $total = 0;
        @endphp
    @endforelse

    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Detalle de la garantia</h3>
            <div class="card-tools">
                <!-- Buttons, labels, and many other things can be placed here! -->
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="register-box-body">
                <div class="card border border-primary shadow-0 mb-3" style="max-width: 80rem;">
                    <div class="card-header">
                        <h4>DATOS DE GARANTIA</h4>
                    </div>
                    <div class="card-body text-dark">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <h4><strong>Fecha</strong></h4>
                                <h5>{{ Carbon::parse($garantia->created_at)->format('d/m/Y') }}</h5>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <h4><strong>Folio de garantia</strong></h4>
                                <h5>{{ $garantia->FolGarantia }}</h5>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <h4><strong>Garantia</strong></h4>
                                <h5>{{ $garantia->NumGarantia }}</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h4><strong>Descripción del Fallo</strong></h4>
                                <h5>{!! $garantia->DescripcionFallo !!}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h4><strong>Información Adicional</strong></h4>
                                <h5>{!! $garantia->TextAdicional !!}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h4><strong>Solución</strong></h4>
                                <h5>{!! $garantia->Solucion !!}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border border-success shadow-0 mb-3" style="max-width: 80rem;">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-lg-12 col-md-12">
                                <h4 class="card-title">PIEZAS DE GARANTIA</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-dark">
                        <table class="table table-striped table-hover dataTable dtr-inline display responsive nowrap"
                            style="width:100%" id="reparaciones">
                            <thead>
                                <tr>
                                    <th>Cant</th>
                                    <th>Producto</th>
                                    <th>Nota</th>
                                    <th>P.U</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody id="body_details_table">
                                
                                @forelse($detalle as $row)
                                    <tr>
                                        <td>
                                            {{ $row->cantidad }}
                                        </td>
                                        <td>
                                            {{ $row->Producto }}
                                        </td>
                                        <td>
                                            {{ $row->nota }}
                                        </td>
                                        <td>
                                            $ {{ number_format($row->precio, 2) }}
                                        </td>
                                        <td>
                                            $ {{ number_format($row->importe, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td>No se encontró ningún registro</td>
                                        <td style="display:none;"></td>
                                        <td style="display:none;"></td>
                                        <td style="display:none;"></td>
                                        <td style="display:none;"></td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align:right"><h5><strong>Total: $ {{ number_format($total, 2) }} </strong></h5></th>
                                </tr>
                            </tfoot>
                        </table>

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
    <script>
        $(document).ready(function() {
            $('#reparaciones').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                }
            });

            function formatToFloat (data){
                return data.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
            };
        });
    </script>
@stop
