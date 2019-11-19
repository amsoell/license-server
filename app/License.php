<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client as GuzzleClient;

class License extends Model
{
    protected $fillable = [
        'machine_id',
    ];

    protected $primaryKey = 'machine_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::created(function (License $license) {
            if ($endpoint = config('services.slack.webhook_endpoint')) {
                $fields = [
                    [
                        'type' => 'mrkdwn',
                        'text' => sprintf("*IP:*\n%s", app('Illuminate\Http\Request')->ip()),
                    ],
                    [
                        'type' => 'mrkdwn',
                        'text' => sprintf("*Machine ID:*\n%s", $license->machine_id),
                    ],
                ];

                try {
                    if (($ipstack_api_key = config('services.ipstack.api_key')) &&
                        ($response = (new GuzzleClient())->request('POST', sprintf('http://api.ipstack.com/%s?access_key=%s', app('Illuminate\Http\Request')->ip(), $ipstack_api_key))) &&
                        ($location = json_decode($response->getBody()->getContents()))) {
                        $fields[] = [
                            'type' => 'mrkdwn',
                            'text' => sprintf("*Location:*\n%s", $location->city ?? $location->country ?? 'unknown'),
                        ];
                    }

                    (new GuzzleClient())->request('POST', $endpoint, [
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
        });
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class, 'machine_id');
    }

    public function getIsValidAttribute()
    {
        return ! $this->invalidated_at;
    }
}
