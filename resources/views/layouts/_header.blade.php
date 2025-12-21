<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <title>{{ config('app.name', 'SSPO') }}</title>

    <!-- Agrega los enlaces a los archivos de estilos de MDB -->
    <link href="{{ asset('mdb/css/mdb.min.css') }}" rel="stylesheet">

    <!-- Agrega los enlaces a los archivos de estilos de LazyLoad  -->
    <link rel="stylesheet" href="{{ asset('css/carousel.css') }}">

    <!-- Agrega los enlaces a los archivos de estilos de Fontawesome  -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset('datatable/css/datatables.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.1/css/rowGroup.dataTables.min.css">

    {{-- SELECT2 --}}
    <link rel="stylesheet" href="{{ asset('select2/select2.min.css') }}"  />

    <!-- SWEET ALERT 2 -->
    {{--<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>--}}

    <script src="{{ asset('sweetalert/sweetalert2@10.js') }}"></script>

    <style type="text/css">
        .active {
            border-bottom: 1px solid rgb(255, 255, 255);
        }

        .card.border {
            --mdb-border-width: 3px !important;
            /* Cambia el tamaño del borde a 3px */
            border-width: var(--mdb-border-width) !important;
            /* Importante para asegurarte de que esta regla prevalezca sobre otras */
        }

        .list-group {
            --mdb-list-group-bg: rgba(0, 132, 134) !important;
            --mdb-list-group-color: #ffffff !important;
        }
    </style>

    @yield('css')
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg text-white no-imprimir" style="background-color: rgba(8, 44, 78, 1);">
        <!-- Container wrapper -->
        <div class="container text-white">
            <!-- Navbar brand -->
            <a class="navbar-brand me-2" href="/">
                <img src="{{ asset('image/caja.png') }}" height="70" alt="SSPO_logo" loading="lazy"
                    style="margin-top: -1px;" />
            </a>

            <!-- Toggle button -->
            <button class="navbar-toggler" type="button" data-mdb-toggle="collapse"
                data-mdb-target="#navbarButtonsExample" aria-controls="navbarButtonsExample" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fas fa-bars text-white"></i>
            </button>

            <!-- Collapsible wrapper -->

            <div class="collapse navbar-collapse" id="navbarButtonsExample">

                <!-- Left links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Navbar dropdown -->
                    <li class="nav-item dropdown position-static">
                        <a class="nav-link dropdown-toggle text-white {{ Str::startsWith(request()->path(), 'socios') ? 'active' : '' }}"
                            href="#" id="navbarDropdown" role="button" data-mdb-toggle="dropdown"
                            aria-expanded="false">
                            Socios
                        </a>
                        <!-- Dropdown menu -->
                        <div class="dropdown-menu w-100 mt-0" aria-labelledby="navbarDropdown"
                            style="
                            border-top-left-radius: 0;
                            border-top-right-radius: 0;
                            background-color: rgb(0, 132, 134);
                          ">
                            <div class="container">
                                <div class="row my-4">
                                    <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                        <div class="list-group list-group-flush">
                                            <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                <strong>Socios</strong>
                                            </p>
                                            @can('ver-socio')
                                                <a href="{{ route('admin.socios.index') }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Ver socio</a>
                                            @endcan
                                            @can('crear-socio')
                                                <a href="{{ route('admin.socios.create') }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Nuevo socio</a>
                                            @endcan
                                            @can('ver-socio-historial')
                                                <a href="{{ route('admin.historial.index') }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Historial</a>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                        <div class="list-group list-group-flush">
                                            <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                <strong> Ahorro</strong>
                                            </p>
                                            @can('agregar-ahorro-voluntario')
                                                <a href="{{ route('admin.ahorros.index') }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Ahorro voluntario</a>
                                            @endcan
                                            @can('agregar-ahorro-voluntario')
                                                <a href="{{ route('admin.ahorros.create') }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Nuevo Ahorro voluntario</a>
                                            @endcan
                                            @can('agregar-ahorro-excel')
                                                    <a href="{{ route('admin.excel.ahorros.create') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Importar ahorro quincenal</a>
                                            @endcan
                                            @can('aprobar-retiro')
                                                    <a href="{{ route('admin.retiros.create') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Pre-aprobar retiro</a>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3 mb-3 mb-md-0">
                                        <div class="list-group list-group-flush">
                                            <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                <strong> Préstamos</strong>
                                            </p>
                                            @can('crear-prestamo')
                                                <a href="{{ route('admin.prestamos.create') }}"
                                                class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Nuevo préstamo</a>
                                            @endcan
                                            @can('saldo-simulador')
                                                    <a href="{{ route('admin.simulador.prestamos.index') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Saldos y simulador</a>
                                            @endcan
                                            @can('historial-prestamo')
                                                    <a href="{{ route('admin.historial.prestamos.index') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Historial de préstamos</a>
                                            @endcan
                                            @can('historial-avales')
                                                    <a href="{{ route('admin.avales.index') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Historial de avales</a>
                                            @endcan
                                            @can('prestamos-diarios')
                                                    <a href="{{ route('admin.prestamos.index') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Préstamos por día</a>
                                            @endcan
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                        <div class="list-group list-group-flush">
                                            <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                <strong> Préstamos especiales</strong>
                                            </p>
                                            @can('ver-concepto-prestamo-especial')    
                                                <a href="{{ route('admin.prestamos.comceptos.index') }}"
                                                class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Conceptos préstamos
                                                especiales</a>
                                            @endcan
                                            @can('crear-prestamos-especiales')
                                                <a href="{{ route('admin.prestamos.especiales.create') }}"
                                                class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Nuevo préstamo especial</a>
                                            @endcan
                                            @can('crear-prestamos-enfermedad')
                                                    <a href="{{ route('admin.prestamos.enfermedad.create') }}"
                                                class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Nuevo préstamo por
                                                enfermedad</a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    @can('fianlizar-prestamo')
                        <li class="nav-item dropdown position-static">
                            <a class="nav-link dropdown-toggle text-white {{ Str::startsWith(request()->path(), 'tesoreria') ? 'active' : '' }}"
                                href="#" id="navbarDropdown" role="button" data-mdb-toggle="dropdown"
                                aria-expanded="false">
                                Tesorería
                            </a>
                            <!-- Dropdown menu -->
                            <div class="dropdown-menu w-100 mt-0" aria-labelledby="navbarDropdown"
                                style="
                                border-top-left-radius: 0;
                                border-top-right-radius: 0;
                                background-color: rgb(0, 132, 134);
                            ">
                                <div class="container">
                                    <div class="row my-4">
                                        <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                            <div class="list-group list-group-flush">
                                                <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                    <strong> Tesorería</strong>
                                                </p>
                                                @can('fianlizar-prestamo')
                                                    <a href="{{ route('admin.tesoreria.prestamos.index') }}"
                                                        class="list-group-item list-group-item-action"><i
                                                            class="fas fa-caret-right pe-2"></i>Finalizar prestamo</a>
                                                @endcan
                                                @can('finalizar-retiro')
                                                    <a href="{{ route('admin.tesoreria.retiro.index') }}"
                                                        class="list-group-item list-group-item-action"><i
                                                            class="fas fa-caret-right pe-2"></i>Finalizar retiro</a>
                                                @endcan
                                                @can('pagar-prestamo')
                                                    <a href="{{ route('admin.pagar.prestamo.index') }}"
                                                        class="list-group-item list-group-item-action"><i
                                                            class="fas fa-caret-right pe-2"></i>Liquidar préstamo</a>
                                                @endcan

                                                {{--
                                                @can('corte-caja')
                                                    <a href="" class="list-group-item list-group-item-action"><i
                                                            class="fas fa-caret-right pe-2"></i>Corte de caja</a>
                                                @endcan
                                                
                                                @can('reposiscion-credencial')
                                                    <a href="" class="list-group-item list-group-item-action"><i
                                                            class="fas fa-caret-right pe-2"></i>Reposición de credencial</a>
                                                @endcan
                                                --}}
                                            </div>
                                        </div>
                    
                                        <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                            @can('cargar-pago-prestamo-excel')
                                                <div class="list-group list-group-flush">
                                                    <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                        <strong> Pago de préstamos</strong>
                                                    </p>
                                                    @can('cargar-pago-prestamo-excel')
                                                        <a href="{{ route('admin.pago.prestamos.create') }}"
                                                            class="list-group-item list-group-item-action"><i
                                                                class="fas fa-caret-right pe-2"></i>Cargar pagos de préstamos
                                                            (Formato Excel)</a>
                                                    @endcan
                                                    @can('historial-pago-prestamos')
                                                        <a href="{{ route('admin.pago.prestamos.index') }}"
                                                            class="list-group-item list-group-item-action"><i
                                                                class="fas fa-caret-right pe-2"></i>Historial pagos de
                                                            préstamos</a>
                                                    @endcan
                                                </div>
                                            @endcan
                                        </div>

                                        <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                            @can('devoluciones')
                                                <div class="list-group list-group-flush">
                                                    <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                        <strong> Devoluciones</strong>
                                                    </p>
                                                    @can('devoluciones')
                                                        <a href="{{ route('admin.devoluciones.create') }}"
                                                            class="list-group-item list-group-item-action">
                                                            <i class="fas fa-caret-right pe-2"></i>
                                                            Nueva devolución
                                                        </a>
                                                    @endcan
                                                    @can('devoluciones')
                                                        <a href="{{ route('admin.devoluciones.index') }}"
                                                            class="list-group-item list-group-item-action">
                                                            <i class="fas fa-caret-right pe-2"></i>
                                                            Finalizar devolución
                                                        </a>
                                                    @endcan
                                                </div>
                                            @endcan
                                        </div>

                                        <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                            @can('devoluciones')
                                                <div class="list-group list-group-flush">
                                                    <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                        <strong> Efectivo diario</strong>
                                                    </p>
                                                    @can('devoluciones')
                                                        <a href="{{ route('admin.efectivo.diario.index') }}"
                                                            class="list-group-item list-group-item-action"><i
                                                                class="fas fa-caret-right pe-2"></i>
                                                                Efectivo diario
                                                        </a>
                                                        <a href="{{ route('admin.efectivo.diario.create') }}"
                                                            class="list-group-item list-group-item-action">
                                                            <i class="fas fa-caret-right pe-2"></i>
                                                            Nuevo efectivo diario
                                                        </a>
                                                    @endcan
                                                </div>
                                            @endcan
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </li>
                    @endcan

                    {{--
                    @can('cargar-socios-excel')
                        <li class="nav-item dropdown position-static">
                            <a class="nav-link dropdown-toggle text-white {{ Str::startsWith(request()->path(), 'excel-socios') ? 'active' : '' }}"
                                href="#" id="navbarDropdown" role="button" data-mdb-toggle="dropdown"
                                aria-expanded="false">
                                Cargar Excel
                            </a>
                            <!-- Dropdown menu -->
                            <div class="dropdown-menu w-100 mt-0" aria-labelledby="navbarDropdown"
                                style="
                                border-top-left-radius: 0;
                                border-top-right-radius: 0;
                                background-color: rgb(0, 132, 134);
                            ">
                                <div class="container">
                                    <div class="row my-4">
                                        <div class="col-md-7 col-lg-3 mb-3 mb-lg-0">
                                            <div class="list-group list-group-flush">
                                                <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                    <strong> Cargar Excel</strong>
                                                </p>
                                                <a href="{{ route('admin.excel.socios.create') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                        class="fas fa-caret-right pe-2"></i>Cargar socios (Formato
                                                    Excel)</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endcan
                    --}}

                    <li class="nav-item dropdown position-static">
                        <a class="nav-link dropdown-toggle text-white {{ Str::startsWith(request()->path(), 'reportes') ? 'active' : '' }}"
                            href="#" id="navbarDropdown" role="button" data-mdb-toggle="dropdown"
                            aria-expanded="false">
                            Reportes
                        </a>
                        <!-- Dropdown menu -->
                        <div class="dropdown-menu w-100 mt-0" aria-labelledby="navbarDropdown"
                            style="
                            border-top-left-radius: 0;
                            border-top-right-radius: 0;
                            background-color: rgb(0, 132, 134);
                          ">
                            <div class="container">
                                <div class="row my-4">
                                    <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                        <div class="list-group list-group-flush">
                                            {{--
                                            <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                <strong>Socios</strong>
                                            </p>
                                            --}}
                                            {{--@can('ver-socio')--}}
                                                <a href="{{ route('admin.reportes.index', ['tipo' => 'prestamos']) }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Reporte préstamos generados</a>
                                            {{--@endcan--}}
                                            {{--@can('crear-socio')--}}
                                                <a href="{{ route('admin.reportes.index', ['tipo' => 'pago-liquidacion']) }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Reporte liquidación de préstamos</a>
                                            {{--@endcan--}}
                                            {{--@can('ver-socio-historial')--}}
                                                <a href="{{ route('admin.reportes.index', ['tipo' => 'retiros']) }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Reporte retiros</a>
                                            {{--@endcan--}}
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                        <div class="list-group list-group-flush">
                                            {{--@can('agregar-ahorro-voluntario')--}}
                                                <a href="{{ route('admin.reportes.index', ['tipo' => 'ahorros']) }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Reporte ahorros</a>
                                            {{--@endcan--}}
                                            {{--@can('agregar-ahorro-voluntario')--}}
                                                <a href="{{ route('admin.reportes.index', ['tipo' => 'prestamo-pago-nomina']) }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Reporte pago de préstamos</a>

                                                <a href="{{ route('admin.reportes.index', ['tipo' => 'ingreso-efectivo']) }}"
                                                class="list-group-item list-group-item-action"><i
                                                class="fas fa-caret-right pe-2"></i>Reporte ingresos en efectivo</a>

                                            {{--@endcan--}}
                                            {{--
                                            @can('agregar-ahorro-excel')
                                                    <a href="{{ route('admin.excel.ahorros.create') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Importar ahorro quincenal</a>
                                            @endcan
                                            @can('aprobar-retiro')
                                                    <a href="{{ route('admin.retiros.create') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Pre-aprobar retiro</a>
                                            @endcan
                                            --}}
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                        <div class="list-group list-group-flush">
                                            <a href="{{ route('admin.reportes.index', ['tipo' => 'arqueo-caja']) }}"
                                            class="list-group-item list-group-item-action"><i
                                            class="fas fa-caret-right pe-2"></i>Arqueo de caja</a>
                                        </div>
                                    </div>
                                    {{--
                                    <div class="col-md-6 col-lg-3 mb-3 mb-md-0">
                                        <div class="list-group list-group-flush">
                                            @can('crear-prestamo')
                                                <a href="{{ route('admin.prestamos.create') }}"
                                                class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Nuevo préstamo</a>
                                            @endcan
                                            @can('saldo-simulador')
                                                    <a href="{{ route('admin.simulador.prestamos.index') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Saldos y simulador</a>
                                            @endcan
                                            @can('historial-prestamo')
                                                    <a href="{{ route('admin.historial.prestamos.index') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Historial de préstamos</a>
                                            @endcan
                                            @can('historial-avales')
                                                    <a href="{{ route('admin.avales.index') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Historial de avales</a>
                                            @endcan
                                            @can('prestamos-diarios')
                                                    <a href="{{ route('admin.prestamos.index') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Préstamos por día</a>
                                            @endcan
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                        <div class="list-group list-group-flush">
                                            @can('ver-concepto-prestamo-especial')    
                                                <a href="{{ route('admin.prestamos.comceptos.index') }}"
                                                class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Conceptos préstamos
                                                especiales</a>
                                            @endcan
                                            @can('crear-prestamos-especiales')
                                                <a href="{{ route('admin.prestamos.especiales.create') }}"
                                                class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Nuevo préstamo especial</a>
                                            @endcan
                                            @can('crear-prestamos-enfermedad')
                                                    <a href="{{ route('admin.prestamos.enfermedad.create') }}"
                                                class="list-group-item list-group-item-action"><i
                                                    class="fas fa-caret-right pe-2"></i>Nuevo préstamo por
                                                enfermedad</a>
                                            @endcan
                                        </div>
                                    </div>
                                    --}}
                                </div>
                            </div>
                        </div>
                    </li>

                    @can('ver-rol')
                        <li class="nav-item dropdown position-static">
                            <a class="nav-link dropdown-toggle text-white {{ Str::startsWith(request()->path(), 'excel-socios') ? 'active' : '' }}"
                                href="#" id="navbarDropdown" role="button" data-mdb-toggle="dropdown"
                                aria-expanded="false">
                                Administrador
                            </a>
                            <!-- Dropdown menu -->
                            <div class="dropdown-menu w-100 mt-0" aria-labelledby="navbarDropdown"
                                style="
                                border-top-left-radius: 0;
                                border-top-right-radius: 0;
                                background-color: rgb(0, 132, 134);
                            ">
                                <div class="container">
                                    <div class="row my-4">
                                        <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                            <div class="list-group list-group-flush">
                                                <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                    <strong>Cuentas de usuarios</strong>
                                                </p>
                                                <a href="{{ route('admin.usuarios.index') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                        class="fas fa-caret-right pe-2"></i>Lista de usuarios</a>
                                                <a href="{{ route('admin.roles.index') }}"
                                                    class="list-group-item list-group-item-action"><i
                                                        class="fas fa-caret-right pe-2"></i>Roles</a>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                                            @can('cargar-pago-prestamo-excel')
                                                <div class="list-group list-group-flush">
                                                    <p class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                        <strong>Sector / Categoría</strong>
                                                    </p>
                                                    @can('cargar-pago-prestamo-excel')
                                                        <a href="{{ route('admin.sector.categoria.index') }}"
                                                            class="list-group-item list-group-item-action"><i
                                                                class="fas fa-caret-right pe-2"></i>Sector / Categoría</a>
                                                    @endcan
                                                </div>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endcan
                </ul>

                <!-- Left links -->

                <div class="d-flex align-items-center">
                    <!-- Icon -->

                    <!-- Avatar -->
                    @if (Route::has('login'))
                        <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                            @auth
                                <div class="dropdown">
                                    <a class="dropdown-toggle d-flex align-items-center hidden-arrow" href="#"
                                        id="navbarDropdownMenuAvatar" role="button" data-mdb-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fas fa-user-circle fa-2x mr-2 text-white"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="navbarDropdownMenuAvatar">
                                        <li>
                                            <a href="{{ route('admin.user.edit', ['user' => auth()->id()]) }}"
                                                class="dropdown-item">Perfil</a>
                                        </li>
                                        <li>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    Cerrar sesión
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <div class="d-flex float-end">
                                    <a href="{{ route('login') }}"
                                        class="me-1 border rounded py-1 px-3 nav-link d-flex align-items-center">
                                        <i class="fas fa-user-alt m-1 me-md-2"></i>
                                        <p class="d-none d-md-block mb-0">Inicio de sesión</p>
                                    </a>

                                </div>
                            @endauth
                        </div>
                    @endif

                    <!-- <button type="button" class="btn btn-link px-3 me-2">
            Login
          </button>
          <button type="button" class="btn btn-primary me-3">
            Sign up for free
          </button>
          <a
            class="btn btn-dark px-3"
            href="https://github.com/mdbootstrap/mdb-ui-kit"
            role="button"
            ><i class="fab fa-github"></i
          ></a>
            -->

                </div>
            </div>
            <!-- Collapsible wrapper -->
        </div>
        <!-- Container wrapper -->
    </nav>
    <!-- Navbar -->
