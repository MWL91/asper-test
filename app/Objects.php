<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Objects extends Model
{
	protected $fillable = ['name', 'street_id'];
	// protected $appends = ['city'];

    public function street()
    {
    	return $this->hasOne('App\Streets', 'id', 'street_id')->with('city');
    }

    public function years()
    {
    	return $this->hasMany('App\Years', 'object_id');
    }
}
