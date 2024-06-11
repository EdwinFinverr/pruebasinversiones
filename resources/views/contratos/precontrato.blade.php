@php(\Jenssegers\Date\Date::setLocale('es'))
@php($fecha_inicio = Jenssegers\Date\Date::parse($inversion->fecha_inicio)->format('l d F Y'))
@php($fecha = Jenssegers\Date\Date::parse($inversion->fecha_inicio)->format('d'))
@php($fecha_termino = Jenssegers\Date\Date::parse($inversion->fecha_termino))
@php($plazo = $fecha_termino->diffInYears($fecha_inicio))
@php($dia = Jenssegers\Date\Date::parse($inversion->fecha_inicio)->day)
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #cintillo {
            background-color: #1a3256;
            color: #ffffff;
            padding: 2px;
            text-align: center;
            border-top-left-radius: 80px;
            border-bottom-right-radius: 80px;
        }

    </style>
    <title>Carátula de inversión </title>
</head>


<body>
<style>
        #cintillo2  {
            background-color: #1573C6;
            color: #ffffff;
            padding: 2px;
            width: 150px;
            text-align: center;
            border-bottom-left-radius:80px;
            border-top-left-radius: 80px;
           
        }
        #dato{
            background-color: #95CEFF;
            color: black;
            padding: 2px;
            width: 200px;
            text-align:left;          
            border-bottom-right-radius: 80px;
            border-top-right-radius: 80px;
        }

        #separador{
            background-color: gray;
            height: 8px;
            border-bottom-right-radius: 80px;
            border-bottom-left-radius: 80px;
            border-top-left-radius: 80px;
            border-top-right-radius: 80px;
            width: 714px;
        }

        #pestaña{

        }
        .interlineado{line-height:15%;}
        
    </style>  

   
    <br>
    <br>

    <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-1" id="cintillo">
                <h4>CARÁTULA DE INVERSIÓN</h4>
        </div>

        <div class="col-md-1">  
        </div>


    </div>
    <br>
    <br>

    <div class="containes">
        <div class="row">
            <div class="col-md-1" id="cintillo2" style="margin-left: 400px;">
                    FOLIO
            </div>
            <div class="col-md-2" id="dato" style=" margin-left: 553px;margin-top: -160px; width: 190px;">
                    {{ $inversion->folio }}
            </div>
        </div>
    </div>
    <div class="interlineado"></div>
    <div class="row">
        <div class="col-md-1">  
        </div>

        <div class="col-md-1" id="cintillo2" style="margin-top: 10px;">
                LUGAR
        </div>

        <div class="col-md-2" id="dato" style="margin-left: 154px;margin-top: -100px; width: 200px;">
                AGUASCALIENTES, AGS. 
        </div>

        <div class="col-md-1" id="cintillo2" style="margin-left: 400px;margin-top: -60px;">
                FECHA
        </div>
        <div class="col-md-2" id="dato" style="margin-left: 553px;margin-top: -160px; width: 190px;">
                {{ $fecha_inicio }}
        </div>

        <div class="col-md-1">  
        </div>
    
    </div>


    </div>

   <br>
        <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-10" id="separador">
        
        </div>

        <div class="col-md-1">  
        </div>


    </div>
    
    <br>

    <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-3" id="pestaña" style="background-color: #f8eebc;
                margin-left: 20px;
            color: black;
            padding: 4px;
            text-align:center;    
            width: 250px;      
            border-top-left-radius: 80px;
            border-top-right-radius: 80px;">
            DATOS DEL CLIENTE
        </div>
        <div class="col-md-7">  
        </div>
        <div class="col-md-1">  
        </div>
    </div>

    <div class="interlineado"></div>

    <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-2" id="cintillo2">
            NOMBRE
        </div>
        <div class="col-md-8" id="dato" style="margin-left: 154px;margin-top: -100px; width: 590px;">  
            {{ $user->name . ' ' . $user->lastName }}
        </div>                          
    </div>

    <div class="interlineado"></div>

    <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-2" id="cintillo2" style="margin-top: 3px;">
            DIRECCIÓN:
        </div>
        <div class="col-md-8" id="dato" style="margin-left: 154px;margin-top: -100px; width: 590px;">  
            {{ $user->address }}
        </div>
  
                
    </div>

    <div class="interlineado"></div>

    <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-1" id="cintillo2" style="margin-top: 3px;width: 100px;">
            CUIDAD:
        </div>
        <div class="col-md-2" id="dato" style="margin-left: 104px;margin-top: -100px; width: 150px;">  
            {{ $user->ciudad }}
        </div>
        <div class="col-md-1" id="cintillo2" style="margin-left: 259px;margin-top: -100px; width: 86px;">
            C.P.:
        </div>
        <div class="col-md-2" id="dato" style="margin-left: 350px;margin-top: -100px; width: 86px;">  
            {{ $user->postalcode }}
        </div>
        <div class="col-md-1" id="cintillo2" style="margin-left: 440px;margin-top: -100px; width: 100px;">
            E-MAIL:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 544px;margin-top: -100px; width: 200px;">  
            {{ $user->email }}
        </div>                
        <div class="col-md-1">  
        </div>                
    </div>

    <div class="interlineado"></div>

    <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-1" id="cintillo2" style="margin-top: 3px;width: 100px;">
            RFC:
        </div>
        <div class="col-md-2" id="dato" style="margin-left: 104px;margin-top: -100px; width: 150px;">  
            {{ $user->rfc }}
        </div>
        <div class="col-md-1" id="cintillo2" style="margin-left: 259px;margin-top: -100px; width: 150px;">
            IDENTIFICACIÓN:
        </div>
        <div class="col-md-2" id="dato" style="margin-left: 413px;margin-top: -100px; width: 120px;">  
            {{ $user->identificacion }}
        </div>
        <div class="col-md-1" id="cintillo2" style="margin-left: 539px;margin-top: -100px; width: 80px;">
            NO:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 623px;margin-top: -100px; width: 120px;">  
            {{ $user->numero }}
        </div>                
        <div class="col-md-1">  
        </div>                
    </div>


    <div class="interlineado"></div>

    <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-1" id="cintillo2"style="margin-top: 3px;">
            TELÉFONO:
        </div>
        <div class="col-md-2" id="dato"style="margin-left: 154px;margin-top: -100px; width: 150px;">  
            {{ $user->telephone }}
        </div>
        <div class="col-md-1" id="cintillo2" style="margin-left: 310px;margin-top: -100px; width: 150px;">
            CELULAR:
        </div>
        <div class="col-md-2" id="dato" style="margin-left: 464px;margin-top: -100px; width: 150px;">  
            {{ $user->telephone }}
        </div>              
        <div class="col-md-1">  
        </div>                
    </div>

                
            </div>
        
    <br>
    

    <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-3" id="pestaña" style="background-color: #f8eebc;
                margin-left: 20px;
            color: black;
            padding: 4px;
            text-align:center;    
            width: 250px;      
            border-top-left-radius: 80px;
            border-top-right-radius: 80px;">
            DATOS DEL PROYECTO
        </div>
        <div class="col-md-7">  
        </div>
        <div class="col-md-1">  
        </div>
    </div>
    @foreach ($proyecto as $proyectos)
    <div class="interlineado"></div>
     <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-2" id="cintillo2" style="width: 180px;">
            NOMBRE:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 184px;margin-top: -100px; width: 185px;">  
            {{ $proyectos->proyecto }}
        </div>
        <div class="col-md-2" id="cintillo2" style="margin-left: 374px;margin-top: -100px; width: 180px;">
            CUIDAD:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 558px;margin-top: -100px; width: 185px;">  
            {{ $proyectos->ciudad }}
        </div>                
        <div class="col-md-1">  
        </div>                
    </div>
    @endforeach
    <div class="interlineado"></div>
     <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-2" id="cintillo2" style="margin-top: 3px;width: 220px;">
            VALOR DE LA INVERSIÓN:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 224px;margin-top: -100px; width: 146px;">  
            {{ $inversion->cantidad }}
        </div>
        <div class="col-md-2" id="cintillo2" style="margin-left: 375px;margin-top: -100px; width: 280px;">
            PORCENTAJE MINIMO MENSUAL:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 659px;margin-top: -100px; width: 84px;">  
            {{ $inversion->tasa_mensual }}%
        </div>                
        <div class="col-md-1">  
        </div>                
    </div>    
        
    <div class="interlineado"></div>
     <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-2" id="cintillo2" style="margin-top: 3px;width: 180px;">
            NÚMERO DE CUENTA:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 184px;margin-top: -100px; width: 184px;">  
            {{ $inversion->cuenta_transferencia }}
        </div>
        <div class="col-md-2" id="cintillo2" style="margin-left: 374px;margin-top: -100px; width: 180px;">
            BANCO:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 558px;margin-top: -100px; width: 185px;">  
            {{ $bancoNombre }}
        </div>                
        <div class="col-md-1">  
        </div>                
    </div>                  
        
 <br>
   

    <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-3" id="pestaña" style="background-color: #f8eebc;
                margin-left: 20px;
            color: black;
            padding: 4px;
            text-align:center;    
            width: 250px;      
            border-top-left-radius: 80px;
            border-top-right-radius: 80px;">
            DATOS DE LA INVERSIÓN
        </div>
        <div class="col-md-7">  
        </div>
        <div class="col-md-1">  
        </div>
    </div>

    <div class="interlineado"></div>
     <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-2" id="cintillo2" style="width: 180px;">
            PLAZO DE INVERSIÓN:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 184px;margin-top: -100px; width: 184px;">  
            {{ $plazo }} años
        </div>
        <div class="col-md-2" id="cintillo2" style="margin-left: 374px;margin-top: -100px; width: 200px;">
            DÍA DE PAGO MENSUAL:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 578px;margin-top: -100px; width: 160px;">  
            {{ $fecha }}
        </div>                
        <div class="col-md-1">  
        </div>                
    </div>  
    @foreach ($beneficiarios as $beneficiario)
    <div class="interlineado"></div>
     <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-2" id="cintillo2" style="margin-top: 3px;width: 140px;">
            BENEFICIARIO:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 144px;margin-top: -100px; width: 224px;">  
            {{ $beneficiario->name }}
                    {{ $beneficiario->lastName }}-
        </div>
        <div class="col-md-2" id="cintillo2" style="margin-left: 374px;margin-top: -100px; width: 200px;">
            PORCENTAJE:
        </div>
        <div class="col-md-3" id="dato" style="margin-left: 578px;margin-top: -100px; width: 160px;">  
            {{ $beneficiario->porcentaje }}%
        </div>                
        <div class="col-md-1">  
        </div>                
    </div>  
    @endforeach
    <br>
        <div class="row">
        <div class="col-md-1">  
        </div>
        <div class="col-md-10" id="separador">
        
        </div>

        <div class="col-md-1">  
        </div>


    </div>
    
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
