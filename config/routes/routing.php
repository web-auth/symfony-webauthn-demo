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

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use App\Controller\HomeController;
use App\Controller\SecurityController;
use App\Controller\ProfileController;

$routes = new RouteCollection();

// Security
$routes->add('app_login', new Route('/login', [
    '_controller' => [SecurityController::class, 'login'],
]));
$routes->add('app_login_assertion', new Route('/login/assertion', [
    '_controller' => [SecurityController::class, 'assertion'],
]));
$routes->add('app_logout', new Route('/logout', [
    '_controller' => [SecurityController::class, 'logout'],
]));
$routes->add('app_login_abort', new Route('/login/abort', [
    '_controller' => [SecurityController::class, 'abort'],
]));
$routes->add('app_register_user', new Route('/register', [
    '_controller' => [SecurityController::class, 'registerUser'],
]));
$routes->add('app_register_public_key', new Route('/register/attestation', [
    '_controller' => [SecurityController::class, 'registerPublicKey'],
]));

// Home
$routes->add('app_home', new Route('/', [
    '_controller' => [HomeController::class, 'home'],
]));

// Profile
$routes->add('app_profile', new Route('/profile', [
    '_controller' => [ProfileController::class, 'profile'],
]));
$routes->add('app_device_registration', new Route('/profile/register_device', [
    '_controller' => [ProfileController::class, 'creation'],
]));

return $routes;
