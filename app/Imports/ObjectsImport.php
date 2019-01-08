<?php

namespace App\Imports;

use App\Objects;
use App\Years;
use App\Cities;
use App\Streets;
use App\Stats;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Mail;
use App\Mail\ImportStats;

class ObjectsImport implements ToCollection, WithHeadingRow
{
    public $stats;
    private $existing;
    private $rows;

    /**
    * Import xls file
    * @param array $row - rows from imported file
    *
    * @return null
    */
    public function collection(Collection $rows)
    {
        // assign all imported rows to object
        $this->rows = $rows;

        // prepare existing objects
        $this->prepare_existing();

        // stats counter
        $this->prepare_stats();

        // importing rows form xls file
        foreach($this->rows as $row)
        {
            // get a row input based on heading row
            $input = [
                'city' => $row['miasto'],
                'street' => $row['ulica'],
                'year' => $row['rok'],
                'object' => $row['nazwa_obiektu']
            ];

            // get or create street and city by current row
            $street = $this->get_street($input);
            
            // if object exists check for year update
            $object = Objects::firstOrCreate([
                'name' => $input['object'],
                'street_id' => $street->id
            ]);

            // count new objects for stats
            if($object->wasRecentlyCreated)
            {
                $this->stats->new_objects++;
            }

            // if year not exist, add additional year
            $year = Years::firstOrCreate([
                'year' => $input['year'],
                'object_id' => $object->id
            ]);

            // count new years for stats
            if($year->wasRecentlyCreated)
            {
                $this->stats->new_years++;
            }

        }

        // send email with stats
        Mail::to(env('MAIL_STATS_RECEIVER'))->send(new ImportStats($this->stats));
        return true;
    }

    public function prepare_existing()
    {
        $this->existing = new \stdClass();

        // get unique records from import
        $unique_cities = $this->rows->pluck('miasto')->unique();
        $unique_streets = $this->rows->pluck('ulica')->unique();

        // get all unique existing cities
        $this->existing->cities = Cities::whereIn('name', $unique_cities)->get();
        // get all unique existing streets
        $existing = $this->existing->cities;
        $this->existing->streets = Streets::with('city')->whereHas('city', function($query) use ($existing){
            $query->whereIn('name', $existing->pluck('name'));
        })->whereIn('name', $unique_streets)->get();

        return $this;
    }

    public function prepare_stats()
    {
        $this->stats = new Stats();
        $this->stats->records = $this->rows->count();
        $this->stats->old_cities = $this->existing->cities->count();
        $this->stats->old_streets = $this->existing->streets->count();
        return $this;
    }

    /**
    * Generate street object from current input
    * @param array $input - single import row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function get_street($input)
    {
        // check if street exist
        $street = $this->existing->streets->where('city.name', $input['city'])->where('name', $input['street'])->first();
        if(!$street)
        {
            // check if city exist
            $city = $this->existing->cities->where('name', $input['city'])->first();
            if(!$city)
            {
                // create non existing city
                $this->stats->new_cities++;
                $city = Cities::create([
                    'name'=>$input['city']
                ]);
                $this->existing->cities->push($city);
            }

            // create non existing street
            $this->stats->new_streets++;
            $street = Streets::create([
                'name' => $input['street'],
                'city_id' => $city->id
            ]);
            $this->existing->streets->push($street);
        }

        return $street;
    }
}
