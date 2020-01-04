<?php

declare(strict_types=1);

use App\Controller\ChangeDisplayNameController;
use App\Controller\HomepageController;
use App\Controller\LogoutController;
use App\Controller\ProfileController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->import('.', 'webauthn');

// Profile API
    $routes->add('api_profile', '/api/profile')
        ->controller(ProfileController::class)
        ->methods(['GET'])
        ->schemes(['https'])
    ;

    $routes->add('api_change_display_name', '/api/change_display_name')
        ->controller(ChangeDisplayNameController::class)
        ->methods(['PUT'])
        ->schemes(['https'])
    ;

// Logout API
    $routes->add('api_logout', '/api/logout')
        ->controller(LogoutController::class)
        ->methods(['GET'])
        ->schemes(['https'])
    ;

// Home
    $routes->add('app_home', '/{reactRouting}')
        ->controller(HomepageController::class)
        ->requirements(['reactRouting' => '^(?!_).*$'])
        ->defaults(['reactRouting' => null])
    ;
};
