<?php

return [
    'default' => 'maxmind_database',

    'services' => [

        'maxmind_database' => [
            'driver' => 'maxmind_database',
            'database_path' => database_path('geoip.mmdb'),
        ],

        'maxmind_api' => [
            'driver' => 'maxmind_api',
            'user_id' => 'test',
            'license_key' => 'test',
        ],
    ],
];