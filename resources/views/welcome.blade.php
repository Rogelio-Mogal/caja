<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Caja de Ahorro</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('css/welcome/styles.css') }}" rel="stylesheet">
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" id="mainNav">
            <div class="container">
                <a class="navbar-brand" href="/">Caja de Ahorro</a>
                <button class="navbar-toggler text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="fas fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto">
                        @if (Route::has('login'))
                                @auth
                                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="{{ url('/dashboard') }}">Inicio</a></li>
                                @else
                                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="{{ route('login') }}">Inicio de sesión</a></li>
                                    @if (Route::has('register'))
                                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="{{ route('register') }}">Registro</a></li>
                                    @endif
                                @endauth
                        @endif
                    </ul>
                </div>
<!--
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="#portfolio">Portfolio</a></li>
                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="#about">About</a></li>
                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="#contact">Contact</a></li>
                    </ul>
                </div>
            -->
            </div>
        </nav>
        <!-- Masthead-->


        <!-- About Section-->
        <section class="masthead bg-primary text-white mb-0" id="about">
            <div class="container">
                <p class="text-center"><img class="masthead-avatar mb-5" src="{{ asset('image/caja.png') }}" alt="SSPO_logo" /></p>
                <!-- About Section Heading-->
                <h2 class="page-section-heading text-center text-uppercase text-white">Bienvenidos al sistema de Caja de Ahorro</h2>
                <!-- Icon Divider-->
                <div class="divider-custom divider-light">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                </div>
                <!-- About Section Content-->
                <div class="row">
                    <div class="col-lg-4 ms-auto"><p class="lead">Cuentas de ahorro: El sistema permite a los empleados abrir y administrar cuentas de ahorro individuales. Los empleados pueden depositar regularmente parte de sus ingresos en estas cuentas, lo que les permite acumular capital a lo largo del tiempo.</p></div>
                    <div class="col-lg-4 me-auto"><p class="lead">Préstamos: Los empleados tienen la opción de solicitar préstamos utilizando los fondos disponibles en sus cuentas de ahorro. El sistema evalúa la elegibilidad del empleado y proporciona una cantidad de préstamo basada en el capital acumulado y otros criterios predefinidos.</p></div>
                    <div class="col-lg-4 me-auto"><p class="lead">Procesamiento de solicitudes: El sistema gestiona todo el proceso de solicitud de préstamos, desde la presentación inicial hasta la aprobación y la distribución de los fondos. Se utilizan algoritmos y reglas predefinidas para evaluar la capacidad de reembolso y el riesgo asociado con cada solicitud.</p></div>
                    <div class="col-lg-4 me-auto"><p class="lead">Tasas de interés y plazos de reembolso: El sistema establece tasas de interés y plazos de reembolso para los préstamos. Estas condiciones pueden variar en función de la cantidad solicitada, la capacidad de pago del empleado y otros factores determinados por la política del sistema.</p></div>
                    <div class="col-lg-4 me-auto"><p class="lead">Gestión de pagos: El sistema facilita el seguimiento de los pagos y el cálculo de los intereses generados por los préstamos. Los empleados pueden acceder a información detallada sobre su historial de préstamos y realizar pagos en línea a través de la plataforma.</p></div>
                    <div class="col-lg-4 me-auto"><p class="lead">Seguridad y privacidad: El sistema implementa medidas de seguridad robustas para proteger la información personal y financiera de los empleados. Se utilizan técnicas de encriptación y autenticación para garantizar la confidencialidad de los datos.</p></div>
                </div>
            </div>
        </section>


        <!-- Copyright Section-->
        <div class="copyright py-4 text-center text-white">
            <div class="container"><small>&copy; Caja de Ahorro SSPO {{ now()->year }}</small></div>
        </div>
    </body>
</html>
