<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Streets extends Model
{
    protected $fillable = ['name', 'city_id'];

    public function city()
    {
    	return $this->hasOne('App\Cities', 'id', 'city_id');
    }
}
