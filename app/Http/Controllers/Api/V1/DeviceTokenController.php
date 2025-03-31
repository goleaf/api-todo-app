<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    /**
     * Store a device token for push notifications.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        DeviceToken::updateOrCreate(
            ['player_id' => $request->token],
            [
                'user_id' => auth()->id(),
                'device_type' => $request->header('User-Agent') ?
                    (str_contains($request->header('User-Agent'), 'iPhone') ? 'ios' : 'android') :
                    'android',
            ]
        );

        return response()->json(['message' => 'Device token stored successfully']);
    }
}
