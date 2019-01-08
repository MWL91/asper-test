<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    protected $fillable = ['name'];

    public function street()
    {
    	return $this->hasMany('App\Streets', 'id');
    }
}
