<?php

namespace App\Http\Controllers;

use App\License;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;

class LicenseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function checkin(String $machine_id, Request $request, GuzzleClient $guzzle)
    {
        $license = License::firstOrCreate([
            'machine_id' => $machine_id,
        ]);

        if ($license->wasRecentlyCreated &&
            $endpoint = config('services.slack.webhook_endpoint')) {
            $client = new $guzzle();
            $res = $client->request('POST', $endpoint, [
                'body' => json_encode([
                    'text' => sprintf('New license issued: %s', $machine_id),
                ]),
            ]);
        }

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

        return response()->json([
            'success' => true,
            'data'    => compact('license', 'checkin'),
        ]);
    }
}
