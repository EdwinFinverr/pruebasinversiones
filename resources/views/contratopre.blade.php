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
                <div class="desc"><a style="color: black"><span>Documentos</span></a></div>
                <div class="circle"><span>3</span></div>
            </div>
            <div class="part on">
                <div class="desc"><span>Pre contrato</span></div>
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
                    <div class="card-body">
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
                        <h2 class="card-title">Ya casi está listo</h2>
                        <div class="row">
                            <div
                                class="col-lg-8 offset-lg-2 col-md-12 col-sm-12 d-flex flex-column justify-content-center my-3 ">
                                <p class="card-text">A continuación tienes la carátula de inversión para la revisión de tu información</p>
                                <p class="card-text">El contrato final se te enviará a tu correo cuando se apruebe tu
                                    comprobante, para firmarlo.</p>
                                <a type="submit" href="{{ route('customer.printpdfpre', Session::get('inversion')) }}"
                                    target="_blank" class="btn btn-outline-secondary">Ver Carátula</a>
                                <br>

                                <form action="{{ route('postContratopre') }}" method="post">
                                    @csrf

                                    <button type="submit" class="btn "><img
                                            src="{{ asset('img/Recurso%20225siibal-.png') }}"
                                            style="background: left / contain no-repeat, rgba(13,110,253,0);border-style: none;height: 45px;border-top-left-radius: 0px;border-top-right-radius: 0;border-bottom-right-radius: 0;border-bottom-left-radius: 0; "></button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection('content')
