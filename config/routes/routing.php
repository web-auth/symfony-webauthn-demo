<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use App\Controller\AttestationRequestController;
use App\Controller\AttestationResponseController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use App\Controller\HomepageController;

$routes = new RouteCollection();

// Authentication API
$routes->add('api_attestation_request', new Route('/api/attestation/options',
    ['_controller' => AttestationRequestController::class],
    [],[],null,[],['POST']
));
$routes->add('api_attestation_response', new Route('/api/attestation/result',
    ['_controller' => AttestationResponseController::class],
    [],[],null,[],['POST']
));

// Home
$routes->add('app_home', new Route('/{reactRouting}',
    [
        '_controller' => [HomepageController::class, 'home'],
        'reactRouting' => null
    ],
    ['reactRouting' => '.*']
));

return $routes;
