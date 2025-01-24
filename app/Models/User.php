<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;  // Importe a interface JWTSubject
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject  // Implemente a interface JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * Atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Obtenha o identificador que será armazenado na reclamação JWT subject.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Retorna o ID do usuário (por padrão, é o campo 'id')
    }

    /**
     * Retorna as declarações personalizadas que serão adicionadas ao JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];  // Aqui você pode adicionar claims personalizadas, se necessário
    }
}
