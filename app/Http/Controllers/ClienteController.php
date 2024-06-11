<?php

namespace App\Http\Controllers;

use App\Models\Beneficiarios;
use App\Models\Contrato_inversion;
use App\Models\DatosUsuario;
use App\Models\Beneficiario_inversion;
use App\Models\Empresa_inversion;
use App\Models\Estado_inversion;
use App\Models\Procedencia;
use App\Http\Requests\AlmacenarBeneficiario;
use App\Models\InversionCliente;
use App\Models\photos;
use App\Mail\MailAltaCliente;
use App\Mail\ProcesoInversionMail;
use App\Mail\reinversion;
use App\Mail\InversionExitosaMail;
use App\Models\User;
use App\Mail\ContactoMail;
use App\Mail\UsuarioCuentaMail;
use App\Models\Proyecto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\Storage;
use PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
Use App\Models\Banco;
use App\Mail\ContratoMail;
use Illuminate\Support\Facades\Validator;

class clienteController extends Controller
{

    public function index()
    {
        // $role = DatosUsuario::where('user_id', Auth::user()->id)->get();
        if (Auth::user()->hasRoles(['admin'])) {
            $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
                ->where('verified', 0)
                ->paginate(15);
            $beneficiarios = Beneficiarios::all();
            $inversiones = InversionCliente::all();
            $empresas = Empresa_inversion::all();
            $contratos = Contrato_inversion::all();
            $estados = Estado_inversion::all();
            return view('admin', ['usuarios' => $users, 'empresas' => $empresas, 'contratos' => $contratos, 'inversiones' => $inversiones, 'beneficiarios' => $beneficiarios, 'estados' => $estados]);
        }
        else {
            $userid = Auth::user()->id;
            $beneficiarios = Beneficiarios::where('user_id', $userid)->get();
            $inversionesActivas = InversionCliente::obtener($userid, 1);
            $inversionesPendientes = InversionCliente::obtener($userid, 2);
            $inversionesTerminadas = InversionCliente::obtener($userid, 3);
            $datos = DatosUsuario::where('user_id', $userid)->get();
            return view('controlCliente', ['beneficiarios' => $beneficiarios, 'inversionesActivas' => $inversionesActivas, 'inversionesPendientes' => $inversionesPendientes, 'inversionesTerminadas' => $inversionesTerminadas, 'datos' => $datos]);
        }
        return Redirect::to("login")->withSuccess('Opps! You do not have access');
    }

    public function postInversion(Request $request)
    {
        $imagePath = $this->uploadImage($request);
  
        $inversion = new InversionCliente();
        $inversion->user_id = Auth::user()->id;
        $inversion->cantidad = $request->input('cantidadInversion');
        $inversion->empresa_inversion_id =  (Empresa_inversion::where('activa', true)->first())->id;
        try {
            $ultimoFolio = InversionCliente::where('empresa_inversion_id', $inversion->empresa_inversion_id)->latest('created_at')->first();
           $inversion->folio = ($ultimoFolio->folio) + 1;
        } catch (\Throwable $th) {
           $inversion->folio = 1;
        }



        $inversion->cuenta_inversion = $request->input('account');
        $inversion->cuenta_pago_rendimientos = $request->input('rendimiento');
        $inversion->cfdi = $request->input('cfdi');
        $inversion->fecha_inicio = Carbon::now();
        $inversion->fecha_termino = InversionCliente::obtenerPlazo($request->input('plazoInversion'));
        $inversion->estado_inversion_id = 5;
        $inversion->contrato_inversion_id = 5;
        $inversion->tasa_mensual = 1;
        Session::put('inversion', $inversion);
        Session::save();
     
        return Redirect::to('registro/altabeneficiario');
    }


    public function saveImage(Request $request)
    {
        if ($request->hasFile('fiscal')) {
            $file = $request->file('fiscal');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = public_path('cfdi');
            
            // Crear el directorio si no existe
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            $file->move($path, $filename);
            
            return 'cfdi/' . $filename;
        }
        return null;
    }
    


    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = time().'.'.$request->image->extension();

        // Mueve la imagen a la carpeta public/cfdi
        $request->image->move(public_path('cfdi'), $imageName);

