@extends('layout.appcliente')

@section('content')

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>inversiones</title>
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/Contact-form-simple.css">
        <link rel="stylesheet" href="assets/css/Hero-Clean-Reverse.css">
        <link rel="stylesheet" href="assets/css/Icon-Input.css">
        <link rel="stylesheet" href="assets/css/Navbar-Right-Links.css">
        <link rel="stylesheet" href="assets/css/styles.css">
    </head>
    <br>
    <div class="container">
        <div id="progress">
            <div class="part on">
                <div class="desc"><a style="color: black"><span>Home</span></div>
                <div class="circle"><span>1</span></div>
            </div>

            <div class="part on">
                <div class="desc"><a style="color: black"><span>Beneficiario</span></a></div>
                <div class="circle"><span>2</span></div>
            </div>
            <div class="part on">
                <div class="desc"><span>Documentos</span></div>
                <div class="circle"><span>3</span></div>
            </div>
            <div class="part">
                <div class="desc"><a style="color: black"><span>Pre contrato</span></a></div>
                <div class="circle"><span>4</span></div>
            </div>
            <div class="part">
                <div class="desc"><a style="color: black"><span>Inversión</span></a></div>
                <div class="circle"><span>5</span></div>
            </div>
            <div class="part">
                <div class="desc"><a style="color: black"><span>Fín</span></a></div>
                <div class="circle"><span>6</span></div>
            </div>
        </div>
    </div>





    <style>
        #progress {
            overflow: hidden;
            padding-bottom: 2em;
            text-align: center;
        }

        #progress .part {
            border-bottom: 3px solid #999;
            float: left;
            margin-bottom: 0.75em;
            position: relative;
            width: 16%;
        }

        #progress .desc {
            height: 1.1875em;
            padding-bottom: 1em;
        }

        #progress .desc span {
            font-size: 0.75em;
        }

        #progress .circle {
            left: 0;
            position: absolute;
            right: 0;
            top: 1.5em;
        }

        #progress .circle span {
            background: none repeat scroll 0 0 #666;
            border-radius: 2em 2em 2em 2em;
            color: #FFFFFF;
            display: inline-block;
            font-size: 0.75em;
            height: 2em;
            line-height: 2;
            width: 2em;
        }

        #progress .step {
            position: absolute;
        }

        #progress .on {
            border-bottom-color: #102940;
        }

        #progress .on .desc {
            font-weight: bold;
        }

        #progress .on .circle span {
            font-weight: bold;
            background-color: #102940;
        }
    </style>
    <div class="contrainer my-5">
        <div class="row">
            <div class="col-lg-10 col-xl-10 mx-auto text-center">
                <div class="card">
                    <div class="row mb-5">
                        @foreach ($usuarios as $usuario)
                            <div class="col-md-8 col-xl-6 text-center mx-auto">
                                <button class="btn btn-primary" id="ayuda"
                                    style="background-color:#999;border-style: none ;width: 200px;height: 80px;"><i
                                        class="fa fa-question-circle"> Tienes duda de tu numero de Identificación Oficial
                                        puedes dar click aqui </i></button>
                                <form class="form-signin" action="{{ Route('update', [$usuario->user_id]) }}" method="POST"
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
                                    @csrf
                                    @method('PUT')
                                    <div><img></div>
                                    <strong>Anexar documentación</strong>
                                    <p style="font-size: 70%">Archivos permitidos (jpeg, png, jpg, gif, pdf) tamaño
                                        máximo 3MB
                                    </p>
                                    <strong style="color: #10406C">"Cargar documentos de forma clara y legible"</strong>
                                    <br>
                                    <div class="container">
                                        <div class="row">
                                            <div>
                                                <label for="idphoto">Identificación Oficial (INE, pasaporte,
                                                    ambos lados)<span clase="red" style=" color: red;">*</span>
                                                </label>
                                                <input type="file" style="overflow: hidden !important; "
                                                    class="form-control-file" name="idphoto" required/>
                                            </div>

                                            <div>
                                                    <label for="idphoto">Identificación
                                                        Oficial (Parte
                                                        trasera)</label>
                                                    <input type="file" style="overflow: hidden !important"
                                                        class="form-control-file" name="idphotoback" />
                                                </div>
                                            <div class="col-md-6"><span clase="red"
                                                    style=" padding-left: 260px; color: red;font-size: 25px;">*</span>
                                                <select class="form-control" name="identificacion" required placeholder="-"
                                                    id="identificacion" required>
                                                    <option value="" disabled selected hidden>Identificación
                                                    </option>
                                                    <option>INE</option>
                                                    <option>Pasaporte</option>
                                                </select>

                                            </div>
                                            <div class="col-md-6">
                                                <span class="red"
                                                    style="padding-left: 290px; color: red; font-size: 25px;">*</span>
                                                <input type="text" id="numero" placeholder="No." name="numero"
                                                    required
                                                    style="background: #d1dde5; border-top-left-radius: 0; border-top-right-radius: 0; border-bottom-right-radius: 0; border-bottom-left-radius: 0; border-width: 1px; border-style: none; width: 300px; height: 40px;">
                                            </div>

                                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                            <script>
                                            $(document).ready(function() {
                                                $('#numero').on('keypress', function(e) {
                                                    var keyCode = e.which;
                                                    if (!(keyCode >= 48 && keyCode <= 57) && // Numeros
                                                        !(keyCode >= 65 && keyCode <= 90) && // Letras mayúsculas
                                                        !(keyCode >= 97 && keyCode <= 122)) { // Letras minúsculas
                                                        e.preventDefault();
                                                    }
                                                });

                                                $('#identificacion').on('change', function() {
                                                    var maxLength = $(this).val() === 'INE' ? 13 : 9;
                                                    $('#numero').attr('maxlength', maxLength);
                                                });

                                                $('#identificacion').trigger('change'); // Initialize with default value
                                            });

                                            document.getElementById("numero").addEventListener("input", function() {
                                                this.value = this.value.toUpperCase();
                                            });
                                        </script>
                                        </div>
                                    </div>
                                    <div><img></div>
                                    <div>
                                        <label for="addressphoto">Imágen de un comprobante de domicilio (luz, gas,
                                            internet, " no mayor a 3 meses") <span clase="red"
                                                style=" color: red;">*</span></label>
                                        <input type="file" style="overflow: hidden !important; padding-left: 30px;"
                                            class="form-control-file" name="addressphoto" required/>

                                    </div>
                                    <div><img></div>
                                    <div>
                                        <label for="fiscalphoto">Constancia de situación fiscal<span clase="red"
                                                style=" color: red;">*</span></label>
                                        <input type="file" style="overflow: hidden !important; padding-left: 30px;"
                                            class="form-control-file" name="fiscalphoto" required/>
                                    </div>

                                    <div>
                                        <label for="estadodecuenta">Estado de cuenta<span clase="red"
                                                style=" color: red;">*</span></label>
                                        <input type="file" style="overflow: hidden !important; padding-left: 30px;"
                                            class="form-control-file" name="estadodecuenta"required />
                                    </div>
                                    <hr>
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-6"> <button class="btn save_btn" type="submit"
                                                    value="Upload" style="">
                                                    <img src="{{ asset('img/Recurso%20226siibal-.png') }}"
                                                        style="background: left / contain no-repeat, rgba(13,110,253,0);border-style: none;height: 45px;border-top-left-radius: 0px;border-top-right-radius: 0;border-bottom-right-radius: 0;border-bottom-left-radius: 0; ">
                                                </button></div>

                                        </div>
                                    </div>

                                    <script>
                                        function toggleImageField() {
                                            var imageField = document.getElementById("addImageField");
                                            if (document.getElementById("addImage").checked) {
                                                imageField.style.display = "block";
                                            } else {
                                                imageField.style.display = "none";
                                            }
                                        }
                                        $(document).ready(function() {
                                            $('#ayuda').click(function() {
                                                $('#modal-ayuda').modal('show');
                                            });
                                        });
                                    </script>
                                </form>
                        @endforeach
                    </div>
                    <div class="modal fade" id="modal-ayuda" tabindex="-1" role="dialog"
                        aria-labelledby="modal-ayuda-label">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-ayuda-label">Identifiación Oficial</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h5 class="modal-title" id="modal-ayuda-label">INE</h5>
                                    <img src="{{ asset('img/INE.png') }}" style="width: 500px;">
                                    <h5 class="modal-title" id="modal-ayuda-label">Pasaporte</h5>
                                    <img src="{{ asset('img/PASSPORT.png') }}" style="width: 500px;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection('content')
