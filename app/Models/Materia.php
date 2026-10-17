<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Materia extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'materias';
    protected $fillable = ['nombre'];

    public function materiasXUsuarios(): HasMany
    {
        return $this->hasMany(MateriasXUsuario::class, 'materias_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'materias_x_usuarios', 'materias_id', 'users_id');
    }
}