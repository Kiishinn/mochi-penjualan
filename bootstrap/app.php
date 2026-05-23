<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->redirectUsersTo(function () {
            if (auth()->check()) {
                return match (auth()->user()->role) {
                    'owner'         => route('owner.dashboard'),
                    'kepala_cabang' => route('kepala-cabang.dashboard'),
                    'kasir'         => route('kasir.dashboard'),
                    default         => route('login'),
                };
            }
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()->back()->withInput($request->except('password', '_token'))->with('error', 'Sesi Anda telah kedaluwarsa. Silakan coba lagi.');
        });
    })->create();
