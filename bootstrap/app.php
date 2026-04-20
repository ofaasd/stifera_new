<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('mhs/*') || $request->is('mahasiswa/*')) {
                return route('mahasiswa.login');
            }

            if ($request->is('pegawai/*') || $request->is('dosen/*') || $request->is('akademik/*')) {
                return route('pegawai.login');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 401);
            }

            if ($request->is('mhs/*') || $request->is('mahasiswa/*')) {
                return redirect()->guest(route('mahasiswa.login'));
            }

            if ($request->is('pegawai/*') || $request->is('dosen/*') || $request->is('akademik/*')) {
                return redirect()->guest(route('pegawai.login'));
            }

            return redirect()->guest(route('login'));
        });
    })->create();
