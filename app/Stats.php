<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
    public $records = 0;
    public $old_cities = 0;
    public $old_streets = 0;
	public $new_cities = 0;
    public $new_streets = 0;
    public $new_objects = 0;
    public $new_years = 0;
}
