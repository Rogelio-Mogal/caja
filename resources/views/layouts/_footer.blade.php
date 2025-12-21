<footer class="bg-info text-center text-lg-start fixed-bottom no-imprimir">

    <!-- Copyright -->
    <div class="text-center text-white p-3" style="background-color: rgba(8, 44, 78, 1);">
        ©
        <a class="text-white text-reset fw-bold" href="#">Caja de Ahorro SSPO {{ now()->year }}</a>
    </div>
    <!-- Copyright -->
</footer>

<!-- Agrega los enlaces a los archivos de scripts de MDB -->
<script src="{{ asset('mdb/js/mdb.min.js') }}" defer></script>

<!-- Agrega los enlaces a los archivos de scripts de LazyLoad  -->
<script src="{{ asset('js/lazysizes.min.js') }}" defer></script>

<script src="{{ asset('datatable/js/datatables.min.js') }}"></script>
<script src="https://cdn.datatables.net/rowgroup/1.4.1/js/dataTables.rowGroup.min.js"></script>


{{--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>--}}

<script src="{{ asset('bundle/bootstrap.bundle.min.js') }}"></script>

{{-- SELECT2 --}}
<script src="{{ asset('select2/select2.min.js') }}"></script>

<!-- Mensajes de alerta -->
@if(session('swal'))
    <script>
        Swal.fire( {!! json_encode(session('swal')) !!} )
    </script>
@endif


@yield('js')

</body>

</html>
