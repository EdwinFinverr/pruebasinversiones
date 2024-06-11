@extends('layout.apps')

@section('content')

    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show my-3">
            {{ Session::get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>
                        <span class="text-uppercase"> {{ $error }} </span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div style="padding-top: 50px;"><a data-toggle="modal" data-target="#exampleModal"
                        style="color: white;"><img src="{{ asset('img/Recurso%20232siibal-.png') }}"
                            style="background: left / contain no-repeat, rgba(13,110,253,0);border-style: none;height: 45px;border-top-left-radius: 0px;border-top-right-radius: 0;border-bottom-right-radius: 0;border-bottom-left-radius: 0; "></a>
                    <div class="modal fade" id="#agregarUsuario" tabindex="-1" role="dialog"
                        aria-label="exampleModalLabel" aria>
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-tittle" id="impleModalLabel">
                                    </h5>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-xl-6 text-center mx-auto">
                    <h2><img src="{{ asset('img/Recurso%20231siibal-.png') }}" style="width: 300px;"></h2>

                </div>
                <form class="form-inline ml-3">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" name="search" type="search" placeholder="Search"
                            aria-label="Search"
                            style="border-top-left-radius: 10px;border-top-right-radius: 10px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <br>
                <br>
                <style>
        /* Estilos generales para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        /* Cambia la apariencia de la tabla en pantallas pequeñas */
        @media screen and (max-width: 600px) {
            table {
                font-size: 14px;
            }

            th, td {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            th {
                text-align: center;
            }

            /* Agregar estilos para resaltar encabezados */
            th:nth-child(1), td:nth-child(1) {
                background-color: #f2f2f2;
                font-weight: bold;
            }
        }

        /* Estilo para el encabezado pegajoso en pantallas largas */
        @media screen and (min-width: 768px) {
            th {
                position: sticky;
                top: 0;
                background-color: #f2f2f2;
            }
        }
    </style>
                <table>
                    <thead style="border-top: 5px solid rgb(0,121,198); background: #D1DDE5;">
                        <tr>
                            <th scope="col"style="text-align: center;">Nombre</th>
                            <th scope="col" style="text-align: center;">Correo</th>
                            <th scope="col" style="text-align: center;">Clave Asesor</th>
                            <th scope="col" style="text-align: center;">Estatus</th>
                            <th scope="col" style="text-align: center" style="width:280px">Fecha de baja</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $usuario)
                            <tr>
                                <th scope="row">{{ $usuario->name . '  ' . $usuario->lastName }}</th>
                                <td>{{ $usuario->email }}</td>
                                <td>{{ $usuario->clave_asesor }}</td>
                                <td>
                                    @if ($usuario->estatus == '1')
                                        Activo
                                    @elseif ($usuario->estatus == '2')
                                        Baja
                                    @endif
                                </td>
                                <td>
                                    {{ $usuario->fecha_terminado }}
                                </td>

                                <td>
                                    <div class="nav-item dropdown">
                                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><img src="{{ asset('img/Recurso%20228siibal-.png') }}"
                                                    style="background: left / contain no-repeat, rgba(13,110,253,0);border-style: none;height: 45px;border-top-left-radius: 0px;border-top-right-radius: 0;border-bottom-right-radius: 0;border-bottom-left-radius: 0; "></a>
                                        <div class="dropdown-menu border-light m-0">
                                            <a href="project.html" class="dropdown-item"><form method="POST" action="{{ route('baja', [$usuario->id]) }}"
                                                        style="border-style: none;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" value="Upload"
                                                            style="border-style: none;padding-left: 15px; background: rgba(255,255,255,0)"><img
                                                                src="{{ asset('img/Recurso%20229siibal-.png') }}"
                                                                style="background: left / contain no-repeat, rgba(13,110,253,0);border-style: none;height: 45px;border-top-left-radius: 0px;border-top-right-radius: 0;border-bottom-right-radius: 0;border-bottom-left-radius: 0; "></button>
                                                    </form></a>
                                            <a href="feature.html" class="dropdown-item"><form method="POST" action="{{ route('activo', [$usuario->id]) }}"
                                                        style="border-style: none;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" value="Upload"
                                                            style="border-style: none;padding-left: 15px; background: rgba(255,255,255,0)"><img
                                                                src="{{ asset('img/activo.png') }}"
                                                                style="background: left / contain no-repeat, rgba(13,110,253,0);border-style: none;height: 45px;border-top-left-radius: 0px;border-top-right-radius: 0;border-bottom-right-radius: 0;border-bottom-left-radius: 0; "></button>
                                                    </form></a>
                                        </div>
                                    </div>
                                </td>
                        @endforeach
                    </tbody>
                </table>
                {{ $usuarios->links() }}
            </div>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
                style="border-style: none;">
                <div class="modal-dialog" style="border-style: none;">
                    <div class="modal-content" style="border-style: none; ">
                        <div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                style="font-size: 80px;   position: relative;
                                right: 30px;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-8 col-xl-6 text-center mx-auto">
                                <form class="form-signin" action="{{ url('postRegistrationAsesor') }}" method="POST"
                                    id="regForm" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>
                                                        <span class="text-uppercase"> {{ $error }} </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <div><img></div>
                                    <h2><img class="img-fluid" src="{{ asset('img/Recurso%20231siibal-.png') }}" style="width: 300px;">
                                    </h2>
                                    <div class="mb-3"><span clase="red"
                                            style="color: red;font-size: 25px;">*</span><input
                                            value="{{ old('name') }}" type="text" name="name" id="inputNombre"
                                            placeholder="Nombre (s)"
                                            style="background: #D1DDE5;border-top-left-radius: 10px;border-top-right-radius: 10px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;border: 1px none #c1d7e5;width: 200px;height: 40px;">
                                    </div>
                                    <div class="mb-3"><span clase="red"
                                            style="color: red;font-size: 25px;">*</span><input
                                            value="{{ old('lastName') }}" type="text" id="inputApellidos"
                                            placeholder="Apellido (s)" name="lastName" required autofocus
                                            style="background: #d1dde5;border-top-left-radius: 10px;border-top-right-radius: 10px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;border-width: 1px;border-style: none;width: 200px;height: 40px;">
                                    </div>
                                    <div class="mb-3"><span clase="red"
                                            style=" color: red;font-size: 25px;">*</span><input
                                            value="{{ old('email') }}" type="email" id="inputEmail"
                                            placeholder="Correo electrónico" name="email" required
                                            style="background: #d1dde5;border-top-left-radius: 10px;border-top-right-radius: 10px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;border-width: 1px;border-style: none;width: 200px;height: 40px;">
                                    </div>
                                    <div class="mb-3"><span clase="red"
                                            style=" color: red;font-size: 25px;">*</span><input
                                            value="{{ old('clave_asesor') }}" type="clave_asesor" id="inputClave"
                                            placeholder="Clave del Asesor" name="clave_asesor" required
                                            style="background: #d1dde5;border-top-left-radius: 10px;border-top-right-radius: 10px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;border-width: 1px;border-style: none;width: 200px;height: 40px;">
                                    </div>

                                    <div><button class="btn btn-primary" type="submit" value="Upload" id="submit-btn"
                                            disabled
                                            style="border-style: none;background: none top / contain no-repeat;width: 588px;height: 45px;">
                                            <img src="{{ asset('img/Recurso%20191siibal-.png') }}" style="width: 588px%; height: 45px;"></button>
                                    </div>
                                    <span id="mensaje" style="color: red"></span>
                            
                            </div>
                            </form>
                        </div>
                        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
                                        document.getElementById("inputNombre").addEventListener("input", function() {
                                            this.value = this.value.toUpperCase();
                                        });
                                        document.getElementById("inputApellidos").addEventListener("input", function() {
                                            this.value = this.value.toUpperCase();
                                        });
                                        document.getElementById("inputClave").addEventListener("input", function() {
                                            this.value = this.value.toUpperCase();
                                        });
                                        const inputNombre = document.getElementById('inputNombre');
                                        const inputApellidos = document.getElementById('inputApellidos');
                                        const inputEmail = document.getElementById('inputEmail');
                                        const inputClave = document.getElementById('inputClave');
                                        const inputPassword = document.getElementById('inputPassword');
                                        const inputConfirmPassword = document.getElementById('inputConfirmPassword');
                                        const mensaje = document.getElementById('mensaje');

                                        $(document).ready(function() {
                                            $('#inputNombre, #inputApellidos, #inputEmail, #inputClave').on(
                                                'input',
                                                function() {
                                                    // Verificar si todos los campos de entrada tienen valores
                                                    if ($('#inputNombre').val() && $('#inputApellidos').val() && $('#inputEmail').val() &&
                                                        $('#inputClave').val()
                                                    ) {
                                                        // Habilitar el botón
                                                        $('#submit-btn').prop('disabled', false);
                                                    } else {
                                                        // Deshabilitar el botón
                                                        $('#submit-btn').prop('disabled', true);
                                                    }
                                                });
                                        });
                                    </script>
    @endsection
