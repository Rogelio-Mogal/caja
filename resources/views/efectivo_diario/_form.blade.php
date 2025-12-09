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

        /* Ajustar la altura de la caja del select2 */
        .select2-container .select2-selection--single {
                height: 35px; /* Ajusta la altura según tus necesidades */
                line-height: 35px; /* Asegura que el texto se alinee verticalmente al centro */
                display: flex; /* Flexbox para alinear el contenido */
                align-items: center; /* Centra el contenido verticalmente */
                position: relative; /* Necesario para posicionar el ícono */
            }

            /* Ajustar la altura del dropdown */
            .select2-container .select2-dropdown {
                max-height: 300px; /* Ajusta el alto máximo del dropdown */
                overflow-y: auto; /* Permite el scroll si el contenido es más alto */
            }

            /* Centrar el texto en el elemento seleccionado */
            .select2-container .select2-selection__rendered {
                line-height: 35px; /* Asegura que el texto seleccionado esté centrado */
                display: flex;
                align-items: center; /* Centra el texto verticalmente */
            }

            /* Posicionar el ícono de "limpiar" a la derecha */
            .select2-container .select2-selection__clear {
                position: absolute; /* Posicionamiento absoluto */
                right: 10px; /* Ajusta según sea necesario para alejarlo del borde derecho */
                top: 40%; /* Posición vertical en el centro */
                transform: translateY(-55%); /* Asegura que esté centrado verticalmente */
                cursor: pointer; /* Establece un puntero de cursor para indicar que es clickeable */
            }
    </style>
@stop

<br />
@csrf
<div class="card card-outline card-primary">
    <div class="card-header text-center">
        <h3 class="card-title">Efectivo diario</h3>
    </div>

    <div class="card-body">
        <div class="register-box-body">
            {{-- Centrar todo el contenido --}}
            <div class="row justify-content-center">
                {{-- Columna centrada y con ancho limitado --}}
                <div class="col-md-6 col-lg-5">

                    <div class="row">
                        <div class="col-4 mb-3">
                            <div class="form-outline">
                                {{ Form::number('b_mil', old('b_mil', $efectivo->b_mil), ['id' => 'b_mil', 'class' => 'form-control','placeholder' => 'Cantidad', 'min' => '0', 'pattern' => '^[0-9]+', 'step' => '1']) }}
                                <label class="form-label" for="b_mil">Cantidad</label>
                            </div>
                            @error('b_mil')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <div>DE $ 1,000</div>
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <span id="t_mil">$0</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 mb-3">
                            <div class="form-outline">
                                {{ Form::number('b_quinientos', old('b_quinientos', $efectivo->b_quinientos), ['id' => 'b_quinientos', 'class' => 'form-control','placeholder' => 'Cantidad', 'min' => '0', 'pattern' => '^[0-9]+', 'step' => '1']) }}
                                <label class="form-label" for="b_quinientos">Cantidad</label>
                            </div>
                            @error('b_quinientos')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <div>DE $ 500</div>
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <span id="t_quinientos">$0</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 mb-3">
                            <div class="form-outline">
                                {{ Form::number('b_doscientos', old('b_doscientos', $efectivo->b_doscientos), ['id' => 'b_doscientos', 'class' => 'form-control','placeholder' => 'Cantidad', 'min' => '0', 'pattern' => '^[0-9]+', 'step' => '1']) }}
                                <label class="form-label" for="b_doscientos">Cantidad</label>
                            </div>
                            @error('b_doscientos')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <div>DE $ 200</div>
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <span id="t_doscientos">$0</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 mb-3">
                            <div class="form-outline">
                                {{ Form::number('b_cien', old('b_cien', $efectivo->b_cien), ['id' => 'b_cien', 'class' => 'form-control','placeholder' => 'Cantidad', 'min' => '0', 'pattern' => '^[0-9]+', 'step' => '1']) }}
                                <label class="form-label" for="b_cien">Cantidad</label>
                            </div>
                            @error('b_cien')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <div>DE $ 100</div>
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <span id="t_cien">$0</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 mb-3">
                            <div class="form-outline">
                                {{ Form::number('b_cincuenta', old('b_cincuenta', $efectivo->b_cincuenta), ['id' => 'b_cincuenta', 'class' => 'form-control','placeholder' => 'Cantidad', 'min' => '0', 'pattern' => '^[0-9]+', 'step' => '1']) }}
                                <label class="form-label" for="b_cincuenta">Cantidad</label>
                            </div>
                            @error('b_cincuenta')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <div>DE $ 50</div>
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <span id="t_cincuenta">$0</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 mb-3">
                            <div class="form-outline">
                                {{ Form::number('b_veinte', old('b_veinte', $efectivo->b_veinte), ['id' => 'b_veinte', 'class' => 'form-control','placeholder' => 'Cantidad', 'min' => '0', 'pattern' => '^[0-9]+', 'step' => '1']) }}
                                <label class="form-label" for="b_veinte">Cantidad</label>
                            </div>
                            @error('b_veinte')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <div>DE $ 20</div>
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <span id="t_veinte">$0</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 mb-3">
                            <div class="form-outline">
                                {{ Form::number('monedas', old('monedas', $efectivo->monedas), ['id' => 'monedas', 'class' => 'form-control','placeholder' => 'Cantidad', 'min' => '0', 'step' => 'any']) }}
                                <label class="form-label" for="monedas">Total</label>
                            </div>
                            @error('monedas')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <div>TOTAL EN MONEDAS</div>
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <span id="t_monedas">$0</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8 mb-3 d-flex align-items-center justify-content-center">
                            <div>TOTAL EFECTIVO</div>
                        </div>

                        <div class="col-4 mb-3 d-flex align-items-center justify-content-center">
                            <span id="t_total">$0</span>
                            {{ Form::hidden('total', old('total', $efectivo->total), ['id' => 'total']) }}
                        </div>
                    </div>

                    {{-- Aquí puedes seguir con más filas como desees --}}
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                            <div class="col-2">
                                <br />
                                {!! Form::button('Guardar', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'submitBtn']) !!}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>





