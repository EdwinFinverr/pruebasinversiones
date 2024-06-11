@php(\Jenssegers\Date\Date::setLocale('es'))
@php($fecha_inicio = Jenssegers\Date\Date::parse($inversion->fecha_inicio)->format('l d F Y'))
@php($terminado = Jenssegers\Date\Date::parse($inversion->fecha_termino)->format('l d F Y'))
@php($fecha = Jenssegers\Date\Date::parse($inversion->fecha_inicio)->format('d'))
@php($fecha_termino = Jenssegers\Date\Date::parse($inversion->fecha_termino))
@php($plazo = $fecha_termino->diffInYears($fecha_inicio))
@php($dia = Jenssegers\Date\Date::parse($inversion->fecha_inicio)->day)
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Adendum</title>
</head>


<body>
    <h2 class="text-center"
        style="background: rgb(36,53,78);border-top-left-radius: 29px;border-bottom-right-radius: 29px;color: #ffffff;">
        ADENDUM</h2>
    <div>
        <p class="text-justify
    text-break mt-5">ADENDUM AL CONTRATO DE MUTUO CON INTERESES DE FECHA <span
                class="text-uppercase">{{ $fecha_inicio }}</span> IDENTIFICADO CON EL NÚMERO DE
            CONTRATO
            P{{ $inversion->empresa_inversion_id == 1 ? 'C' : 'F' }} - {{ $inversion->folio }}
            , QUE CELEBRAN POR UNA
            PARTE
            <span class="text-uppercase">{{ $empresa->nombre }}</span>, REPRESENTADA EN ESTE
            ACTO POR {{ $inversion->empresa_inversion_id == 1 ? 'MARIA MONSERRAT VILLEGAS MOJARRO' : 'JOSÉ ALFREDO VILLEGAS MOJARRO' }} “MUTUARIO”, Y POR LA OTRA
            <span class="text-muted text-uppercase">{{ $user->name . ' ' . $user->lastName }}</span>, POR SU PROPIO
            DERECHO COMO “MUTUANTE”.
        </p>
        <h3 class="h3 text-center">ANTECEDENTES</h3>
        <p class="text-justify text-break">I.- En fecha {{ $fecha_inicio }} “LAS PARTES” celebraron un Contrato de Mutuo
            con intereses
            identificado con el número de contrato P{{ $inversion->empresa_inversion_id == 1 ? 'C' : 'F' }} -
            {{ $inversion->folio }}, en el cual, se ostentaron con el mismo carácter
            que el mencionado en el proemio del presente.
        </p>
        <h3 class="h3 text-center">DECLARACIONES</h3>
        <p class="text-justify text-break">I.- Declara el “LAS PARTES”: </p>
        <p class="text-justify text-break">a. Que reconocen la existencia del un Contrato de Mutuo con Intereses
            identificado con el número
            de contrato P{{ $inversion->empresa_inversion_id == 1 ? 'C' : 'F' }} -
            {{ $inversion->folio }}
        </p>
        <p class="text-justify text-break">b. Que han llegado a un acuerdo para modificar la Cláusula 2.- del Contrato
            Principal de Mutuo
            con Intereses con la intención de ampliar la vigencia de este, por lo que es su voluntad otorgar
            el presente Adendum. </p>
        <p class="text-justify text-break">c. Que el presente Adendum no representa una novación en las obligaciones del
            Contrato
            Principal de Mutuo con Intereses, por lo que se obligan a continuar cumpliéndolas en todos sus
            términos y en las condiciones establecidas en este.
        </p>
        <p class="text-justify text-break">d. Que se reconocen mutuamente la capacidad legal y personalidades en los
            términos más
            amplios, mismas que al suscribir el presente Adendum no les han sido revocadas, modificadas,
            ni limitadas de ninguna forma.
        </p>
        <p class="text-justify text-break">e. Que están de acuerdo con que la intención del presente Adendum es la de
            que el “CLIENTE”
            Reinvierta el Capital del Contrato Principal de Mutuo con Intereses con el “PRESTADOR”.
        </p>
        <p class="text-justify text-break">f. Que en otorgamiento del presente no existe dolo, error, mala fe, lesión o
            alguna causa que
            pudiere invalidar o anular el presente Adendum por lo que es su deseo sujetarse a lo estipulado
            en las siguientes:
            .</p>
        <br>
        <br>
        <br>
        <h3 class="h3 text-center">CLAUSULAS</h3>
        <p class="text-justify text-break">1.- “LAS PARTES” acuerdan que toda controversia e interpretación que se
            derive
            de este
            Adendum, respecto de su operación, formalización y cumplimiento, será resuelta conforme lo
            estipulado en el Contrato Principal de Mutuo con Intereses de {{ $fecha_inicio }} identificado
            con el número de Contrato P{{ $inversion->empresa_inversion_id == 1 ? 'C' : 'F' }} -
            {{ $inversion->folio }}. </p>
        <p class="text-justify text-break">2.- “LAS PARTES” acuerdan la modificación en la Cláusula 2.- del Contrato
            Principal de
            Mutuo con Intereses respecto a la vigencia de este para quedar de la siguiente manera:
        </p>
        <p class="text-justify text-break">“2. Este contrato se celebra por un tiempo determinado, el cual
            consecuentemente
            tendrá una vigencia y será obligatoria para las partes en el periodo acordado por @if ($plazo == 4)
                el tiempo necesario para finalizar el proyecto
            @elseif($plazo == 3)
                3 años
            @elseif($plazo == 2)
                2 años
            @else
                1 año
            @endif entre PRESTADOR y CLIENTE con las modalidades que se relacionan y
            refieren en la información proporcionada en la página https:\\plataforma.finverr.com”
        </p>
        <p class="text-justify text-break">Partiendo de lo anterior, “LAS PARTES” acuerdan que la vigencia del Contrato
            de Mutuo con
            Intereses de {{ $fecha_inicio }} identificado con el número de contrato
            P{{ $inversion->empresa_inversion_id == 1 ? 'C' : 'F' }} -
            {{ $inversion->folio }} se amplía
            hasta el {{ $terminado }}
        </p>
        <p class="text-justify text-break">3.- “LAS PARTES” acuerdan que toda controversia e interpretación que se
            derive
            de este
            Adendum, respecto de su operación, formalización y cumplimiento, será resuelta conforme lo
            estipulado en el Contrato Principal de Mutuo con Intereses de fecha {{ $fecha_inicio }} identificado
            con el número de Contrato P{{ $inversion->empresa_inversion_id == 1 ? 'C' : 'F' }} -
            {{ $inversion->folio }}
        </p>
        <p class="text-justify text-break">4.- El presente Adendum no representa una novación en las obligaciones en el
            Contrato
            Principal de Mutuo con Intereses, por lo que “LAS PARTES” siguen obligadas en los términos y las
            condiciones anteriormente establecidas en el Contrato Principal.
        </p>
        <p class="text-justify text-break">5.- El presente Adendum empezará a surtir sus efectos, al momento de la
            firma
            del presente,
            hasta la fecha de terminación del Contrato Principal de Subarrendamiento, es decir, el {{ $terminado }}.
        </p>
        <p class="text-justify text-break">6.- “LAS PARTES” establecen que para la Celebración y firma de conformidad
            del presente
            Adendum se realizará electrónicamente en la plataforma digital http://plataforma.finverr.com/ dando
            “CLICK” al botón de “FIRMAR CONTRATO” en el documento digital de Términos y Condiciones, el
            cual, se encuentra en la plataforma digital en mención para su consulta y firma.
        </p>
        <p class="text-justify text-break">Leído que fue por las partes y enteradas de su contenido y alcance legal, se
            firma el presente
            Adendum por duplicado, otorgándose un tanto para cada una de “LAS PARTES”, ante dos testigos,
            en Aguascalientes, Ags, el {{ $fecha_inicio }}
        </p>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>
