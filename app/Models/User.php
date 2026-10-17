<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;
    protected $fillable = [
        'tipos_id',
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(Tipo::class, 'tipos_id');
    }

    public function materiasXUsuarios(): HasMany
    {
        return $this->hasMany(MateriasXUsuario::class, 'users_id');
    }

    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(Materia::class, 'materias_x_usuarios', 'users_id', 'materias_id');
    }
}