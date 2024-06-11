<?php

namespace App\Http\Controllers;

use App\Models\Empresa_inversion;
use App\Models\InversionCliente;
use App\Models\User;
use App\Models\Beneficiarios;
Use App\Models\Proyecto;
Use App\Models\Banco;
Use App\Models\Procedencia;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use PDF;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use Money\Currency;


class pdfController extends Controller
{
    public function crearData($años, $empresa, $user, $cantidad, $dt, $day, $tipo_contrato,$proyecto,$beneficiarios)
    {
        switch ($años) {
            case 1:
                $plazo = '1 año';
                break;
            case 2:
                $plazo = '2 años';
                break;
            case 3:
                $plazo = '3 años';
                break;
            case 4:
                $plazo = '4 años';
                 break;
            default:
                $plazo = 'Contrato Invalido';
                break;
        }
        $data = [
            'title' => 'Contrato de ' . $user->name,
            'nombre' => $user->name . ' ' . $user->lastName,
            'empresa' => $empresa->nombre,
            'titular' => $empresa->titular,
            'fecha_notaria' => $empresa->Fecha_notaria,
            'numero_escritura' => $empresa->numero_escritura,
            'folio_mercantil' => $empresa->folio_mercantil,
            'credencial_elector' => $empresa->credencial_elector,
            'testimonio_notarial' => $empresa->testimonio_notarial,
            'rfc_empresa' => $empresa->RFC_empresa,
            'plazo' => $plazo,
            'cantidad' => $cantidad,
            'fecha' => $dt,
            'dia' => $day,
            'tipo_contrato' => $tipo_contrato,
            'proyecto' => $proyecto->proyecto,
            'ciudad' => $proyecto->ciudad,
            'valor' => $proyecto->valor,
            'porcentaje' => $proyecto->porcentaje,
            'name' => $beneficiarios->name,
        ];
        return $data;
    }

