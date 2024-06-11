@extends('layout.appcliente')

@section('content')
<!DOCTYPE html>
<html>
<head>
    <!-- Coloca aquí tus enlaces a las hojas de estilo CSS -->
    <link rel="stylesheet" href="tu_archivo_de_estilos.css">
</head>
<body>
    <div class="background-image-container">
        <!-- Contenido de tu página va aquí -->
    </div>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="assets/js/Beautiful-Contact-from-animated.js"></script>
    <script src="assets/js/Smooth-Scrollto-button-read-Description-1.js"></script>
    <script src="assets/js/Smooth-Scrollto-button-read-Description.js"></script>
</body>
</html>
<style>
    /* Estilos para el contenedor de la imagen de fondo */
.background-image-container {
    background-image: url({{ asset('img/AGRADECIMIENTO.jpg') }});
    background-size: cover; /* La imagen de fondo cubrirá todo el contenedor */
    background-position: center; /* La imagen se posicionará en el centro del contenedor */
    background-repeat: no-repeat; /* Evita la repetición de la imagen de fondo */
    min-height: 100vh; /* Establece una altura mínima igual a la altura de la ventana */
}

</style>

@endsection

