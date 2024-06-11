<?php

use App\Http\Controllers\AsesorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\adminController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use Illuminate\Support\Facades\Route;
use App\Mail\ContactoMail;
use App\Mail\DanielEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Http\Request;



 // cuenta proviniente de los fondos. Cuenta de destino de rendimientos
Route::view('/', 'bienvenida')->name('home');
Route::get('pdfprueba', 'App\Http\Controllers\adminController@pdf')->name('pdfprueba');
Route::view('SORTEONAVIDEÑO', 'promocion')->name('promocion');
Route::view('correo', 'correo')->name('correo');
Route::view('actualizar', 'actualizar')->name('actualizar');
Route::view('actualizarasesor', 'actualizarasesor')->name('actualizarasesor');
// en routes/web.php o routes/api.php según tu configuración
Route::get('/actualizar-contrato/{user_id}', [ClienteController::class, 'actualizarContratoFirmado']);

Route::get('informacioncliente', 'App\Http\Controllers\AsesorController@index')->name('informacioncliente');
Route::get('altaclientease', 'App\Http\Controllers\AsesorController@indexasesor')->name('altaclientease');

Route::view('register', 'register')->name('register');
Route::view('contacto', 'contacto')->name('contacto');
Route::view('privacidad', 'privacidad')->name('privacidad');

Route::post('/calcular-rendimiento', function (Request $request) {
    $cantidad = $request->input('cantidad');
    $duracion = $request->input('duracion');

    // Validar que el valor de cantidad sea numérico
    if (!is_numeric($cantidad)) {
        // Redirigir al formulario con un mensaje de error
        return redirect()->back()->withErrors(['cantidad' => 'El valor de cantidad debe ser numérico.']);
    }

    $rendimientoMensual = $cantidad * 0.01;
    $rendimientoAnual = $rendimientoMensual * 12 * $duracion;

    return view('invinfo', compact('cantidad', 'rendimientoMensual', 'rendimientoAnual', 'duracion'));
})->name('calcular-rendimiento');


Route::view('/invinfo', 'invinfo')->name('invinfo');

Route::view('login', 'login')->name('login')->middleware('guest');
Route::get('mailclientealta', 'App\Http\Controllers\ClienteController@getdatos')->name('mailclientealta');
Route::view('mailclientealta', 'mailclientealta')->name('mailclientealta');
Route::post('post-login', 'App\Http\Controllers\AuthController@postLogin')->name('postLogin');
Route::view('/registro/lobbyregister', '/registro/lobby/register')->name('signin')->middleware('guest');
Route::post('postRegistration', 'App\Http\Controllers\AuthController@postRegistration')->name('postRegistration');
Route::post('postRegistrationCliente', 'App\Http\Controllers\ClienteController@postRegistrationCliente')->name('postRegistrationCliente');
Route::get('/asesor/cambiar-contrasena/{id}', 'AsesorController@showChangePasswordForm')->name('cambiar-contrasena');
Route::post('/asesor/guardar-contrasena', 'AsesorController@updatePassword')->name('guardar-contrasena');
Route::post('postRegistrationAsesor', 'App\Http\Controllers\AuthController@postRegistrationAsesor')->name('postRegistrationAsesor');
Route::post('postRegistrationClienteAsesor', 'App\Http\Controllers\AsesorController@postRegistrationClienteAsesor')->name('postRegistrationClienteAsesor');
Route::get('clienteadmin/documentacion/{id}', 'App\Http\Controllers\AuthController@getGallery')->name('documentacion');
Route::get('informacioncliente/documentacionase/{id}', 'App\Http\Controllers\AsesorController@getGallery')->name('documentacionase');
Route::get('datoscliente/documentacioncliente/{id}', 'App\Http\Controllers\ClienteController@getGallery')->name('documentacioncliente');
Route::delete('datoscliente/documentacioncliente/{id}', 'App\Http\Controllers\ClienteController@eliminarFoto')->name('eliminar.foto');
Route::get('informacioninversiones/{id}', 'App\Http\Controllers\AsesorController@indexinversion')->name('informacioninversiones');
Route::get('logout', 'App\Http\Controllers\AuthController@logout')->name('logout');
Route::view('terms', 'terminosycondiciones')->name('terms');
Route::view('/project/VillaCampestre', '/project')->name('projectVilla');
Route::view('/project/TorreAbi', '/project1')->name('projectTorre');
Route::get('photos', 'App\Http\Controllers\AuthController@indexusuario')->name('photos');
Route::put('datoscliente/{id}', 'App\Http\Controllers\ClienteController@updatedocumentos')->name('updatedocumentos');
Route::put('documentos/{id}', 'App\Http\Controllers\ClienteController@update')->name('update');
Route::put('/update/{id}', 'App\Http\Controllers\AsesorController@aprobar')->name('aprobado');
Route::resource('mail','MailController');
Route::get('/cambiar-contrasena/{id}', 'App\Http\Controllers\PasswordChangeController@showChangeForm')->name('password.change');
Route::post('/password/update/{id}', 'App\Http\Controllers\PasswordChangeController@change')->name('cambiarcontraseña');
Route::get('altabeneficiario', 'App\Http\Controllers\ClienteController@datosinv')->name('altabeneficiario');
Route::view('trigales', 'trigales')->name('trigales');
Route::view('sibal', 'sibal')->name('sibal');
Route::view('anbani', 'anbani')->name('anbani');

