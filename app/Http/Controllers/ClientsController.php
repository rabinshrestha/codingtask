<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Illuminate\Support\Facades\Validator;

use League\Csv\Reader;
use League\Csv\Writer;
use \SplFileObject;

class ClientsController extends Controller {

	private static $validationRule = [
        'fullName' 		=> 'required|min:5|max:50',
        'inputGender'	=> 'required',
        'phone' 		=> 'numeric|digits_between:0,10',
        'email' 		=> 'email|max:100',
        'address'		=> 'max:100',
        'nationality'	=> 'required|max:50',
        'dateOfBirth'	=> 'required|date',
        'education'		=> 'max:100',
        'contactMode'	=> 'required',	
    ];

    private static $csvFileName;

    public function __construct(){
    	ClientsController::$csvFileName = storage_path(). '/clients.csv';
    }

    /**
     * Get all clients list
     * @return Array
     */
    private function getAllClients(){
    	$reader = Reader::createFromPath(ClientsController::$csvFileName);
		$data = $reader->fetchAll(); 
		return $data;
    }
        

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		dd($this->getAllClients());
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

		$writer = Writer::createFromPath(new SplFileObject(ClientsController::$csvFileName, 'a+'), 'w');
		$currentClients = $this->getAllClients();
		if(!empty($currentClients))
			$writer->insertAll($currentClients);
		$writer->insertOne($input);
		return "Data Inserrted";
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