        return back()
            ->with('success','Imagen subida con éxito.')
            ->with('image', $imageName);
    }

    public function contrato()
    {
        return view('contrato');
    }
    public function Reinversion(Request $request)
    {
        request()->validate([
            'cantidadInversion' => 'required|confirmed',
            'plazoInversion' => 'required',
            'id' => 'required|numeric',
            'cuentaTransferencia' => 'required|confirmed|numeric'
        ]);

        $data = $request->all();
        $inversion = new InversionCliente();
        $inversion->cantidad = $data['cantidadInversion'];
        $inversion->fecha_inicio = Carbon::now();
        $inversion->fecha_termino = InversionCliente::obtenerPlazo($data['plazoInversion']);
        $inversionBase = InversionCliente::where([
            ['id', $data['id']],
            ['user_id', Auth::user()->id],
        ])->first();
        $resultado = app('App\Http\Controllers\pdfController')->contratoReinversion($inversion, $inversionBase);

        if ($inversionBase) {
            if ($inversionBase->cantidad < $inversion->cantidad) {
                $inversion->contrato_inversion_id = 2;
            } elseif ($inversionBase->cantidad > $inversion->cantidad) {
                $inversion->contrato_inversion_id = 3;
            } else {
                $inversion->contrato_inversion_id = 4;
            }

            $inversion->cuenta_transferencia = $data['cuentaTransferencia'];
            $inversion->estado_inversion_id = 2;
            $inversion->empresa_inversion_id = $inversionBase->empresa_inversion_id;
            $inversion->user_id = Auth::user()->id;

            // Obtén el último folio del cliente, independientemente del sufijo
            $lastFolio = InversionCliente::where('user_id', Auth::user()->id)
                ->orderBy('folio', 'desc')
                ->first();
                if ($lastFolio) {
                    // Verifica si $lastFolio es null antes de acceder a la propiedad folio
                    $lastFolioNumber = intval(substr($lastFolio->folio, -3));
                    $newFolioNumber = str_pad($lastFolioNumber + 1, 3, '0', STR_PAD_LEFT);
                
                    // Reemplaza el último número en el folio existente con el nuevo número
                    $inversion->folio = preg_replace('/-AD\d{3}$/', '-AD' . $newFolioNumber, $lastFolio->folio);
                } else {
                    // Si no hay folios anteriores para el cliente, asigna un folio inicial
                    $inversion->folio = 'AD001'; // O cualquier otro formato que desees
                }

            Session::put('inversion', $inversion);
            Session::save();

            return view('contratoReinversion', compact('inversion'));
        } else {
            // Manejar el caso cuando no se encuentra una inversión base
            return redirect()->back()->withErrors(['No se encontró la inversión base.']);
        }
    }




    public function cambiarClabe(Request $request, $id)
    {
        $datos = User::find($id);
            // Guardar la imagen de estadodecuenta
            $imagefiscal = $request->file('estadodecuenta');
            if ($imagefiscal) {
                $new_nameestado = rand() . $datos['name'] . $datos['lastName'] . 'estado.' . $imagefiscal->getClientOriginalExtension();
                $imagefiscal->move(public_path('estadodecuenta'), $new_nameestado);
                $photo = new photos();
                $photo->user_id = $id;
                $photo->path = 'estadodecuenta/' . $new_nameestado;
                $photo->save();
            }
            $datos->save();
        // Obtener el ID de la inversión y la nueva CLABE de la solicitud
        $id = $request->input('id');
        $password = $request->input('password');
        $nuevaClabe = $request->input('nuevaClabe');
        $confirmarClabe = $request->input('confirmarClabe');

        // Buscar la inversión por ID y verificar si existe
        $inversionCliente = InversionCliente::find($id);
        if (!$inversionCliente) {
            abort(404, 'Inversión no encontrada');
        }

        // Validar la contraseña del usuario
        if (!Hash::check($password, $inversionCliente->user->password)) {
            throw new \Exception("Contraseña incorrecta");
        }

        // Validar que la nueva CLABE y la confirmación coincidan
        if ($nuevaClabe !== $confirmarClabe) {
            throw new \Exception("La nueva CLABE y la confirmación no coinciden");
        }


        // Guardar la nueva CLABE
        $inversionCliente->cuenta_transferencia = $nuevaClabe;
        $inversionCliente->save();


        return redirect()->back()->with('success', 'CLABE interbancaria actualizada con éxito');
    }

    public function actualizarContratoFirmado($user_id) {
        // Buscar la última inversión del usuario
        $ultimaInversion = InversionCliente::where('user_id', $user_id)
                                           ->latest()
                                           ->first();
    
        if ($ultimaInversion) {
            // Actualizar el campo contratofirmado
            $ultimaInversion->update([
                'contratofirmado' => "si", // Cambia esto según el valor que desees asignar
            ]);
    
            // Obtener los datos del usuario
            $user = DatosUsuario::where('user_id', $user_id)->first();
    
            if ($user) {
                // Generar el contrato PDF
                $filename = $this->generarContratoPDF($user->user_id);
    
                // Obtener la ruta completa del archivo PDF
                $pdfPath = public_path($filename); // Usar public_path() para obtener la ruta pública del archivo PDF
    
                // Enviar el correo electrónico con el PDF adjunto
                Mail::to($user->email)->send(new ContratoMail($user->user_id, $pdfPath));
            }
    
            return "El campo contratofirmado de la última inversión del usuario con ID {$user_id} ha sido actualizado correctamente y se ha enviado un correo electrónico.";
        } else {
            return "No se encontró ninguna inversión para el usuario con ID {$user_id}.";
        }
    }

    public function generarContratoPDF($userid) {
        // Obtener la inversión más reciente de la base de datos
        $inversionDB = InversionCliente::where('user_id', $userid)->latest()->first();
    
        // Obtener la inversión de la sesión
        $inversionSesion = session()->get('inversion');
    
        // Combinar ambas inversiones usando el operador de fusión de null
        $inversion = $inversionDB ?? $inversionSesion;
        $proyecto = Proyecto::all();
        $beneficiarios = Beneficiarios::join('beneficiario_inversi', 'beneficiarios.id', '=', 'beneficiario_inversi.id_beneficiario')
            ->select('beneficiarios.name', 'beneficiarios.lastName', 'beneficiarios.relationship', 'beneficiario_inversi.porcentaje')
            ->where('beneficiarios.user_id', '=', $userid)
            ->get();
        $empresa = Empresa_inversion::where('id', $inversion->empresa_inversion_id)->first();
        $user = User::where('users.id', $userid)
            ->rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
            ->first();
        $clabe = substr($inversion->cuenta_transferencia, 0, 3);
        $banco = Banco::where('clave', $clabe)->first();
        $bancoNombre = $banco ? $banco->banco_nombre : 'Banco Desconocido';
    
        // Lógica condicional para determinar qué vista cargar
        $viewName = 'contratos.inversionV1';
    
        // Cargar la vista correcta
        $pdf = PDF::loadView($viewName, compact('inversion', 'empresa', 'user', 'proyecto', 'beneficiarios', 'bancoNombre'));
    
        // Generar el nombre del archivo
        $nombreCliente = Str::lower($user->name); // Convertir el nombre del cliente a minúsculas
        $folioContrato = $inversion->folio;
        $filename = 'contrato' . $nombreCliente . $folioContrato . '.pdf';
    
        // Guardar el PDF en el directorio public/contratos
        $pdf->save(public_path('contratos/' . $filename));
    
        // Devolver la ruta relativa del archivo
        return 'contratos/' . $filename;
    }

    public function postContrato(Request $request)
    {
        request()->validate([
            'contrato',
        ]);

        $inversion = session()->get('inversion');

        if ($inversion) {

            // Actualizar el campo contratofirmado a "si"
            $inversion->contratofirmado = "si";
            $inversion->save();

            if ($inversion->contrato_inversion_id == 3 || $inversion->contrato_inversion_id == 4) {
                $inversionBase = InversionCliente::where([
                    ['folio', $inversion->folio],
                    ['empresa_inversion_id', $inversion->empresa_inversion_id],
                    ['estado_inversion_id', 4]
                ])->latest("updated_at")->first();

                if ($inversionBase) {
                    if ($inversion->contrato_inversion_id == 3) {
                        $inversionBase->contratofirmado = "si";
                        $inversionBase->estado_inversion_id = 7;
                    } elseif ($inversion->contrato_inversion_id == 4) {
                        $inversionBase->contratofirmado = "si";
                        $inversionBase->estado_inversion_id = 9;
                    }
                    $inversionBase->save();

       
                }
            }
        }

        return Redirect::to("registro/inversionfinal");
    }




    public function postContratopre(Request $request)
    {

        request()->validate([
            'contrato',
        ]);
        $inversion = session()->get('inversion');
        $inversion->save();
        if ($inversion->contrato_inversion_id == 3 || $inversion->contrato_inversion_id == 4) {
            $inversionBase = InversionCliente::where([
                ['folio' , $inversion->folio],
                ['empresa_inversion_id' , $inversion->empresa_inversion_id],
                ['estado_inversion_id' , 4]
                ])->latest("updated_at")->first();
            if ($inversion->contrato_inversion_id == 3) {
                $inversionBase->estado_inversion_id = 7;

            } elseif($inversion->contrato_inversion_id ==  4) {
                $inversionBase->estado_inversion_id = 9;

            }

            $inversionBase->save();


        }

        $userid = Auth::user()->id;
        return redirect()->route('comprobanteinv', ['id' => $userid]);


    }

    public function createInversion(array $data)
    {
        $user = User::findOrFail(Auth::user()->id);
        $this->authorize($user);
        return InversionCliente::create([
            'user_id' => Auth::user()->id,
            'folio' => $data['folio'],
            'cantidad' => $data['cantidad'],
            'cuenta_transferencia' => $data['account'],
            'fecha_inicio' => $data['fecha_inicio_contrato'],
            'fecha_termino' => $data['fecha_termino_contrato'],
            'empresa_inversion_id' => $data['empresa_inversion_id'],
            'estado_inversion_id' => $data['estado_inversion_id'],
            'contrato_inversion_id' => $data['contrato_inversion_id'],
            'titular_transferencia' => $data['titular_transferencia'],
        ]);
    }
    public function getBeneficiariosCount()
    {
        // Obtener el recuento de beneficiarios para el usuario actual
        return Beneficiarios::where('user_id', Auth::user()->id)->count();
    }
    public function postBeneficiario(AlmacenarBeneficiario $request)
    {
        // Verificar si el usuario ya tiene tres beneficiarios registrados
        if ($this->getBeneficiariosCount() >= 4) {
            return redirect()->back()->withErrors('Ya has registrado el máximo de cuatro beneficiarios.');
        }
    
        // Si el usuario no ha alcanzado el límite de tres beneficiarios, crear uno nuevo
        $this->createBeneficiario($request->all());
    
        return Redirect::to("registro/altabeneficiario")->withSuccess('Se ha registrado tu beneficiario exitosamente, selecciona los beneficiarios para tu inversión');
    }
    
    public function createBeneficiario(array $data)
    {
        $user = User::findOrFail(Auth::user()->id);
        $this->authorize($user);
        return Beneficiarios::create([
            'user_id' => Auth::user()->id,
            'name' => $data['nombreBeneficiario'],
            'lastName' => $data['apellidoBeneficiario'],
            'relationship' => $data['parentescoBeneficiario'],
            'edad' => $data['edadBeneficiario'],
        ]);

    }

    public function postComprobante(Request $request)
    {
        request()->validate([
            'id' => 'required|numeric',
            'comprobanteImg' => 'required|mimes:jpeg,png,pdf',
        ]);
        
        $data = $request->all();
        $file = $request->file('comprobanteImg');
        $new_name = rand() . 'Comprobante.' . $file->getClientOriginalExtension();
        $file->move(public_path('comprobante'), $new_name);
        
        $photo = new photos();
        $photo->user_id = Auth::user()->id;
        $photo->path = 'comprobante/' . $new_name;
        $photo->save();
        
        $inversion = InversionCliente::find($data['id']);
        $inversion->estado_inversion_id = 1;
        $inversion->save();
        
        // Obtenemos los datos del usuario
        $usuario = Auth::user();
        $datosUsuario = DatosUsuario::where('user_id', $usuario->id)->first();
        $userName = $usuario->name;
        $lastName = $datosUsuario->lastName;
    
        // Enviamos el correo al usuario
        Mail::to($usuario->email)->send(new InversionExitosaMail($userName, $lastName));
    
        return Redirect::to("registro/fininv")->withSuccess('Recibimos tu comprobante, un asesor se contactará contigo.');
    }
    

    public function postAccount(Request $request)
    {
        request()->validate([
            'accountNo' => 'required|mimes:png,jpg,jpeg,csv,txt,xlx,xls,pdf|max:2048',
        ]);
        $image = $request->file('accountNo');
        $new_name = rand() . 'accountNo.' . $image->getClientOriginalExtension();
        $image->move(public_path('accountNo'), $new_name);
        $photo = new photos();
        $photo->user_id = Auth::user()->id;
        $photo->path = 'accountNo/' . $new_name;
        $photo->save();
        $account = DatosUsuario::where('user_id', Auth::user()->id)->first();
        $account->innerID = 'OK';
        $account->save();
        return Redirect::to("registro/lobby")->withSuccess('Recibimos tu número de cuenta, esta en proceso de validación');
    }

    public function update(Request $request, $id) {

        $messages = [
            'idphoto.required' => 'La foto de identificación es obligatoria.',
            'idphoto.mimes' => 'La foto de identificación debe ser un archivo de tipo: png, jpg, jpeg, csv, txt, xls, xlsx, pdf.',
            'idphoto.max' => 'La foto de identificación no debe ser mayor de 2MB.',
            'idphotoback.mimes' => 'La parte posterior de la identificación debe ser un archivo de tipo: png, jpg, jpeg, csv, txt, xls, xlsx, pdf.',
            'idphotoback.max' => 'La parte posterior de la identificación no debe ser mayor de 2MB.',
            'addressphoto.mimes' => 'La foto de la dirección debe ser un archivo de tipo: png, jpg, jpeg, csv, txt, xls, xlsx, pdf.',
            'addressphoto.max' => 'La foto de la dirección no debe ser mayor de 2MB.',
            'fiscalphoto.mimes' => 'La foto fiscal debe ser un archivo de tipo: png, jpg, jpeg, csv, txt, xls, xlsx, pdf.',
            'fiscalphoto.max' => 'La foto fiscal no debe ser mayor de 2MB.',
            'estadodecuenta.mimes' => 'El estado de cuenta debe ser un archivo de tipo: png, jpg, jpeg, csv, txt, xls, xlsx, pdf.',
            'estadodecuenta.max' => 'El estado de cuenta no debe ser mayor de 2MB.',
        ];

        // Validación de los archivos
        $validator = Validator::make($request->all(), [
            'idphoto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'idphotoback' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'addressphoto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'fiscalphoto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'estadodecuenta' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], $messages);
    
    // Si la validación falla, redirigir con errores
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }
    
        // Obtener el usuario
        $datos = User::find($id);
    
        // Asignar los campos restantes desde la solicitud HTTP
        $datos->identificacion = $request->input('identificacion');
        $datos->numero = $request->input('numero');
    
        // Procesar la imagen de idphoto
        $image = $request->file('idphoto');
        if ($image) {
            $new_name = rand() . $datos['name'] . $datos['lastName'] . 'Id.' . $image->getClientOriginalExtension();
            $image->move(public_path('ids'), $new_name);
            $photo = new Photos();
            $photo->user_id = $id;
            $photo->path = 'ids/' . $new_name;
            $photo->save();
        }
    
        // Procesar la imagen de idphotoback
        $imageidback = $request->file('idphotoback');
        if ($imageidback) {
            $new_name = rand() . $datos['name'] . $datos['lastName'] . 'IdBack.' . $imageidback->getClientOriginalExtension();
            $imageidback->move(public_path('ids'), $new_name);
            $photo = new Photos();
            $photo->user_id = $id;
            $photo->path = 'ids/' . $new_name;
            $photo->save();
        }
    
        // Procesar la imagen de addressphoto
        $imageaddress = $request->file('addressphoto');
        if ($imageaddress) {
            $new_nameaddress = rand() . $datos['name'] . $datos['lastName'] . 'address.' . $imageaddress->getClientOriginalExtension();
            $imageaddress->move(public_path('addressPhotos'), $new_nameaddress);
            $photo = new Photos();
            $photo->user_id = $id;
            $photo->path = 'addressPhotos/' . $new_nameaddress;
            $photo->save();
        }
    
        // Procesar la imagen de fiscalphoto
        $imagefiscal = $request->file('fiscalphoto');
        if ($imagefiscal) {
            $new_namefiscal = rand() . $datos['name'] . $datos['lastName'] . 'fiscal.' . $imagefiscal->getClientOriginalExtension();
            $imagefiscal->move(public_path('fiscalphoto'), $new_namefiscal);
            $photo = new Photos();
            $photo->user_id = $id;
            $photo->path = 'fiscalphoto/' . $new_namefiscal;
            $photo->save();
        }
    
        // Procesar la imagen de estadodecuenta
        $imageestado = $request->file('estadodecuenta');
        if ($imageestado) {
            $new_nameestado = rand() . $datos['name'] . $datos['lastName'] . 'estado.' . $imageestado->getClientOriginalExtension();
            $imageestado->move(public_path('estadodecuenta'), $new_nameestado);
            $photo = new Photos();
            $photo->user_id = $id;
            $photo->path = 'estadodecuenta/' . $new_nameestado;
            $photo->save();
        }
    
        // Guardar los cambios en la base de datos
        $datos->save();
    
        return redirect()->route('contratopre')->with("success", "Información actualizada correctamente.");
    }


    public function eliminarFoto($id) {
        $photo = photos::findOrFail($id);
        $path = public_path($photo->path);
        if (file_exists($path)) {
            unlink($path);
        }
        $photo->delete();

        return redirect()->back()->with('success', 'La foto ha sido eliminada correctamente');
    }
    public function updatedocumentos(Request $request, $id){


        $datos = User::find($id);

        $datos->identificacion = $request->input('identificacion');
    $datos->numero = $request->input('numero');

    // Asignar los campos restantes desde la solicitud HTTP


        // Guardar la imagen de idphoto
$image = $request->file('idphoto');
if ($image !== null) {
    $new_name = rand() . $datos['name'] . $datos['lastName'] . 'Id.' . $image->getClientOriginalExtension();
    $image->move(public_path('ids'), $new_name);
    $photo = new photos();
    $photo->user_id = $id;
    $photo->path = 'ids/' . $new_name;
    $photo->save();
}

$imageidback = $request->file('idphotoback');
if ($imageidback !== null) {
    $new_name = rand() . $datos['name'] . $datos['lastName'] . 'IdBack.' . $imageidback->getClientOriginalExtension();
    $imageidback->move(public_path('ids'), $new_name);
    $photo = new photos();
    $photo->user_id = $id;
    $photo->path = 'ids/' . $new_name;
    $photo->save();
}

// Guardar la imagen de addressphoto
$imageaddress = $request->file('addressphoto');
if ($imageaddress !== null) {
    $new_nameaddress = rand() . $datos['name'] . $datos['lastName'] . 'address.' . $imageaddress->getClientOriginalExtension();
    $imageaddress->move(public_path('addressPhotos'), $new_nameaddress);
    $photo = new photos();
    $photo->user_id = $id;
    $photo->path = 'addressPhotos/' . $new_nameaddress;
    $photo->save();
}

// Guardar la imagen de fiscalphoto
$imagefiscal = $request->file('fiscalphoto');
if ($imagefiscal !== null) {
    $new_namefiscal = rand() . $datos['name'] . $datos['lastName'] . 'fiscal.' . $imagefiscal->getClientOriginalExtension();
    $imagefiscal->move(public_path('fiscalphoto'), $new_namefiscal);
    $photo = new photos();
    $photo->user_id = $id;
    $photo->path = 'fiscalphoto/' . $new_namefiscal;
    $photo->save();
}

// Guardar la imagen de estadodecuenta
$imageestado = $request->file('estadodecuenta');
if ($imageestado !== null) {
    $new_nameestado = rand() . $datos['name'] . $datos['lastName'] . 'estado.' . $imageestado->getClientOriginalExtension();
    $imageestado->move(public_path('estadodecuenta'), $new_nameestado);
    $photo = new photos();
    $photo->user_id = $id;
    $photo->path = 'estadodecuenta/' . $new_nameestado;
    $photo->save();
}

        // Guardar los cambios en la base de datos
        $datos->save();
        return redirect()->route('datoscliente')->with("success");

    }

    public function patchInversion($id)
{
    $user = User::findOrFail(Auth::user()->id);
    $this->authorize($user);
    $inversion = InversionCliente::where([
        ['id', $id],
        ['user_id', $user->id],
    ])->first();
    $inversion->estado_inversion_id = 8;
    $inversion->save();
    
    // Obtener el apellido del usuario desde la tabla datos_usuario
    $usuario = Auth::user();
    $datosUsuario = DatosUsuario::where('user_id', $usuario->id)->first();
    $userName = $usuario->name;
    $lastName = $datosUsuario->lastName;
    
    // Enviar un correo al usuario de la cuenta
    Mail::to('programacion@finverr.com')->send(new UsuarioCuentaMail());

    // Enviar un correo al administrador
    Mail::to('programacion@finverr.com')->send(new reinversion($userName, $lastName));


    return Redirect::to("registro/lobby")->withSuccess('Estimado CLIENTE obtendrá la devolución íntegra de la cantidad entregada en mutuo, a más tardar en los treinta días hábiles posteriores a la expiración del contrato vía transferencia electrónica.');
}
    public function reinversionVista($id)
    {
        $userid = Auth::user()->id;
        $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
                ->where('user_id', $userid)
                ->paginate(15);
        $inversion = InversionCliente::find($id);
        $proyecto = proyecto::all();
        return view('reinversion', ['inversion' => $inversion, 'proyecto' => $proyecto, 'usuarios' => $users]);
    }



    public function perfil( $id )
    {

        $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
        ->where('user_id', $id)
                ->paginate(50);
            return view('datoscliente')->with('usuarios',$users);
            //return view('informacioncliente', ['usuarios' => $users, 'empresas' => $empresas, 'contratos' => $contratos, 'inversiones' => $inversiones, 'beneficiarios' => $beneficiarios, 'estados' => $estados]);
    }



    public function indexdatos(  )
    {
        $userid = Auth::user()->id;
        $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
                ->where('user_id', $userid)
                ->paginate(15);
            $beneficiarios = Beneficiarios::all();
            $inversiones = InversionCliente::all();
            $empresas = Empresa_inversion::all();
            $contratos = Contrato_inversion::all();
            $estados = Estado_inversion::all();
            return view('datoscliente')->with('usuarios',$users);
    }
    public function indexdatosinicio(  )
    {
        $userid = Auth::user()->id;
        $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
                ->where('user_id', $userid)
                ->paginate(15);
            $beneficiarios = Beneficiarios::all();
            $inversiones = InversionCliente::all();
            $empresas = Empresa_inversion::all();
            $contratos = Contrato_inversion::all();
            $estados = Estado_inversion::all();
            return view('actualizar')->with('usuarios',$users);
    }
    public function indexdatosinicioase(  )
    {
        $userid = Auth::user()->id;
        $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
                ->where('user_id', $userid)
                ->paginate(15);
            $beneficiarios = Beneficiarios::all();
            $inversiones = InversionCliente::all();
            $empresas = Empresa_inversion::all();
            $contratos = Contrato_inversion::all();
            $estados = Estado_inversion::all();
            return view('actualizarasesor')->with('usuarios',$users);
    }
    public function documentos(  )
    {
        $userid = Auth::user()->id;
        $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
                ->where('user_id', $userid)
                ->paginate(15);
            $beneficiarios = Beneficiarios::all();
            $inversiones = InversionCliente::where('user_id', $userid)->paginate(1);
            $empresas = Empresa_inversion::all();
            $contratos = Contrato_inversion::all();
            $estados = Estado_inversion::all();
            $userid = Auth::user()->id;
            return view('documentos')->with('usuarios',$users, 'inversiones', $inversiones);
    }
    public function precontrato(  )
    {
        $userid = Auth::user()->id;
        $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
                ->where('user_id', $userid)
                ->paginate(15);
            $beneficiarios = Beneficiarios::all();
            $inversiones = InversionCliente::where('user_id', $userid)->paginate(1);
            $empresas = Empresa_inversion::all();
            $contratos = Contrato_inversion::all();
            $estados = Estado_inversion::all();
            $userid = Auth::user()->id;
            return view('contratopre')->with('usuarios',$users, 'inversiones', $inversiones);
    }

    public function indexdatoinv(  )
    {
        $userid = Auth::user()->id;
        $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
                ->where('user_id', $userid)
                ->paginate(15);
            $beneficiarios = Beneficiarios::all();
            $inversiones = InversionCliente::all();
            $empresas = Empresa_inversion::all();
            $contratos = Contrato_inversion::all();
            $estados = Estado_inversion::all();
            return view('altabeneficiario')->with('usuarios',$users);
    }

    public function activa(Request $request, $id){
        $estatus = DatosUsuario::find($id);
        $estatus->estado_fotos = '3';
        $estatus->save();
        return redirect()->route('informacioncliente')->with("success","Validacion Actualizada");
    }

     public function resetpassword(Request $request ,User $users){
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'password-confirm' => 'required|confirmed|min:8',
        ]);
        $users->update([
            'email' => $request->email,
            'password' => $request->password,
            'password-confirm' => $request->remember_token,

        ]);
        return redirect()->route('resetpassword', with($users->user_id))->with('success', 'Se actualizo ');
    }


    public function indexinversion($id )
    {
        $inversiones = InversionCliente::where('user_id', $id)->get();
        return view('invcliente', ['inversiones' => $inversiones]);
    }




    public function indexdatoss()
{
    $userid = Auth::user()->id;
    $beneficiarios = Beneficiarios::where('user_id', $userid)->orderBy('created_at', 'desc')->paginate(5);
    $inversiones = InversionCliente::where('user_id', $userid)->paginate(1);
    return view('altabeneficiario')->with([ 'beneficiarios' => $beneficiarios, 'inversiones' => $inversiones]);

    }



    public function getBeneficiario( )
    {
        $userid = Auth::user()->id;
        $beneficiarios = Beneficiario_inversion::where('id_user', $userid)->get();
        $inversionesActivas = InversionCliente::obtener($userid, 1);
        $inversionesPendientes = InversionCliente::obtener($userid, 2);
        $inversionesTerminadas = InversionCliente::obtener($userid, 3);
        $datos = DatosUsuario::where('user_id', $userid)->get();
        return view('benfcliente', ['beneficiarios' => $beneficiarios, 'inversionesActivas' => $inversionesActivas, 'inversionesPendientes' => $inversionesPendientes, 'inversionesTerminadas' => $inversionesTerminadas, 'datos' => $datos]);
    }



    public function getInversiones( )
    {
        $userid = Auth::user()->id;
        $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
        ->where('user_id', $userid)
        ->paginate(15);
        $beneficiarios = Beneficiarios::where('user_id', $userid)->get();
        $inversionesActivas = InversionCliente::obtener($userid, 1);
        $inversionesPendientes = InversionCliente::obtener($userid, 2);
        $inversionesTerminadas = InversionCliente::obtener($userid, 3);
        $datos = DatosUsuario::where('user_id', $userid)->get();
        return view('invcliente', ['usuarios',$users,'beneficiarios' => $beneficiarios, 'inversionesActivas' => $inversionesActivas, 'inversionesPendientes' => $inversionesPendientes, 'inversionesTerminadas' => $inversionesTerminadas, 'datos' => $datos]);
    }
        public function getInversionescliente( )
    {
        $userid = Auth::user()->id;
        $beneficiarios = Beneficiarios::where('user_id', $userid)->get();
        $inversionesActivas = InversionCliente::obtener($userid, 1);
        $inversionesPendientes = InversionCliente::obtener($userid, 2);
        $inversionesTerminadas = InversionCliente::obtener($userid, 3);
        $datos = DatosUsuario::where('user_id', $userid)->get();
        return view('comprobanteinv', ['beneficiarios' => $beneficiarios, 'inversionesActivas' => $inversionesActivas, 'inversionesPendientes' => $inversionesPendientes, 'inversionesTerminadas' => $inversionesTerminadas, 'datos' => $datos]);
    }
    public function getInversioness( )
    {
        $userid = Auth::user()->id;
        $beneficiarios = Beneficiarios::where('user_id', $userid)->get();
        $inversionesActivas = InversionCliente::obtener($userid, 1);
        $inversionesPendientes = InversionCliente::obtener($userid, 2);
        $inversionesTerminadas = InversionCliente::obtener($userid, 3);
        $datos = DatosUsuario::where('user_id', $userid)->get();
        return view('altacomprobante', ['beneficiarios' => $beneficiarios, 'inversionesActivas' => $inversionesActivas, 'inversionesPendientes' => $inversionesPendientes, 'inversionesTerminadas' => $inversionesTerminadas, 'datos' => $datos]);
    }

    public function postRegistrationCliente(Request $request)
{
    $request->validate([
        'name' => 'required',
        'lastName' => 'required',
        'email' => 'required|email|unique:users',
    ]);

    $data = $request->all();
    $passwordTemporal = Str::random(8); // Generar una contraseña temporal de 8 caracteres

    // Crear el usuario y obtener el ID del último registro
    $lastId = $this->createUser($data, $passwordTemporal);

    // Crear los datos adicionales del usuario
    $this->createUserData($data, $lastId);

    // Obtener el usuario creado
    $user = User::find($lastId);

    // Enviar el correo electrónico con los datos del usuario y la contraseña temporal
    Mail::to($user->email)->send(new MailAltaCliente($user, $passwordTemporal));

    // Redirigir a la página de administración de clientes con un mensaje de éxito
    return Redirect::to("registro/clienteadmin")->withSuccess('Se ha creado el cliente exitosamente');
}


