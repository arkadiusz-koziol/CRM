<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;



class Car extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'name',
        'description',
        'registration_number',
        'technical_details'
    ];

    public function getId() : int 
    {
        return $this->id;
    }
}
