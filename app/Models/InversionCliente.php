<?php
namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class InversionCliente extends Model
{
    //
    protected $primaryKey = 'id';
    protected $table = 'inversiones_cliente';
    protected $fillable = [
        'user_id', 'cantidad', 'folio', 'cuenta_inversion', 'cuenta_pago_rendimientos', 'tasa_mensual', 'fecha_inicio', 'fecha_termino', 'empresa_inversion_id',
        'contrato_inversion_id', 'estado_inversion_id', 'contratofirmado', 'cfdi' ,
    ];


    public static function obtener($userId, $solicitud)
    {
        switch ($solicitud) {
            case '1':
                return InversionCliente::where('user_id', $userId)
                    ->whereIn('estado_inversion_id', [1, 4, 5, 6, 7, 8])
                    ->paginate(3);
                break;
            case '2':
                return InversionCliente::where([
                    ['user_id', $userId],
                    ['estado_inversion_id', 2],
                ])->paginate(3);
                break;
            case '3':
                return InversionCliente::where([
                    ['user_id', $userId],
                    ['estado_inversion_id', 3],
                ])->paginate(3);
                break;
        }

    }
    public static function obtenerPlazo($plazo)
    {
        switch ($plazo) {
            case '1 año':
                return Carbon::now()->addYear();
                break;
            case '2 años':
                return Carbon::now()->addYears(2);
                break;
            case '3 años':
                return Carbon::now()->addYears(3);
                break;
            case '4 años':
                return Carbon::now()->addYears(4);
                break;
        }
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
}