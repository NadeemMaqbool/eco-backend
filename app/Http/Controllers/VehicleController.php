<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VehicleController extends Controller
{
    //
    //home.assignment-699172
    // Api link to get last locations of vehicles

    public function lastData($key)
    {
        $apiBaseUrl = config('app.base_url');

        $getLastDataUrl = $apiBaseUrl . '/getLastData?key=' . $key . '&json';

        $results = Http::get($getLastDataUrl)->json()['response'];

        return response()->json(['data' => $results], 200);
    }

    public function getRawData($objId, $startDate, $key)
    {
        //home.assignment-699172
        // Api link to get last locations of vehicles
        $total_distance = 0;
        $stops = 0;
        $visited_locations = [];
        $waypoints = [];

        $startDate = Carbon::parse($startDate)->format('Y-m-d');

        $apiBaseUrl = config('app.base_url');

        $startDate = Carbon::createFromFormat('Y-m-d', $startDate);

        $endDate = $startDate->copy()->addDays(1);

        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate)->format('Y-m-d');


        $getRawData = $apiBaseUrl . '/getRawData?objectId=' . $objId .
            '&begTimestamp=' . $startDate . '&endTimestamp=' . $endDate . '&key=' . $key . '&json';


        $results = Http::get($getRawData)->json()['response'];


        if (count($results) > 0) {
            foreach ($results as $location) {

                if (!in_array($location['Longitude'], $visited_locations, true)) {

                    $total_distance += $location['Distance'];
                    array_push($visited_locations, $location['Longitude']);

                    $temp['location']['lng'] = $location['Longitude'];
                    $temp['location']['lat'] = $location['Latitude'];

                    $waypoints[$stops] = $temp;

                    $stops++;
                }
            }

            $start['lng'] =  $results[0]['Longitude'];
            $start['lat'] = $results[0]['Latitude'];
        }

        if (count($temp) > 0) {
            $end['lng'] =  $temp['location']['lng'];
            $end['lat'] = $temp['location']['lat'];
        }

        $response = [
            'total_distance' => $total_distance,
            'stops' => $stops,
            'waypoints' => $waypoints,
            'start' => $start,
            'end' => $end

        ];

        return response()->json(['data' => $response], 200);
    }
}
