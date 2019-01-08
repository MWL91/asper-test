<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

// use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ObjectsImport;

use App\Objects;
use App\Years;
use App\Cities;
use App\Streets;
use App\Stats;

class ExcelTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testImportCommand()
    {
    	$this->artisan('xls:import', ['filename'=>'e.xlsx'])
        	 ->expectsOutput('Data import is now completed')
        	 ->assertExitCode(0);
    }

    public function testImportNoFileCommand()
    {
    	$this->artisan('xls:import', ['filename'=>'a.xlsx'])
        	 ->expectsOutput('File not found at path: a.xlsx')
        	 ->assertExitCode(0);
    }

    public function testImportCollection()
    {
    	$collect = collect([
    		[
				'miasto' => 'Warszawa',
				'ulica' => 'Testowa2',
				'rok' => '2019',
				'nazwa_obiektu' => 'Test'
    		]
		]);

		$import = new ObjectsImport();
		$this->assertTrue($import->collection($collect));
    }

    public function testImportCollectionNoDuplicates()
    {
    	$collect = collect([
    		[
				'miasto' => 'Warszawa',
				'ulica' => 'Testowa',
				'rok' => '2019',
				'nazwa_obiektu' => 'Test'
    		],
    		[
				'miasto' => 'Warszawa',
				'ulica' => 'Testowa',
				'rok' => '2019',
				'nazwa_obiektu' => 'Test'
    		],
    		[
				'miasto' => 'Warszawa',
				'ulica' => 'Testowa',
				'rok' => '2019',
				'nazwa_obiektu' => 'Test'
    		]
		]);

    	// is import is valid?
		$import = new ObjectsImport();
		$this->assertTrue($import->collection($collect));

		// is only one city Warszawa in database?
		$cities = new Cities();
		$this->assertEquals(1, $cities->where('name', 'Warszawa')->count());

		// is only one street Testowa with Warszawa in database? 
		$this->assertEquals(1, Streets::with('city')->whereHas('city', function($query) {
            $query->whereIn('name', ['Warszawa', 'Kraków']);
        })->whereIn('name', ['Testowa', 'TestowaB'])->count());

		// is only one object Test on separate street
        $objects = Objects::where('name', 'Test')->get();
        $objects->unique('street_id');

        $this->assertEquals($objects->count(), $objects->unique('street_id')->count());
    }

    public function testStats()
    {
    	$unique_id = [uniqid(), uniqid(), uniqid()];
    	$collect = collect([
    		[
				'miasto' => 'Warszawa'.$unique_id[0],
				'ulica' => 'Testowa'.$unique_id[0],
				'rok' => '2019',
				'nazwa_obiektu' => 'Test'.$unique_id[0]
    		],
    		[
				'miasto' => 'Warszawa'.$unique_id[1],
				'ulica' => 'Testowa'.$unique_id[1],
				'rok' => '2019',
				'nazwa_obiektu' => 'Test'.$unique_id[1]
    		],
    		[
				'miasto' => 'Warszawa'.$unique_id[2],
				'ulica' => 'Testowa'.$unique_id[2],
				'rok' => '2019',
				'nazwa_obiektu' => 'Test'.$unique_id[2]
    		],
		]);

    	// is import is valid? and perform
		$import = new ObjectsImport();
		$this->assertTrue($import->collection($collect));
		$this->assertEquals(3, $import->stats->records);
		$this->assertEquals(0, $import->stats->old_cities);
		$this->assertEquals(0, $import->stats->old_streets);
		$this->assertEquals(3, $import->stats->new_cities);
		$this->assertEquals(3, $import->stats->new_streets);
		$this->assertEquals(3, $import->stats->new_objects);
		$this->assertEquals(3, $import->stats->new_years);

		// do import again
		$import = new ObjectsImport();
		$this->assertTrue($import->collection($collect));
		$this->assertEquals(3, $import->stats->records);
		$this->assertEquals(3, $import->stats->old_cities);
		$this->assertEquals(3, $import->stats->old_streets);
		$this->assertEquals(0, $import->stats->new_cities);
		$this->assertEquals(0, $import->stats->new_streets);
		$this->assertEquals(0, $import->stats->new_objects);
		$this->assertEquals(0, $import->stats->new_years);
    }
}
