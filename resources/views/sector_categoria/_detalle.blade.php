@section('css')
    <style type="text/css">
        .btn-uno {
            background-color: #ecbe14 !important;
            color: #000;
        }

        .btn-dos {
            background-color: #6fa84b !important;
            color: #000;
        }

        .btn-tres {
            background-color: #34b5aa !important;
            color: #000;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .custom-p {
            display: inline-block;
            padding: .375rem .75rem;
            margin-bottom: 0;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            text-transform: uppercase;
        }

        .custom-p:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
@stop

<br />
@csrf
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Detallalles del sector/categoría</h3>
    </div>

    <div class="card-body">
        <div class="register-box-body">
            <div class="row mb-4">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="col">
                        <label for="campo1" class="form-label fw-bold">NOMBRE</label>
                        <p id="campo1" class="form-control custom-p">
                            {{ $sectorCategoria->nombre }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="col">
                        <label for="campo2" class="form-label fw-bold">TIPO</label>
                        <p id="campo2" class="form-control custom-p">
                            {{ $sectorCategoria->tipo }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="col-2">
                        {!! Form::open(['method' => 'DELETE', 'route' => ['admin.sector.categoria.destroy', $sectorCategoria->id]]) !!}
                        <div class="form-group">
                            {!! Form::submit('Elimina Registro', ['class' => 'btn btn-raised btn-danger', 'id' => 'btn-delete', 'data-id' => $sectorCategoria->id ]) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@section('js')
    <script>
        $(document).ready(function() {
            // Manejar el clic en la opción "Eliminar"
            $('#btn-delete').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                // Utilizar SweetAlert2 para mostrar un mensaje de confirmación
                Swal.fire({
                    title: '¿Estás seguro de eliminar el registro?',
                    text: 'No podrás revertir esto',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminarlo'
                }).then((result) => {
                    if (result.value) {
                        // Solicitud AJAX para eliminar el elemento
                        $.ajax({
                            url: "{{ route('admin.sector.categoria.destroy', ':id') }}"
                                .replace(':id', id),
                            type: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "_method": "DELETE"
                            },
                            success: function(response) {
                                Swal.fire(response.swal).then(() => {
                                    window.location.href = response.redirect;
                                });
                            },
                            error: function(xhr, status, error) {
                                //console.error(xhr.responseText);
                                if (xhr.status === 400) {
                                    var swalData = xhr.responseJSON.swal;
                                    Swal.fire({
                                        icon: 'error',
                                        title: swalData.title || 'Error',
                                        text: swalData.text || 'Hubo un error durante el proceso.',
                                    });
                                } else {
                                    console.error(xhr.responseText);
                                }
                            }
                        });
                    }
                });
            });


        });
    </script>


@stop