Route::view('sanblas', 'sanblas')->name('sanblas');
Route::view('altacliente', 'altacliente')->name('altacliente');
Route::get('admininv/{id}', 'App\Http\Controllers\adminController@indexinversion')->name('inversionesadm');
Route::get('asesoralta', 'App\Http\Controllers\adminController@indexasesor')->name('asesoralta');
Route::get('/views/inc/navphotos', 'App\Http\Controllers\AsesorController@indexinicio')->name('navphotos');
Route::get('invadmin/{id}', 'App\Http\Controllers\adminController@indexinversion')->name('invadmin');
Route::get('altabeneficiario/{id}', 'App\Http\Controllers\ClienteController@altabeneficiario')->name('altabeneficiario');
Route::get('user/{id}/data', 'App\Http\Controllers\AsesorController@getUserData')->name('clienteData');
Route::post('/login', [adminController::class, 'store'])
    ->name('login.store');
    Route::get('/views/inc/navphotos', 'App\Http\Controllers\ClienteController@menuasesor')->name('navphotos');

    Route::get('enviar', function(){
        Mail::send('maildaniel', [] , function($message){
            $message->from('programacion@finverr.com', 'FINVERR');
            $message->to('danielzramirezcarranza@gmail.com', 'FINVERR')->subject('PROCESO DE REINVERSIÓN');
        });
        return "seend mail";
    });

    Route::get('mailclientealta', 'App\Http\Controllers\ClienteController@getdatos')->name('mailclientealta');


Route::get('/admin', [adminController::class, 'index'])
    ->middleware('auth.admin')
    ->name('admin.index');

// Seccion de vistas relacionadas con el inicio de sesión del usuario


