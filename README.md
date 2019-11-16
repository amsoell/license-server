# License Server

This is an extremely simple and naive software licensing server. It accepts a unique ID and returns whether the license is valid or not. It also logs all checkins.

## Usage

Send an HTTP request to the `/api/v1/checkin/{id}` endpoint. If the `{id}` value has not been seen before, it will create a new license instance and return a 200 status code. The `invalidated_at` date can be set to a non-NULL value, after which requests for that license will be met with a 403 Forbidden status code.

License Server is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
