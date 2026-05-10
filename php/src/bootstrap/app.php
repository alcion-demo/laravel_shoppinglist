<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use App\Http\Middleware\AdminMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return redirect()->route('login')->with('status', 'ログインしてください。');
        });

        $exceptions->render(function (HttpException $e, $request) {
        if ($e->getStatusCode() === 419) {
            return redirect()->route('login')
                ->with('error', 'セッションが切れました。再度ログインしてください。');
        }
    });