public function createUser(array $data, string $password)
{
    $lastId = User::insertGetId([
        'name' => $data['name'],
        'email' => $data['email'],
        'role' => '2',
        'clave_asesor' => '',
        'password' => Hash::make($password),
        'password_change_required' => true, // Indicador de cambio de contraseña
    ]);

    $user = User::find($lastId);
    event(new Registered($user));
    return $lastId;
}

    public function createUserData(array $data, int $lastId)
    {


        return DatosUsuario::create([
            'user_id' => $lastId,
            'lastName' => $data['lastName'],
            'email' => $data['email'],
            'address' => '',
            'postalcode' => '',
            'telephone' => '',
            'rfc' => '',
            'addressphoto' => '1',
            'idphoto' => '1',
            'verified' => '0',
            'innerID' => 'PENDIENTE',
            'numero_cuenta' => '',
            'clave_asesor' => '',
        ]);
    }


    public function menu(  )
    {
        $userid = Auth::user()->id;
        $users = User::rightJoin('datos_usuario', 'users.id', '=', 'datos_usuario.user_id')
                ->where('user_id', $userid)
                ->paginate(15);
            $beneficiarios = Beneficiarios::all();
            $inversiones = InversionCliente::all();
            $empresas = Empresa_inversion::all();
            $contratos = Contrato_inversion::all();
            $estados = Estado_inversion::all();
            return view('inc/navphotos')->with('usuarios',$users);
    }

    public function menuasesor()
    {
        $userid = Auth::user()->id;
        $users = User::all()->where('id', $userid);
            return view('/views/inc/navphotos')->with('usuarios',$users);
            //return view('informacioncliente', ['usuarios' => $users, 'empresas' => $empresas, 'contratos' => $contratos, 'inversiones' => $inversiones, 'beneficiarios' => $beneficiarios, 'estados' => $estados]);
    }

    public function save_data(Request $request){


        $userid = Auth::user()->id;

    $inversionesCliente = InversionCliente::where('user_id', $userid)->get();

    if ($inversionesCliente->count() > 0) {
        // Obtener el primer registro de la colección
        $inversion = $inversionesCliente->first();
        $userid = Auth::user()->id;
        $inversion = InversionCliente::latest('id')->first();
        $inversionId = $inversion ? $inversion->id : null;
        $beneficiarios = Beneficiarios::where('user_id', $userid)->get();
        $porcentajes = $request->input('porcentaje');

        // Recorrer los porcentajes y guardar los datos en la base de datos solo si están llenados
        foreach ($porcentajes as $index => $porcentaje) {
            if (!empty($porcentaje)) {
                $beneficiario = new Beneficiario_inversion();
                $beneficiario->porcentaje = $porcentaje;
                $beneficiario->id_user =  Auth::user()->id;
                $beneficiario->id_inversion = $inversionId;
                $beneficiario->id_beneficiario = $beneficiarios[$index]->id;
                $beneficiario->relationship = $beneficiarios[$index]->relationship;
                $beneficiario->edad = $beneficiarios[$index]->edad;
                $beneficiario->name = $beneficiarios[$index]->name;
                $beneficiario->save();
            }
        }
        // Redirigir a la ventana "contratopre"
        return redirect()->route('contratopre'); // Suponiendo que tienes una ruta con nombre "contratopre" definida en tu aplicación
    } else {
        // Redirigir a la vista "datoscliente"
        $userid = Auth::user()->id;
        $beneficiarios = Beneficiarios::where('user_id', $userid)->get();
        $porcentajes = $request->input('porcentaje');

        // Recorrer los porcentajes y guardar los datos en la base de datos solo si están llenados
        foreach ($porcentajes as $index => $porcentaje) {
            if (!empty($porcentaje)) {
                $beneficiario = new Beneficiario_inversion();
                $beneficiario->porcentaje = $porcentaje;
                $beneficiario->id_user =  Auth::user()->id;
                $beneficiario->id_beneficiario = $beneficiarios[$index]->id;
                $beneficiario->relationship = $beneficiarios[$index]->relationship;
                $beneficiario->edad = $beneficiarios[$index]->edad;
                $beneficiario->name = $beneficiarios[$index]->name;
                $beneficiario->save();
            }
        }
        return redirect()->route('documentos'); // Suponiendo que tienes una vista llamada "datoscliente" en tu aplicación
    }
    }

    public function updateDatos(Request $request ,$id){
        $cliente=DatosUsuario::findOrFail($id);
        $cliente->cuenta_transferencia	=$request->input('account');
        $cliente->address=$request->input('address');
        $cliente->numero_ext=$request->input('numero_ext');
        $cliente->num_int=$request->input('numero_int');
        $cliente->colonia=$request->input('colonia');
        $cliente->postalcode=$request->input('postalcode');
        $cliente->municipio=$request->input('municipio');
        $cliente->ciudad=$request->input('ciudad');
        $cliente->telephone=$request->input('telephone');
        $cliente->save();
        return redirect()->route('datoscliente')->with("success","Validacion Actualizada");
    }
    public function updateDatosinicio(Request $request, $id)
    {
        $cliente = DatosUsuario::findOrFail($id);
        $cliente->rfc = $request->input('rfc');
        $cliente->birthday = $request->input('birthday');
        $cliente->cuenta_transferencia = $request->input('account');
        $cliente->address = $request->input('address');
        $cliente->numero_ext = $request->input('numero_ext');
        $cliente->num_int = $request->input('numero_int');
        $cliente->colonia = $request->input('colonia');
        $cliente->postalcode = $request->input('postalcode');
        $cliente->municipio = $request->input('municipio');
        $cliente->ciudad = $request->input('ciudad');
        $cliente->telephone = $request->input('telephone');

        $cliente->save();

        // Verificar si se proporcionó una nueva contraseña
        if ($request->filled('password')) {
            // Obtener el usuario autenticado
            $user = Auth::user();

            // Actualizar la contraseña del usuario
            $user->password = bcrypt($request->input('password'));
            // Marcar la contraseña como requerida para el cambio
            $user->password_change_required = true;

            $user->save();
        }

        return redirect()->route('lobby')->with("success", "Validación Actualizada");
    }
    public function updateDatosinicioase(Request $request, $id)
{
    // Verificar si se proporcionó una nueva contraseña
    if ($request->filled('password')) {
        // Obtener el usuario autenticado
        $user = User::find($id);

        // Actualizar la contraseña del usuario
        $user->password = bcrypt($request->input('password'));
        // Marcar la contraseña como requerida para el cambio
        $user->password_change_required = true;

        $user->save();
    }

    return redirect()->route('asesor')->with("success", "Validación Actualizada");
}




public function getGallery($id)
{
    $fotos = photos::where('user_id', $id)->get();

    return view('gallery', ['fotos' => $fotos]);
}


public function postProcedencia(Request $request)
{
    $userid = Auth::user()->id;

    // Obtén la última inversión del cliente
    $inversion = InversionCliente::latest('id')->first();

    $totalSecciones = 3; // Puedes ajustar esto según la cantidad máxima de secciones

    // Guarda los datos en la base de datos
    for ($i = 1; $i <= $totalSecciones; $i++) {
        // Verifica si los campos de la sección actual están presentes en la solicitud y no están vacíos
        if (
            $request->filled("campo{$i}_cantidad") &&
            $request->filled("campo{$i}_cuentaclabe") &&
            $request->filled("campo{$i}_institucion")
        ) {
            Procedencia::create([
                'id_user' => $userid,
                'id_inversion' => $inversion->id,
                'cantidad' => $request->input("campo{$i}_cantidad"),
                'cuentaclabe' => $request->input("campo{$i}_cuentaclabe"),
                'intitucion' => $request->input("campo{$i}_institucion"),
            ]);
        }
    }

    return redirect()->route('comprobanteinv', ['id' => $userid]);
}





}