Route::prefix('registro')->middleware('auth', 'verified')->group(function () {
    //admin
Route::view('administrador', 'administrador')->name('administrador');
Route::get('clienteadmin', 'App\Http\Controllers\adminController@index')->name('clienteadmin');
Route::get('infoclienteadm/{id}', 'App\Http\Controllers\adminController@indexinfo')->name('infoclienteadm');
Route::get('asesoralta', 'App\Http\Controllers\adminController@indexasesor')->name('asesoralta');
//-----------------------------------------------------------
    Route::get('lobby', 'App\Http\Controllers\ClienteController@index')->name('lobby');
    Route::view('asesor', 'asesor')->name('asesor');
    Route::post('/cambiar-contrasena', [AsesorController::class, 'cambiarContrasena'])->name('cambiar-contrasena');
    Route::view('contraseña', 'contraseña')->name('contraseña');
    Route::post('save_data','App\Http\Controllers\ClienteController@save_data')->name('save_data');
    Route::view('documentos', 'documentos')->name('documentos');
    Route::view('fininv', 'fininv')->name('fininv');
    Route::view('inversionfinal', 'inversionfinal')->name('inversionfinal');
    Route::get('altaclientease', 'App\Http\Controllers\AsesorController@indexasesor')->name('altaclientease');
    Route::get('mailclientealta', 'App\Http\Controllers\ClienteController@getdatos')->name('mailclientealta');
    //Route::get('informacioncliente', 'App\Http\Controllers\AsesorController@index')->name('informacioncliente');
    Route::get('invcliente/{id}', 'App\Http\Controllers\ClienteController@getInversiones')->name('invcliente');
    Route::get('comprobanteinv/{id}', 'App\Http\Controllers\ClienteController@getInversionescliente')->name('comprobanteinv');
    Route::post('/validar-contrasena', [ClienteController::class, 'validarContrasena'])->name('validar-contrasena');
    Route::get('altabeneficiario', 'App\Http\Controllers\ClienteController@indexdatoss')->name('altabeneficiario');
    Route::get('altacomprobante/{id}', 'App\Http\Controllers\ClienteController@getInversioness')->name('altacomprobante');
    Route::get('datoscliente', 'App\Http\Controllers\ClienteController@indexdatos')->name('datoscliente');
    Route::get('documentos', 'App\Http\Controllers\ClienteController@documentos')->name('documentos');
    Route::get('contratopre', 'App\Http\Controllers\ClienteController@precontrato')->name('contratopre');
    Route::view('contratoespecial', 'contratoespecial')->name('contratoespecial');
    Route::put('/cambiar-clabe/{id}', 'App\Http\Controllers\ClienteController@cambiarClabe')->name('postCambiarClabe');
    Route::get('benfcliente/{id}', 'App\Http\Controllers\ClienteController@getBeneficiario')->name('benfcliente');
    Route::put('/update/{id}', 'App\Http\Controllers\AsesorController@desaprobar')->name('desaprobar');
    Route::post('postBeneficiario', 'App\Http\Controllers\ClienteController@postBeneficiario')->name('postBeneficiario');
    Route::post('postInversion', 'App\Http\Controllers\ClienteController@postInversion')->name('postInversion');
    Route::get('contrato', 'App\Http\Controllers\ClienteController@contrato');
    Route::post('postContrato', 'App\Http\Controllers\ClienteController@postContrato')->name('postContrato');
    Route::post('postContratopre', 'App\Http\Controllers\ClienteController@postContratopre')->name('postContratopre');
    Route::post('postComprobante', 'App\Http\Controllers\ClienteController@postComprobante')->name('postComprobante');
    Route::post('postProcedencia', 'App\Http\Controllers\ClienteController@postProcedencia')->name('postProcedencia');
    Route::post('postAccount', 'App\Http\Controllers\ClienteController@postAccount')->name('postAccount');
    Route::patch('patchInversion/{id}', 'App\Http\Controllers\ClienteController@patchInversion')->name('patchInversion');
    Route::get('/Reinversion/{id}', 'App\Http\Controllers\ClienteController@reinversionVista')->name('reinversion');
    Route::post('/Reinversion/contrato/{id}', 'App\Http\Controllers\ClienteController@Reinversion')->name('postReinversion');
    Route::view('registerempleado', 'register')->name('registercliente');
    Route::get('user/{id}/data', 'App\Http\Controllers\adminController@getUserData')->name('userData');
    Route::put('/baja/{id}', 'App\Http\Controllers\adminController@baja')->name('baja');
    Route::put('/datoscliente/{id}', 'App\Http\Controllers\ClienteController@updateDatos')->name('updateDatos');
    Route::put('/infoclienteadm/{id}', 'App\Http\Controllers\adminController@updateDatos')->name('updateDatoscliente');
    Route::put('/activo/{id}', 'App\Http\Controllers\adminController@activo')->name('activo');
    Route::get('user/data/verInversion/{inversion}', 'App\Http\Controllers\adminController@showInvestment')->name('verInversion');
    Route::get('user/data/editarInversion/{inversion}', 'App\Http\Controllers\adminController@editInvestment')->name('editarInversion');
    Route::put('user/data/updateInversion/{inversion}', 'App\Http\Controllers\adminController@updateInvestment')->name('actualizarInversion');
    Route::get('/actualizarasesor/{id}', 'App\Http\Controllers\ClienteController@indexdatosinicioase')->name('actualizarasesor');
    Route::put('/actualizarasesor/{id}', 'App\Http\Controllers\ClienteController@updateDatosinicioase')->name('updateDatosinicioasesor');
    Route::get('/actualizar/{id}', 'App\Http\Controllers\ClienteController@indexdatosinicio')->name('actualizar');
Route::put('/actualizar/{id}', 'App\Http\Controllers\ClienteController@updateDatosinicio')->name('updateDatosinicio');
Route::post('/upload', [ClienteController::class, 'uploadImage'])->name('image.upload');
});

