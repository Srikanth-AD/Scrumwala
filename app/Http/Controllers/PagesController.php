<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class PagesController extends Controller {

	public function about()
	{
		$data = [
		'name' => 'Srikanth AD',
		'email' => 'test@exmaple.com'
		];
		return view('pages.about')->with($data);		
	}

}
