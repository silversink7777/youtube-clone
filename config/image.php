<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. Depending on your PHP setup, you can choose one of them.
    |
    | Included drivers:
    | - \Intervention\Image\Drivers\Gd\Driver::class
    | - \Intervention\Image\Drivers\Imagick\Driver::class
    |
    */

    'driver' => env('IMAGE_DRIVER', extension_loaded('imagick') 
        ? \Intervention\Image\Drivers\Imagick\Driver::class 
        : \Intervention\Image\Drivers\Gd\Driver::class),
]; 