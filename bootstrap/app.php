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
        $middleware->alias([
            'resolve.pegawai.context' => \App\Http\Middleware\ResolvePegawaiContext::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->routeIs('mahasiswa.*') || $request->is('mhs') || $request->is('mhs/*') || $request->is('mahasiswa') || $request->is('mahasiswa/*')) {
                return route('mahasiswa.login');
            }

            if ($request->routeIs('pegawai.*') || $request->is('pegawai') || $request->is('pegawai/*') || $request->is('dosen') || $request->is('dosen/*') || $request->is('akademik') || $request->is('akademik/*')) {
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

            $guards = $e->guards();
            if (in_array('mahasiswa', $guards, true)) {
                return redirect()->guest(route('mahasiswa.login'));
            }

            if (in_array('pegawai', $guards, true)) {
                return redirect()->guest(route('pegawai.login'));
            }

            if ($request->routeIs('mahasiswa.*') || $request->is('mhs') || $request->is('mhs/*') || $request->is('mahasiswa') || $request->is('mahasiswa/*')) {
                return redirect()->guest(route('mahasiswa.login'));
            }

            if ($request->routeIs('pegawai.*') || $request->is('pegawai') || $request->is('pegawai/*') || $request->is('dosen') || $request->is('dosen/*') || $request->is('akademik') || $request->is('akademik/*')) {
                return redirect()->guest(route('pegawai.login'));
            }

            return redirect()->guest(route('login'));
        });
    })->create();
