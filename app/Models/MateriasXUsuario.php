<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MateriasXUsuario extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'materias_x_usuarios';
    protected $fillable = ['materias_id', 'users_id'];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materias_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class, 'materias_x_usuarios_id');
    }
}