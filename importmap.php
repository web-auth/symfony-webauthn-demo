<?php

declare(strict_types=1);

return [
    'app' => [
        'path' => 'app.js',
        'preload' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => '@symfony/stimulus-bundle/loader.js',
    ],
    '@hotwired/stimulus' => [
        'downloaded_to' => 'vendor/@hotwired/stimulus.js',
        'url' => 'https://cdn.jsdelivr.net/npm/@hotwired/stimulus@3.2.2/+esm',
    ],
    'typed.js' => [
        'downloaded_to' => 'vendor/typed.js.js',
        'url' => 'https://cdn.jsdelivr.net/npm/typed.js@2.0.132/+esm',
    ],
    '@simplewebauthn/browser' => [
        'downloaded_to' => 'vendor/@simplewebauthn/browser.js',
        'url' => 'https://cdn.jsdelivr.net/npm/@simplewebauthn/browser@8.3.1/+esm',
    ],
];
