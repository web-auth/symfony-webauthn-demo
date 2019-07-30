<?php

declare(strict_types=1);

use App\Controller\AttestationRequestController;
use App\Controller\AttestationResponseController;
use App\Controller\ChangeDisplayNameController;
use App\Controller\HomepageController;
use App\Controller\LogoutController;
use App\Controller\ProfileController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->import('.', 'webauthn');

    $routes->add('api_attestation_request', '/api/register/options')
        ->controller(AttestationRequestController::class)
        ->methods(['POST']);
    $routes->add('api_attestation_response', '/api/register')
        ->controller(AttestationResponseController::class)
        ->methods(['POST']);

// Profile API
    $routes->add('api_profile', '/api/profile')
        ->controller(ProfileController::class)
        ->methods(['GET']);

    $routes->add('api_change_display_name', '/api/change_display_name')
        ->controller(ChangeDisplayNameController::class)
        ->methods(['PUT']);

// Logout API
    $routes->add('api_logout', '/api/logout')
        ->controller(LogoutController::class)
        ->methods(['GET']);

// Home
    $routes->add('app_home', '/{reactRouting}')
        ->controller(HomepageController::class)
        ->requirements(['reactRouting' => '^(?!_).*$'])
        ->defaults(['reactRouting' => null]);
};
