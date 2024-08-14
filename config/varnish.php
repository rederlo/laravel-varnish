<?php

return [
    /*
     * The hostname(s) this Laravel app is listening to.
     */
    'host' => [env('APP_URL')],

    /*
     * The location of the file containing the administrative password.
     */
    'administrative_secret' => env('VARNISH_SECRET'),

    /*
     * The port where the administrative tasks may be sent to.
     */
    'administrative_host' => env('VARNISH_HOST', '127.0.0.1'),

    /*
     * The port where the administrative tasks may be sent to.
     */
    'administrative_port' => env('VARNISH_PORT', 6082),

    /*
     * The default amount of minutes that content rendered using the `CacheWithVarnish`
     * middleware should be cached.
     */
    'cache_time_in_minutes' => 60 * 24,

    /*
     * The name of the header that triggers Varnish to cache the response.
     */
    'cacheable_header_name' => 'X-Cacheable',
];
