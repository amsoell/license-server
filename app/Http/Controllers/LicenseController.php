<?php

namespace App\Http\Controllers;

use App\License;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function checkin(String $machine_id, Request $request)
    {
        $license = License::firstOrCreate([
            'machine_id' => $machine_id,
        ]);

        if ( ! $license->is_valid) {
            return response(null, 403);
        }

        $checkin = $license->checkins()->create([
            'request' => [
                'ip'        => $request->ip(),
                'fullUrl'   => $request->fullUrl(),
                'userAgent' => $request->userAgent(),
            ],
        ]);

        return response()->json(compact('license', 'checkin'));
    }
}