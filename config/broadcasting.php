<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "pusher", "ably", "redis", "log", "null"
    |
    */

    'default' => env('BROADCAST_DRIVER', 'socketio'),

    'connections' => [
        'socketio' => [
            'driver' => 'socket.io',
            'host' => env('SOCKETIO_HOST', 'http://localhost'),
            'port' => env('SOCKETIO_PORT', 6001),
            'namespace' => env('SOCKETIO_NAMESPACE', '/'),
        ],
    ],
    
];
