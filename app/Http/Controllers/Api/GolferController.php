<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Golfer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GolferController extends Controller
{
    /**
     * Get the 500 nearest golfers to given coordinates
     */
    public function nearest(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        // Get the 500 nearest golfers using Haversine formula
        $nearestGolfers = Golfer::selectRaw('
                *,
                (
                    6371 * acos(
                        cos(radians(?)) * 
                        cos(radians(latitude)) * 
                        cos(radians(longitude) - radians(?)) + 
                        sin(radians(?)) * 
                        sin(radians(latitude))
                    )
                ) AS distance
            ', [$latitude, $longitude, $latitude])
            ->orderBy('distance')
            ->limit(500)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'search_coordinates' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ],
                'total_golfers_found' => $nearestGolfers->count(),
                'golfers' => $nearestGolfers->map(function ($golfer) {
                    return [
                        'id' => $golfer->id,
                        'debitor_account' => $golfer->debitor_account,
                        'name' => $golfer->name,
                        'email' => $golfer->email,
                        'born_at' => $golfer->born_at,
                        'latitude' => $golfer->latitude,
                        'longitude' => $golfer->longitude,
                        'distance_km' => round($golfer->distance, 2)
                    ];
                })
            ]
        ]);
    }
}
