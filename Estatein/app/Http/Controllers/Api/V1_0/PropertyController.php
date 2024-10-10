<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PropertyController extends Controller
{
    public function index()
    {
        // Define a cache key to store the properties
        $cacheKey = 'properties';

        // Check if the properties are already cached
        $properties = Cache::remember($cacheKey, 600, function () {
            // If not cached, retrieve from the database and cache it for 600 seconds (10 minutes)
            return Property::all();
        });

        return response()->json($properties);
    }

    public function filter(Request $request)
    {
        // Retrieve the input parameters, which might be null
        $searchTerm = $request->input('searchTerm');
        $location = $request->input('location');
        $propertyType = $request->input('propertyType');
        $pricingRange = $request->input('pricingRange');
        $propertySize = $request->input('propertySize');
        $buildYear = $request->input('buildYear');

        // Start the query for the Property model
        $query = Property::query();

        // Conditionally add where clauses only if the parameters are not null
        if ($searchTerm) {
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        if ($location) {
            $query->where('location', $location);
        }

        if ($propertyType) {
            $query->where('type', $propertyType);
        }

        if ($pricingRange) {
            $range = explode('-', $pricingRange);
            if (count($range) == 2) {
                $query->whereBetween('price', [(int)$range[0], (int)$range[1]]);
            }
        }

        if ($propertySize) {
            $query->where('size', '>=', $propertySize);
        }

        if ($buildYear) {
            // Check if the input is a range like "2010-2020"
            $yearRange = explode('-', $buildYear);
            if (count($yearRange) == 2) {
                $query->whereBetween('build_year', [(int)$yearRange[0], (int)$yearRange[1]]);
            } else {
                // If it's not a range, assume it's a single year
                $query->whereYear('build_year', $buildYear);
            }
        }

        // Execute the query and get the results
        $properties = $query->get();

        // Return the results as a JSON response
        return response()->json($properties);
    }
}
