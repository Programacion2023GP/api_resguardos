<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Korima extends Model
{
    protected $table = 'korima';

    use HasFactory;
    protected $fillable = [
        'id',
        'korima',
        'picture',
        'observation',
       
    ];
}
