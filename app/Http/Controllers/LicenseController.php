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
            $fields = [
                [
                    'type' => 'mrkdwn',
                    'text' => sprintf("*IP:*\n%s", $request->ip()),
                ],
                [
                    'type' => 'mrkdwn',
                    'text' => sprintf("*Machine ID:*\n%s", $machine_id),
                ],
            ];

            try {
                if (($ipstack_api_key = config('services.ipstack.api_key')) &&
                    ($response = (new $guzzle())->request('POST', sprintf('http://api.ipstack.com/%s?access_key=%s', $request->ip(), $ipstack_api_key))) &&
                    ($location = json_decode($response->getBody()->getContents()))) {
                    $fields[] = [
                        'type' => 'mrkdwn',
                        'text' => sprintf("*Location:*\n%s", $location->city ?? $location->country ?? 'unknown'),
                    ];
                }

                (new $guzzle())->request('POST', $endpoint, [
                    'body' => json_encode([
                        'blocks' => [
                            [
                                'type' => 'section',
                                'text' => [
                                    'type' => 'mrkdwn',
                                    'text' => 'New license issued',
                                ],
                            ],
                            [
                                'type'   => 'section',
                                'fields' => $fields,
                            ],
                        ],
                    ]),
                ]);
            } catch (\Exception $exception) {
            }
        }

        if ( ! $license->is_valid) {
            return response(null, 403);
        }

        $checkin = $license->checkins()->create([
            'request' => [
                'ip'        => $request->ip(),
                'fullUrl'   => $request->fullUrl(),
                'userAgent' => $request->userAgent(),
                'location'  => $location ?? null,
            ],
        ]);

        return response()->json([
            'success' => true,
            'data'    => compact('license', 'checkin'),
        ]);
    }
}
