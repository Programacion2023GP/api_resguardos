<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guards extends Model
{
    use HasFactory;



    /**
     * Especificar la conexion si no es la por default
     * @var string
     */
    
    /**
     * Los atributos que se pueden solicitar.
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'picture',
        'stock_number',
        'description',
        'brand',
        'type',
        'state',
        'serial',
        'airlne',
        'payroll',
        'group',
        'employeed',
        'date',
        'observations',
        'user_id',
    ];
    
    /**
     * Nombre de la tabla asociada al modelo.
     * @var string
     */
    protected $table = 'users_guards';

    /**
     * LlavePrimaria asociada a la tabla.
     * @var string
     */
    protected $primaryKey = 'id';
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * Obtener los usuarios relacionados a un rol.
     */
    // public function users()
    // {
    //     return $this->hasMany(User::class,'role_id','id'); //primero se declara FK y despues la PK
    // }

    /**
     * Valores defualt para los campos especificados.
     * @var array
     */
    // protected $attributes = [
    //     'active' => true,
    // ];
}
