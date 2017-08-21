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
		$success = 1;
		$data = '';

		$main_route = MainRoute::where('origin_lat', '=', $start_lat)
								->where('origin_lng', '=', $start_lng)
								->where('destination_lat', '=', $end_lat)
								->where('destination_lng', '=', $end_lng)
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