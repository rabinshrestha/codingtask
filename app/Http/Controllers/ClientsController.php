<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

// use Illuminate\Http\Request;
use Request;
use Illuminate\Support\Facades\Validator;

class ClientsController extends Controller {

	private static $validationRule = [
        'fullName' 		=> 'required|max:50|alpha_num',
        'inputGender'	=> 'required',
        'phone' 		=> 'numeric|max:10',
        'email' 		=> 'email|max:100',
        'address'		=> 'max:100',
        'nationality'	=> 'required|max:50',
        'dateOfBirth'	=> 'required|date',
        'education'		=> 'max:100',
        'contactMode'	=> 'required',
    ];

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
			dd($validator->messages());
		// dd($validator);
		// $input = Request::all();
		// dd($input);
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
