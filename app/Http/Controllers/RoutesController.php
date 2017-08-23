<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;

use App\Models\MainRoute;
use App\Models\SubRoute;

class RoutesController extends Controller
{
	public function __construct()
	{
		//
	}

	public function getRoutes()
	{
		$start_lat = Input::get('start_lat');
		$start_lng = Input::get('start_lng');
		$end_lat = Input::get('end_lat');
		$end_lng = Input::get('end_lng');
		$start_text = Input::get('start_text');
		$end_text = Input::get('end_text');

		$success = 1;
		$data = '';

		$main_route = MainRoute::where('origin_name', 'LIKE', '%' . $start_text . '%')
								->where('destination_name', 'LIKE', '%' . $end_text . '%')
								->first();

		if($main_route)
		{
			$sub_routes = SubRoute::where('main_route', '=', $main_route->id)->orderBy('ordering', 'ASC')->get();
		}

		if(!$main_route || !count($sub_routes))
			$success = 0;
		else
			$data = $sub_routes;

		$result['success'] = $success;
		$result['data'] = $data;
		return $result;
	}
}