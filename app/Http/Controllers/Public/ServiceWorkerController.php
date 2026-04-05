<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

final class ServiceWorkerController extends Controller
{
    public function __invoke(): Response
    {
        $cacheName = 'pcm-portal-shell-v1';

        $script = <<<JS
const CACHE_NAME = '{$cacheName}';
const APP_SHELL = ['/', '/berita', '/agenda', '/amal-usaha', '/program', '/penyaluran'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(keys
            .filter((key) => key !== CACHE_NAME)
            .map((key) => caches.delete(key))
        )).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const responseClone = response.clone();

                    caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));

                    return response;
                })
                .catch(() => caches.match(request).then((response) => response || caches.match('/')))
        );

        return;
    }

    event.respondWith(
        caches.match(request).then((cached) => {
            if (cached) {
                return cached;
            }

            return fetch(request).then((response) => {
                if (! response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }

                const responseClone = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));

                return response;
            });
        })
    );
});
JS;

        return response($script, 200, [
            'Content-Type' => 'application/javascript; charset=UTF-8',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }
}
