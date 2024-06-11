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
        <p class="text-justify text-break">b. Que han llegado a un acuerdo para modificar la Cláusula 2.- y 4.- del 
            Contrato con la intención ampliar la vigencia y de modificar la cantidad otorgada en mutuo, 
            por lo que es su voluntad otorgar el presente Adendum.</p>
        <p class="text-justify text-break">c. Que el presente Adendum no representa una novación en las obligaciones del
            Contrato
            Principal de Mutuo con Intereses, por lo que se obligan a continuar cumpliéndolas en todos sus
            términos y en las condiciones establecidas en este.
        </p>
        <p class="text-justify text-break">d. Que se reconocen mutuamente la capacidad legal y personalidad para la 
            celebración del presente Adendum, mismas que no les han sido revocadas, modificadas, ni limitadas
             de ninguna forma.
        </p>
        <p class="text-justify text-break">e. Que el “PRESTADOR” se encuentra al corriente de los pagos de los
            rendimientos estipulados en el Contrato Principal.
        </p>
        <p class="text-justify text-break">f. Que la intención del presente Adendum es que el “CLIENTE” Reinvierta 
            el Capital del Contrato con el “PRESTADOR.</p>
        <p class="text-justify text-break">g. Que en otorgamiento del presente no existe dolo, error, mala fe, lesión o
            alguna causa que
            pudiere invalidar o anular el presente Adendum por lo que es su deseo sujetarse a lo estipulado
            en las siguientes:</p>
        <h3 class="h3 text-center">CLAUSULAS</h3>
        <p class="text-justify text-break">1.- El objeto del presente Adendum es el de modificar las Cláusulas 4.- del
            Contrato Principal de Mutuo con Intereses de fecha {{ $fecha_inicio }} identificado
            con el número de Contrato P{{ $inversion->empresa_inversion_id == 1 ? 'C' : 'F' }} -
            {{ $inversion->folio }} relativa al capital Objeto del Contrato. </p>
        <p class="text-justify text-break">2.- “LAS PARTES” acuerdan la modificación en la Cláusula 4.- del Contrato
            Principal de Mutuo con Intereses respecto al capital de este para quedar de la siguiente manera:
        </p>
        <p class="text-justify text-break">“2.-Este contrato se celebra por un tiempo determinado, el cual 
            consecuentemente tendrá una vigencia y será obligatoria para las partes en el periodo acordado por xx 
            AÑO entre PRESTADOR y CLIENTE con las modalidades que se relacionan y refieren en la información proporcionada
             en la página https::\\plataforma.finverr.com.”
        </p>
        <p class="text-justify text-break">“4.- El PRESTADOR se obliga a aceptar del CLIENTE un préstamo para inversión
            por la cantidad de {{ $inversion->cantidad }} ({{ $inversion->cantidad }} PESOS 00/100 M.N.) para invertir
            en los proyectos que se encuentran
            desarrollándose del PRESTADOR (Finverr Corporativo Global S.A. de C.V.) y en consecuencia el CLIENTE no
            podrá incidir de forma alguna en los proyectos ni variar éstos en lo sustantivo o accidental y que se
            encuentran dentro de las metas que le corresponden al PRESTADOR. Así mismo, a partir de la firma del
            contrato de manera digital se tendrá 01 día hábil para realizar el depósito o trasferencia bancaria del
            monto establecido por el Usuario mismo en el Contrato Mutuo con Interés suscrito con EL PRESTADOR, de lo
            contrario tal contrato quedará nulificado completamente, sin perjuicio para Finverr Corporativo Global S.A.
            de C.V., en este sentido el CLIENTE deberá adjuntar en la página web mencionada el ticket, comprobante de
            transferencia o depósito bancario según corresponda.”
        </p>
        <p class="text-justify text-break">Partiendo de lo anterior, “LAS PARTES” acuerdan que el capital Objeto del
            Contrato de Mutuo con Intereses de fecha {{ $fecha_inicio }} identificado con el número de contrato
            P{{ $inversion->empresa_inversion_id == 1 ? 'C' : 'F' }} -
            {{ $inversion->folio }} ha sufrido un decremento por lo que ahora es de {{ $inversion->cantidad }}
            ({{ $inversion->cantidad }} PESOS 00/100 M.N.).
        </p>
        <p class="text-justify text-break">3.- Para la actualización del capital Objeto del Contrato de Mutuo el
            “PRESTADOR” realizará la devolución de la cantidad de ${{ $diferenciaFormateada }}
            (${{ $diferenciaFormateada }} PESOS 00/100 M.N.) al “CLIENTE” mediante vía
            transferencia electrónica dentro de los siguientes treinta días posteriores a la firma del presente Adendum.
        </p>

        <p class="text-justify text-break">4.- “LAS PARTES” acuerdan que toda controversia e interpretación que se
            derive de este Adendum, respecto de su operación, formalización y cumplimiento, será resuelta conforme lo
            estipulado en el Contrato Principal de Mutuo con Intereses de fecha {{ $fecha_inicio }} identificado con el
            número de contrato
            P{{ $inversion->empresa_inversion_id == 1 ? 'C' : 'F' }} -
            {{ $inversion->folio }}.
        </p>
        <p class="text-justify text-break">5.- El presente Adendum no representa una novación en las obligaciones en el
            Contrato Principal de Mutuo con Intereses, por lo que “LAS PARTES” siguen obligadas en los términos y las
            condiciones anteriormente establecidas en el Contrato Principal.
        </p>
        <p class="text-justify text-break">6.- El presente Adendum empezará a surtir sus efectos, al momento de la firma
            del presente,
            hasta la fecha de terminación del Contrato Principal de Subarrendamiento, es decir, el {{ $terminado }}.
        </p>
        <p class="text-justify text-break">7.- “LAS PARTES” establecen que para la Celebración y firma de conformidad
            del
            presente Adendum se realizará electrónicamente en la plataforma digital http://plataforma.finverr.com/ dando
            “CLICK” al botón de “FIRMAR ADENDUM” en el documento digital de Términos y Condiciones, el cual, se
            encuentra en la plataforma digital en mención para su consulta y firma.
        </p>
        <p class="text-justify text-break">“LAS PARTES” manifiestan que conocen y aprueban el contenido y alcances de
            este instrumento por lo que hace “CLICK” en el recuadro de aceptación (“FIRMAR ADENDUM”), como prueba eficaz
            de su pleno y total consentimiento, expresando que en él no ocurre violencia física o moral, coacción o
            vicio alguno de la voluntad.
        </p>
        <p class="text-justify text-break"> Aguascalientes, Ags, el {{ $fecha_inicio }}
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