    public function convertirNumeroALetras($numero) {
        $unidad = array('', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve');
        $decena = array('diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciseis', 'diecisiete', 'dieciocho', 'diecinueve');
        $decenas = array('', '', 'veinti', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa');
        $centenas = array('', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos');
    
        $numero = (float)$numero;
        $entero = floor($numero);
        $decimal = round(($numero - $entero) * 100);
        $cadena = '';
    
        if ($entero == 0) {
            $cadena = 'cero';
        } elseif ($entero == 1) {
            $cadena = 'un';
        } elseif ($entero >= 1000) {
            $cadena = $this->convertirNumeroALetras($entero / 1000) . ' mil ' . $this->convertirNumeroALetras($entero % 1000);
        } elseif ($entero >= 100) {
            $cadena = $centenas[(int)($entero / 100)] . ' ' . $this->convertirNumeroALetras($entero % 100);
        } elseif ($entero >= 20) {
            $cadena = $decenas[(int)($entero / 10)] . ' ' . $unidad[$entero % 10];
        } elseif ($entero >= 10) {
            $cadena = $decena[$entero - 10];
        } else {
            $cadena = $unidad[$entero];
        }
    
        if ($decimal > 0) {
            $cadena .= ' punto ' . ($decimal < 10 ? 'cero ' : '') . $this->convertirNumeroALetras($decimal);
        }
    
        return $cadena;
    }

    public function printPDF()
{
    $userid = Auth::user()->id;
    // Obtener la inversión más reciente de la base de datos
    $inversionDB = InversionCliente::where('user_id', $userid)
    ->latest()
    ->first();

    // Obtener la inversión de la sesión
    $inversionSesion = session()->get('inversion');

    // Combinar ambas inversiones usando el operador de fusión de null
    $inversion = $inversionDB ?? $inversionSesion;
    $proyecto = proyecto::all();
    $beneficiarios = Beneficiarios::join('beneficiario_inversi', 'beneficiarios.id', '=', 'beneficiario_inversi.id_beneficiario')
        ->select('beneficiarios.name', 'beneficiarios.lastName', 'beneficiarios.relationship', 'beneficiario_inversi.porcentaje')
        ->where('beneficiarios.user_id', '=', $userid)
        ->get();
    $empresa = Empresa_inversion::where('id', $inversion->empresa_inversion_id)->first();
    $user = User::where('users.id', Auth::id())
        ->rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
        ->first();
    $clabe = substr($inversion->cuenta_transferencia, 0, 3);
    $banco = Banco::where('clave', $clabe)->first();
    $bancoNombre = $banco ? $banco->banco_nombre : 'Banco Desconocido';

    // Lógica condicional para determinar qué vista cargar
    $viewName = ($userid == 367) ? 'contratos.contratoreymundo' : 'contratos.inversionV1';
    
    // Limpiar el número de cualquier carácter no numérico
    $montoNumerico = preg_replace('/[^0-9.]/', '', $inversion->cantidad);
    
    // Convertir el monto a letras
    $montoLetras = $this->convertirNumeroALetras($montoNumerico);

    // Cargar la vista correcta
    $pdf = PDF::loadView($viewName, compact('inversion', 'empresa', 'user', 'proyecto', 'beneficiarios', 'bancoNombre', 'montoLetras'));

    // Generar el nombre del archivo
    $nombreCliente = Str::lower($user->name); // Convertir el nombre del cliente a minúsculas
    $folioContrato = $inversion->folio;
    $filename = 'contrato' . $nombreCliente . $folioContrato . '.pdf';

    // Obtener la ruta completa del directorio de destino
    $directorioDestino = 'C:\Users\Admin\contratos';

    // Verificar si el directorio de destino existe, si no, crearlo
    // (Puedes agregar la lógica para crear el directorio aquí si aún no lo has hecho)

    // Devolver el PDF como una respuesta para su visualización en el navegador
    return $pdf->stream($filename);
}

    
    public function printPDFpre()
    {
        $userid = Auth::user()->id;
        // Obtener la inversión más reciente de la base de datos
        $inversionDB = InversionCliente::where('user_id', $userid)
        ->latest()
        ->first();

        // Obtener la inversión de la sesión
        $inversionSesion = session()->get('inversion');

        // Combinar ambas inversiones usando el operador de fusión de null
        $inversion = $inversionDB ?? $inversionSesion;
        $proyecto = proyecto::all();
        $beneficiarios = Beneficiarios::join('beneficiario_inversi', 'beneficiarios.id', '=', 'beneficiario_inversi.id_beneficiario')
            ->select('beneficiarios.name', 'beneficiarios.lastName', 'beneficiarios.relationship', 'beneficiario_inversi.porcentaje')
            ->where('beneficiarios.user_id', '=', $userid)
            ->get();
        $empresa = Empresa_inversion::where('id', $inversion->empresa_inversion_id)->first();
        $user = User::where('users.id', Auth::id())
            ->rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
            ->first();
        $clabe = substr($inversion->cuenta_transferencia, 0, 3);
        $banco = Banco::where('clave', $clabe)->first();
        $bancoNombre = $banco ? $banco->banco_nombre : 'Banco Desconocido';
    
        // Lógica condicional para determinar qué vista cargar
        $viewName = 'contratos.precontrato';
    
        // Cargar la vista correcta
        
        $pdf = PDF::loadView($viewName, compact('inversion', 'empresa', 'user', 'proyecto', 'beneficiarios', 'bancoNombre'));
    
        // Generar el nombre del archivo
        $nombreCliente = Str::lower($user->name); // Convertir el nombre del cliente a minúsculas
        $folioContrato = $inversion->folio;
        $filename = 'contrato' . $nombreCliente . $folioContrato . '.pdf';
    
        // Obtener la ruta completa del directorio de destino
        $directorioDestino = 'C:\Users\Admin\contratos';
    
        // Verificar si el directorio de destino existe, si no, crearlo
        // (Puedes agregar la lógica para crear el directorio aquí si aún no lo has hecho)
    

        // Devolver el PDF como una respuesta para su visualización en el navegador
        return $pdf->stream($filename);
    }



    public function printContractPDF()
    {
        $userid = Auth::user()->id;

        $inversion = InversionCliente::where('user_id', $userid)
            ->latest()
            ->first();

        $proyecto = Proyecto::all();

        $beneficiarios = Beneficiarios::join('beneficiario_inversi', 'beneficiarios.id', '=', 'beneficiario_inversi.id_beneficiario')
            ->select('beneficiarios.name', 'beneficiarios.lastName', 'beneficiarios.relationship', 'beneficiario_inversi.porcentaje')
            ->where('beneficiarios.user_id', '=', $userid)
            ->get();

        $user = User::where('users.id', Auth::id())
            ->rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
            ->first();

        $empresa = Empresa_inversion::where('id', $inversion->empresa_inversion_id)->first();
        $clabe = substr($inversion->cuenta_transferencia, 0, 3);
        $banco = Banco::where('clave', $clabe)->first();
        $bancoNombre = $banco ? $banco->banco_nombre : 'Banco Desconocido';

        $pdf = PDF::loadView('contratos.contratodaniel', compact('inversion', 'empresa', 'user', 'proyecto', 'beneficiarios', 'bancoNombre'));
        $nombreCliente = Str::lower($user->name); // Convertir el nombre del cliente a minúsculas
        $folioContrato = $inversion->folio;
        $filename = 'recontrato' . $nombreCliente . $folioContrato . '.pdf';

        // Obtener la ruta completa del directorio de destino
        $directorioDestino = 'C:\Users\Admin\contratos';

        // Verificar si el directorio de destino existe, si no, crearlo
        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        // Guardar una copia del contrato en el directorio de destino
        $pdf->save($directorioDestino . '\\' . $filename);

        return $pdf->stream('Contrato.pdf');
    }



    public function printPDFadmin($id_inversion)
    {
        $proyecto = proyecto::all();
    $inversion = InversionCliente::where('id', $id_inversion)->first();
    $userId = $inversion->user_id;
    $user = User::where('users.id', $userId)
        ->rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
        ->first();
    $empresa = Empresa_inversion::where('id', $inversion->empresa_inversion_id)->first();
    
    // Obtenemos los beneficiarios del cliente asociados a la inversión
    $beneficiarios = Beneficiarios::join('beneficiario_inversi', 'beneficiarios.id', '=', 'beneficiario_inversi.id_beneficiario')
        ->select('beneficiarios.name', 'beneficiarios.lastName', 'beneficiarios.relationship', 'beneficiario_inversi.porcentaje')
        ->where('beneficiario_inversi.id_inversion', '=', $id_inversion)
        ->get();

        switch ($inversion->contrato_inversion_id){
            case 1:
                $proyecto = proyecto::all();
                $userid = Auth::user()->id;
                $beneficiarios = Beneficiarios::join('beneficiario_inversi', 'beneficiarios.id', '=', 'beneficiario_inversi.id_beneficiario')
        ->select('beneficiarios.name', 'beneficiarios.lastName', 'beneficiarios.relationship', 'beneficiario_inversi.porcentaje')
        ->where('beneficiarios.user_id', '=', $userid)
        ->get();
                $dt = Date::parse($inversion->fecha_inicio)->format('l d F Y');
                $day = Date::parse($inversion->fecha_inicio)->format('d');
                $años = (Carbon::parse($inversion->fecha_termino))->diffInYears(Carbon::parse($inversion->fecha_inicio));
                $cantidad = $inversion->cantidad;
                $tipo_contrato = $inversion->contrato_inversion_id;
                $data = Self::crearData($años, $empresa, $user, $cantidad, $dt, $day, $tipo_contrato,$proyecto,$beneficiarios);
                $pdf = PDF::loadView('pdf_view1', $data);
                return $pdf->stream('Contrato.pdf');
                break;
            case 5 :
                $clabe = substr($inversion->cuenta_transferencia, 0, 3);
                $banco = Banco::where('clave', $clabe)->first();
                $bancoNombre = $banco ? $banco->banco_nombre : 'Banco Desconocido';
    
                // Verificar el ID del usuario y cargar la vista correspondiente
                if ($userId == 377) {
                    $pdf = PDF::loadView('contratos.contratoreymundo', compact('inversion', 'empresa', 'user', 'proyecto', 'beneficiarios', 'bancoNombre'));
                } else {
                    $pdf = PDF::loadView('contratos.inversionV1', compact('inversion', 'empresa', 'user', 'proyecto', 'beneficiarios', 'bancoNombre'));
                }
    
                return $pdf->stream('contrato-inversion.pdf');
    
                break;
                default:
                $userid = Auth::id();
                // Verificar si la inversión está en la sesión, si no, obtenerla de la base de datos
                if (!$inversion) {
                    $inversion = InversionCliente::where('user_id', $userid)
                        ->orderBy('created_at', 'desc')
                        ->first();
                }
                $folioPrincipal = substr($inversion->folio, 0, strpos($inversion->folio, '-'));
                $clabe = substr($inversion->cuenta_transferencia, 0, 3);
                $banco = Banco::where('clave', $clabe)->first();
                $beneficiarios = Beneficiarios::join('beneficiario_inversi', 'beneficiarios.id', '=', 'beneficiario_inversi.id_beneficiario')
                    ->select('beneficiarios.name', 'beneficiarios.lastName', 'beneficiarios.relationship', 'beneficiario_inversi.porcentaje')
                    ->where('beneficiarios.user_id', '=', $userid)
                    ->get();
                $bancoNombre = $banco ? $banco->banco_nombre : 'Banco Desconocido';
                $inversionBase = InversionCliente::where('folio', $folioPrincipal)->first();
            
                $cantidadInversionPrincipal = $inversionBase ? str_replace(['$', ','], '', $inversionBase->cantidad) : '0';
                $cantidadReinversion = str_replace(['$', ','], '', $inversion->cantidad);
            
                $diferencia = floatval($cantidadInversionPrincipal) - floatval($cantidadReinversion);
                $diferenciamayor = floatval($cantidadReinversion) - floatval($cantidadInversionPrincipal);
                $diferenciaFormateada = number_format($diferencia, 2, '.', ',');
                $diferenciamayorFormateada = number_format($diferenciamayor, 2, '.', ',');
            
                $currencyCode = 'USD'; // Currency code, adjust as needed
                $currencies = new ISOCurrencies();
                $moneyParser = new DecimalMoneyParser($currencies);
            
                $diferenciamayorNumerico = str_replace(',', '', $diferenciamayorFormateada);
                $currency = new Currency($currencyCode); // Create a Currency instance
                $diferenciamayorMoney = $moneyParser->parse($diferenciamayorNumerico, $currency);
            
                // Validación y selección de la vista correspondiente
                if ($cantidadInversionPrincipal < $cantidadReinversion) {
                    $pdf = PDF::loadView('contratos.adendummayor', compact('inversion', 'empresa', 'user', 'inversionBase', 'proyecto', 'beneficiarios', 'bancoNombre', 'diferenciaFormateada', 'diferenciamayor', 'folioPrincipal'));
                } elseif ($cantidadInversionPrincipal > $cantidadReinversion) {
                    $pdf = PDF::loadView('contratos.adendummenor', compact('inversion', 'empresa', 'user', 'inversionBase', 'proyecto', 'beneficiarios', 'bancoNombre', 'diferenciaFormateada', 'diferenciamayor', 'folioPrincipal'));
                } else {
                    $pdf = PDF::loadView('contratos.adendumV1', compact('inversion', 'empresa', 'user', 'inversionBase', 'proyecto', 'beneficiarios', 'bancoNombre', 'diferenciaFormateada', 'diferenciamayor', 'folioPrincipal'));
                }
            
                // Generar el nombre del archivo
                $nombreCliente = Str::lower($user->name); // Convertir el nombre del cliente a minúsculas
                $folioContrato = $inversion->folio;
                $filename = 'adendum_' . $nombreCliente . $folioContrato . '.pdf';
            
                // Obtener la ruta completa del directorio de destino
                $directorioDestino = 'C:\Users\Admin\contratos';
            
                // Verificar si el directorio de destino existe, si no, crearlo
            
                // Resto del código de guardado del contrato en el directorio de destino...
            
                return $pdf->stream($filename);
                break;
        }
    }

    public function printPDFControl($id_inversion)
    {
        $userId = Auth::id();
        $user = User::where('users.id', $userId)
            ->rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
            ->first();
        $inversion = InversionCliente::where([
            ['id', $id_inversion],
            ['user_id', $userId],
        ])->first();
        $proyecto = proyecto::all();
        $userid = Auth::user()->id;
        $beneficiarios = Beneficiarios::join('beneficiario_inversi', 'beneficiarios.id', '=', 'beneficiario_inversi.id_beneficiario')
            ->select('beneficiarios.name', 'beneficiarios.lastName', 'beneficiarios.relationship', 'beneficiario_inversi.porcentaje')
            ->where('beneficiarios.user_id', '=', $userid)
            ->get();
        $empresa = Empresa_inversion::where('id', $inversion->empresa_inversion_id)->first();
        $dt = Date::parse($inversion->fecha_inicio)->format('l d F Y');
        $day = Date::parse($inversion->fecha_inicio)->format('d');
        $años = (Carbon::parse($inversion->fecha_termino))->diffInYears(Carbon::parse($inversion->fecha_inicio));
        $cantidad = $inversion->cantidad;
        $tipo_contrato = $inversion->contrato_inversion_id;
        $data = Self::crearData($años, $empresa, $user, $cantidad, $dt, $day, $tipo_contrato, $proyecto, $beneficiarios);
        $pdf = PDF::loadView('pdf_view1', $data);

        // Generar el nombre del archivo
        $nombreCliente = Str::lower($user->name); // Convertir el nombre del cliente a minúsculas
        $filename = 'contrato' . $nombreCliente . $id_inversion . '.pdf';

        // Obtener la ruta completa del directorio de destino
        $directorioDestino = 'C:\Users\Admin\contratos';

        // Verificar si el directorio de destino existe, si no, crearlo
        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        // Guardar una copia del contrato en el directorio de destino
        $pdf->save($directorioDestino . '\\' . $filename);

        return $pdf->stream($filename);
    }



    public function contratoReinversion()
    {
        $proyecto = proyecto::all();
        $inversion = session()->get('inversion');
        $userid = Auth::id();
        // Verificar si la inversión está en la sesión, si no, obtenerla de la base de datos
        if (!$inversion) {
            $inversion = InversionCliente::where('user_id', $userid)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        $folioPrincipal = substr($inversion->folio, 0, strpos($inversion->folio, '-'));
        $inversionBase = InversionCliente::where('folio', $folioPrincipal)->first();
        $empresa = Empresa_inversion::where('id', $inversion->empresa_inversion_id)->first();
        $user = User::where('users.id', Auth::id())
            ->rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
            ->first();
        $clabe = substr($inversion->cuenta_transferencia, 0, 3);
        $banco = Banco::where('clave', $clabe)->first();
        $userid = Auth::user()->id;
        $bancoNombre = $banco ? $banco->banco_nombre : 'Banco Desconocido';
        $beneficiarios = Beneficiarios::join('beneficiario_inversi', 'beneficiarios.id', '=', 'beneficiario_inversi.id_beneficiario')
            ->select('beneficiarios.name', 'beneficiarios.lastName', 'beneficiarios.relationship', 'beneficiario_inversi.porcentaje')
            ->where('beneficiarios.user_id', '=', $userid)
            ->get();
            $penultimaInversion = InversionCliente::where('user_id', $userid)
    ->orderBy('created_at', 'desc')// Omitir la última inversión
    ->first();

        $cantidadInversionPrincipal = str_replace(['$', ','], '', $penultimaInversion->cantidad);
        $cantidadReinversion = str_replace(['$', ','], '', $inversion->cantidad);

        $diferencia = floatval($cantidadInversionPrincipal) - floatval($cantidadReinversion);
        $diferenciamayor = floatval($cantidadReinversion) - floatval($cantidadInversionPrincipal);
        $diferenciaFormateada = number_format($diferencia, 2, '.', ',');
        $diferenciamayorFormateada = number_format($diferenciamayor, 2, '.', ',');

        $currencyCode = 'USD'; // Currency code, adjust as needed
        $currencies = new ISOCurrencies();
        $moneyParser = new DecimalMoneyParser($currencies);


        $diferenciamayorNumerico = str_replace(',', '', $diferenciamayorFormateada);
        $currency = new Currency($currencyCode); // Create a Currency instance
        $diferenciamayorMoney = $moneyParser->parse($diferenciamayorNumerico, $currency);


        if ($cantidadInversionPrincipal < $cantidadReinversion) {
            $pdf = PDF::loadView('contratos.adendummayor', compact('inversion', 'empresa', 'user', 'inversionBase', 'proyecto', 'beneficiarios', 'bancoNombre', 'diferenciaFormateada', 'diferenciamayor', 'folioPrincipal'));
        } elseif ($cantidadInversionPrincipal > $cantidadReinversion) {
            $pdf = PDF::loadView('contratos.adendummenor', compact('inversion', 'empresa', 'user', 'inversionBase', 'proyecto', 'beneficiarios', 'bancoNombre', 'diferenciaFormateada', 'diferenciamayor', 'folioPrincipal'));
        } else {
            $pdf = PDF::loadView('contratos.adendumV1', compact('inversion', 'empresa', 'user', 'inversionBase', 'proyecto', 'beneficiarios', 'bancoNombre', 'diferenciaFormateada', 'diferenciamayor', 'folioPrincipal'));
        }

        // Generar el nombre del archivo
        $nombreCliente = Str::lower($user->name); // Convertir el nombre del cliente a minúsculas
        $folioContrato = $inversion->folio;
        $filename = 'adendum_' . $nombreCliente . $folioContrato . '.pdf';

        // Obtener la ruta completa del directorio de destino
        $directorioDestino = 'C:\Users\Admin\contratos';

        // Verificar si el directorio de destino existe, si no, crearlo


        // Guardar una copia del contrato en el directorio de destino
      

        return $pdf->stream($filename);
    }
 
    public function showContratoReinversion($id)
    {
        $proyecto = proyecto::all();
        $userid = Auth::user()->id;
        $beneficiarios = Beneficiarios::join('beneficiario_inversi', 'beneficiarios.id', '=', 'beneficiario_inversi.id_beneficiario')
            ->select('beneficiarios.name', 'beneficiarios.lastName', 'beneficiarios.relationship', 'beneficiario_inversi.porcentaje')
            ->where('beneficiarios.user_id', '=', $userid)
            ->get();
        $inversion = InversionCliente::findOrFail($id);
        $clabe = substr($inversion->cuenta_transferencia, 0, 3);
        $banco = Banco::where('clave', $clabe)->first();
        $bancoNombre = $banco ? $banco->banco_nombre : 'Banco Desconocido';
        $empresa = Empresa_inversion::where('id', $inversion->empresa_inversion_id)->first();
        $user = User::where('users.id', Auth::id())
            ->rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
            ->first();

            if ($inversion->contrato_inversion_id == 5) {
                // Obtener información de aprobación de la tabla Procedencia
                $aprobado = Procedencia::where('id_inversion', $inversion->id)->value('aprobado');
        
                if ($aprobado === 'si') {
                    $procedenciaAprobada = Procedencia::where('id_user', $userid)
                        ->where('aprobado', 'si')
                        ->get(); 
                
                    if ($procedenciaAprobada->count() > 0) {
                        $procedenciaViewName = 'contratos.contratoprocedencia';
                
                        $datosProcedencia = $procedenciaAprobada->pluck('campo_texto');
                
                        $textoProcedencia = [];
                
                        // Repetir el texto según el número de filas en la tabla procedencia
                        foreach ($procedenciaAprobada as $procedencia) {
                            // Format the currency
                            $formattedCantidad = number_format($procedencia->cantidad, 2, '.', ',');
                            
                            $textoProcedencia[] = "- La cantidad de $" . $formattedCantidad . "MX mediante transferencia electrónica de la cuenta " . $procedencia->cuentaclabe . " de la Entidad Financiera " . $procedencia->entidadFinanciera . " a nombre de " . $user->name . " " . $user->lastName . ".";
                        }
                
                        // Convertir el array a una cadena con saltos de línea en HTML
                        $textoProcedenciaStr = implode('<br>', $textoProcedencia);
                
                        // Si la columna 'aprobado' es 'si', cargar la vista correspondiente
                        $pdf = PDF::loadView('contratos.contratoprocedencia', compact('inversion', 'empresa', 'user', 'proyecto', 'beneficiarios', 'bancoNombre', 'textoProcedenciaStr'));
                        $filename = 'reinversion.pdf';
                    }
                } else {
                    // Si la columna 'aprobado' no es 'si', cargar la vista por defecto
                    $pdf = PDF::loadView('contratos.inversionV1', compact('inversion', 'empresa', 'user', 'proyecto', 'beneficiarios', 'bancoNombre'));
                    $filename = 'reinversion.pdf';
                }
            } else {
            $clabe = substr($inversion->cuenta_transferencia, 0, 3);
            $banco = Banco::where('clave', $clabe)->first();
            $bancoNombre = $banco ? $banco->banco_nombre : 'Banco Desconocido';
            $inversionBase = InversionCliente::where('folio', $inversion->folio)->first();
            $pdf = PDF::loadView('contratos.adendumV1', compact('inversion', 'empresa', 'user', 'inversionBase', 'proyecto', 'beneficiarios', 'bancoNombre'));
            $filename = 'contrato-Reinversion.pdf';
        }

        // Generar el nombre del archivo
        $filename = Str::lower($filename);

        // Obtener la ruta completa del directorio de destino
        $directorioDestino = 'C:\Users\Admin\contratos';

        return $pdf->stream($filename);
    }



}
