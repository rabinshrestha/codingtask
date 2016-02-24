<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

// use Illuminate\Http\Request;
use Request;
use Illuminate\Support\Facades\Validator;

use League\Csv\Reader;
use League\Csv\Writer;

class ClientsController extends Controller {

	private static $validationRule = [
        'fullName' 		=> 'required|min:5|max:50',
        'inputGender'	=> 'required',
        'phone' 		=> 'digits_between:0,10',
        'email' 		=> 'email|max:100',
        'address'		=> 'max:100',
        'nationality'	=> 'required|max:50',
        'dateOfBirth'	=> 'required|date',
        'education'		=> 'max:100',
        'contactMode'	=> 'required',
    ];

    private static $csvFileName = 'clients.csv';

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View('clients.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View('clients.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Request::only('fullName', 'inputGender', 'phone', 'email', 'address', 'nationality', 'dateOfBirth', 'education', 'contactMode');
		$validator = Validator::make($input, ClientsController::$validationRule);
		if($validator->fails())
			return redirect('clients/create')
						->withErrors($validator)
						->withInput();

		//store data
		// $reader = Reader::createFromPath('/path/to/your/csv/file.csv');
		//the $reader object will use the 'r+' open mode as no `open_mode` parameter was supplied.
		$writer = Writer::createFromPath(new SplFileObject(storage_path . ClientsController::$csvFileName, 'a+'), 'w');
		$writer->insertOne(['john', 'doe', 'john.doe@example.com']);
		return "Data Inserrted";
		//the $writer object open mode will be 'w'!!				
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
