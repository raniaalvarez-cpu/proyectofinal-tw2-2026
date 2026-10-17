<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tipo extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'tipos';
    protected $fillable = ['tipo'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tipos_id');
    }
}