<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purok;

class PurokController extends Controller
{
    public function getPurokData(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('m'));

        // Get all purok data
        $allPuroks = Purok::all();
        
        // Filter by year and month
        $purokData = $allPuroks->filter(function($purok) use ($year, $month) {
            if (!isset($purok->date_schedule)) {
                return false;
            }
            
            $date = strtotime($purok->date_schedule);
            return date('Y', $date) == $year && date('m', $date) == $month;
        })->map(function($purok) {
            return [
                'date_schedule' => $purok->date_schedule,
                'purok_name' => $purok->purok_name
            ];
        })->values();

        return response()->json($purokData);
    }
}