Route::prefix('registro')->middleware('auth', 'verified')->group(function () {


    //Route::get('informacioncliente', 'App\Http\Controllers\AsesorController@index')->name('informacioncliente');

    Route::put('informacioncliente/{id}', 'App\Http\Controllers\AsesorController@activa')->name('activa');
    Route::get('perfilcliente', 'App\Http\Controllers\ClienteController@perfil')->name('perfilcliente');
});

Route::prefix('cliente')->middleware('auth', 'verified', 'roles:cliente')->group(function () {

    Route::get('perfilcliente', 'App\Http\Controllers\ClienteController@perfil')->name('perfilcliente');
});


//Seccion de vistas para control de PDF
Route::get('/customer/print-contract-pdf', 'App\Http\Controllers\pdfController@printContractPDF')->name('customer.printcontractpdf')->middleware('auth', 'verified');
Route::get('/customer/print-pdf', 'App\Http\Controllers\pdfController@printPDF')->name('customer.printpdf')->middleware('auth', 'verified');
Route::get('/customer/print-pdf-pre', 'App\Http\Controllers\pdfController@printPDFpre')->name('customer.printpdfpre')->middleware('auth', 'verified');
Route::get('/customer/control/print-pdf/{id_inversion}', 'App\Http\Controllers\pdfController@printPDFControl')->name('customerControl.printpdf')->middleware('auth', 'verified');
Route::get('/customer/admin/print-pdf/{id_inversion}', 'App\Http\Controllers\pdfController@printPDFadmin')->name('customer.admin.printpdf')->middleware('auth', 'verified');
Route::get('/Reinversion/pdf', 'App\Http\Controllers\pdfController@contratoReinversion')->name('reinversion.pdf')->middleware('auth', 'verified');
Route::get('/Reinversion/pdf/{id}', 'App\Http\Controllers\pdfController@showContratoReinversion')->name('reinversionControl.pdf')->middleware('auth', 'verified');

// Seccion de vistas relacionadas con sesión del admin
Route::prefix('admin')->middleware('auth', 'verified', 'roles:admin')->group(function () {
    Route::get('beneficiarios/{id}', 'App\Http\Controllers\adminController@getBeneficiario')->name('beneficiarios');
    Route::get('inversiones/{id}', 'App\Http\Controllers\adminController@getInversiones')->name('inversiones');
    Route::get('galeria/{id}', 'App\Http\Controllers\adminController@getGallery')->name('gallery');
    Route::post('createEmpresa', 'App\Http\Controllers\adminController@createEmpresa')->name('createEmpresa');
    Route::post('activateEmpresa', 'App\Http\Controllers\adminController@activateEmpresa')->name('activateEmpresa');
    Route::post('createContrato', 'App\Http\Controllers\adminController@createContrato')->name('createContrato');
    Route::post('createEstado', 'App\Http\Controllers\adminController@createEstado')->name('createEstado');


});

// Authentication Routes...

Route::view('correo', 'correo')->name('correo');
// Password Reset Routes
Route::get('password/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');
Route::get('password/confirm', 'App\Http\Controllers\Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'App\Http\Controllers\Auth\ConfirmPasswordController@confirm');
Route::get('correo', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', 'App\Http\Controllers\Auth\VerificationController@verify')
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');