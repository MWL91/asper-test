<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Objects;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->objects = new Objects();
        $objects = $this->objects->paginate(10);

        return view('home', ['objects'=>$objects]);
    }

    public function data(Request $request)
    {
        try
        {
            $input = $request->validate([
                'page'=>'integer|required',
                'order'=>'in:DESC,ASC|required',
                'column'=>'in:object,city,street|required'
            ]);


            switch($input['column'])
            {
                case 'city':
                    $column = 'cities.name';
                break;
                case 'street':
                    $column = 'streets.name';
                break;
                default:
                    $column = 'name';
                break;
            }

            $this->objects = new Objects();
            return $this->objects
                ->leftJoin('streets', 'streets.id', '=', 'objects.street_id')
                ->leftJoin('cities', 'cities.id', '=', 'streets.city_id')
                ->select(['objects.*', 'streets.name as street', 'cities.name as city'])
                ->with('years')
                ->orderBy($column, $input['order'])
                ->paginate(10);
        }
        catch(\Exception $e)
        {
            return response($e->getMessage(), 400);
        }
    }
}