@section('js')
    <script>

        function formatNumber(input) {
            let value = input.value.replace(/[^0-9.]/g, '');

            // Si hay más de un punto, eliminar los extras
            const firstDotIndex = value.indexOf('.');
            if (firstDotIndex !== -1) {
                // Mantener el primer punto, eliminar cualquier otro
                value = value.substring(0, firstDotIndex + 1) + value.substring(firstDotIndex + 1).replace(/\./g, '');
            }

            let parts = value.split('.');
            let integerPart = parts[0];
            let decimalPart = parts[1] ?? '';

            // Eliminar ceros iniciales del entero, pero permitiendo que si todo es 0 no borre
            integerPart = integerPart.replace(/^0+(?=\d)/, '');

            // Formatear la parte entera con comas
            let formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            // Mostrar correctamente si apenas están escribiendo el punto
            input.value = decimalPart !== '' || value.endsWith('.') 
                ? `${formattedInteger}.` + decimalPart 
                : formattedInteger;

            // Actualizar el hidden limpio (sin comas)
            const cleanValue = input.value.replace(/,/g, '');
            document.getElementById('monto').value = cleanValue;
        }

        function fixDecimals(input) {
            let value = input.value.replace(/,/g, '').trim();

            if (value === '' || value === '.') {
                value = '0.00';
            }

            if (value.startsWith('.')) {
                value = '0' + value;
            }

            let parts = value.split('.');
            let integerPart = parts[0];
            let decimalPart = parts[1] || '';

            // Eliminar ceros iniciales
            integerPart = integerPart.replace(/^0+(?=\d)/, '');

            if (decimalPart.length === 0) {
                decimalPart = '00';
            } else if (decimalPart.length === 1) {
                decimalPart = decimalPart + '0';
            } else if (decimalPart.length > 2) {
                decimalPart = decimalPart.slice(0, 2);
            }

            // Formatear parte entera con comas
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            input.value = `${integerPart}.${decimalPart}`;

            // Actualizar el hidden limpio
            document.getElementById('monto').value = input.value.replace(/,/g, '');
        }

        function prepareSubmit() {
            const display = document.getElementById('pp_display').value;
            const cleanValue = display.replace(/,/g, '');
            document.getElementById('b_mil').value = cleanValue;

            return true;
        }

        const denominaciones = {
            b_mil: 1000,
            b_quinientos: 500,
            b_doscientos: 200,
            b_cien: 100,
            b_cincuenta: 50,
            b_veinte: 20,
            monedas: 1 // monedas ya son valor total, no cantidad
        };

        // Formateador de moneda en MXN
        const formatoMoneda = new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN',
            minimumFractionDigits: 2
        });

        function actualizarTotales() {
            let total = 0;

            for (const [campo, valorDenominacion] of Object.entries(denominaciones)) {
                const inputElement = document.getElementById(campo);
                if (!inputElement) continue;

                const cantidad = parseFloat(inputElement.value) || 0;
                const subtotal = campo === 'monedas' ? cantidad : cantidad * valorDenominacion;

                const spanId = 't_' + campo.replace('b_', '');
                const spanElement = document.getElementById(spanId);
                if (spanElement) {
                    spanElement.textContent = formatoMoneda.format(subtotal); // 🟢 Aquí aplicamos el formato
                }

                total += subtotal;
            }

            document.getElementById('t_total').textContent = formatoMoneda.format(total); // 🟢 Total formateado
            document.getElementById('total').value = total.toFixed(2); // Este valor se mantiene sin formato para envío al backend
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar cálculo al cargar
            actualizarTotales();

            // Escuchar cambios en los inputs
            for (const campo of Object.keys(denominaciones)) {
                document.getElementById(campo).addEventListener('input', actualizarTotales);
            }
        });

        $(document).ready(function() {


        });
    </script>


@stop
