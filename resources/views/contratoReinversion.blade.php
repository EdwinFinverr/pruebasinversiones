@extends('layout.appcliente')

@section('content')
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-10 col-xl-10 mx-auto text-center">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>
                                            <span class="text-uppercase">{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <h5 class="card-title">Ya casi está listo</h5>
                        <p class="card-text">A continuación tienes el contrato que te respaldará como cliente</p>
                        <a href="{{ route('reinversion.pdf', Session::get('inversion')) }}" target="_blank"
                            class="btn btn-outline-secondary">Ver Contrato</a>
                        <form action="{{ route('postContrato') }}" method="post">
                            @csrf
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="contrato" id="aceptarContraro">
                                <label class="form-check-label" for="aceptarContraro">
                                    He leído y acepto los términos y condiciones del contrato
                                </label>
                            </div>
                            <div id="password-loader" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i> Validando contraseña...
                            </div>
                            <div>
                                <label for="password-input">Ingrese su contraseña:</label>
                                <input type="password" name="password" id="password-input">
                                <div id="password-error" class="text-danger"
                                    style="border-top-left-radius: 10px;border-top-right-radius: 10px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary my-2" id="btn-contrato" disabled>Firmar
                                contrato</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Font Awesome library -->
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>

    <script>
        const passwordInput = document.getElementById('password-input');
        const contratoButton = document.getElementById('btn-contrato');
        const passwordError = document.getElementById('password-error');
        const passwordLoader = document.getElementById('password-loader');

        let validationTimeout; // Variable para almacenar el timeout

        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value;

            if (password.length > 0) {
                // Mostrar el mensaje "Validando contraseña..."
                passwordLoader.style.display = 'block';

                // Cancelar cualquier timeout existente
                clearTimeout(validationTimeout);

                // Iniciar el timeout para verificar la contraseña después de 1 segundo
                validationTimeout = setTimeout(function() {
                    // Realizar una llamada AJAX al servidor para validar la contraseña en tiempo real
                    axios.post('{{ route('validar-contrasena') }}', {
                            password: password
                        })
                        .then(function(response) {
                            // La respuesta del servidor debe indicar si la contraseña es válida o no
                            const isValidPassword = response.data.isValid;

                            if (isValidPassword) {
                                contratoButton.disabled = false;
                                passwordError.textContent = ''; // Limpiar mensaje de error
                                passwordLoader.style.display =
                                    'none'; // Ocultar el mensaje "Validando contraseña..."
                            } else {
                                contratoButton.disabled = true;
                                passwordError.textContent = 'Contraseña incorrecta';
                                passwordLoader.style.display =
                                    'none'; // Ocultar el mensaje "Validando contraseña..."
                            }
                        })
                        .catch(function(error) {
                            console.error(error);
                            passwordError.textContent = 'Error al validar la contraseña';
                            passwordLoader.style.display =
                                'none'; // Ocultar el mensaje "Validando contraseña..."
                        });
                }, 1000);
            } else {
                // Limpiar el mensaje de error y ocultar el mensaje "Validando contraseña..."
                passwordError.textContent = '';
                passwordLoader.style.display = 'none';

                // Cancelar cualquier timeout existente
                clearTimeout(validationTimeout);
            }
        });
    </script>
@endsection
