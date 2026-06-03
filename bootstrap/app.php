<?php

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException as ValidationValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->append(ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
                return response()->json([
                    'status' => 'Não autenticado',
                    'message' => 'Token de autenticação inválido ou ausente.'
                ], 401);
        });
        $exceptions->render(function (MissingAbilityException $e, $request) {
                return response()->json([
                    'status' => 'Token inválido',
                    'message' => 'O token fornecido é inválido ou não possui permissão.'
                ], 401);
        });
        $exceptions->render(function (ValidationValidationException $e, $request) {
                return response()->json([
                    'status' => 'Falha',
                    'message' => $e->errors(),
                ], 422);
        });

        // Model não encontrada (404)
        $exceptions->render(function (ModelNotFoundException $e, $request) {
                return response()->json([
                    'status' => 'Não encontrado',
                    'message' => 'O recurso solicitado não foi encontrado.'
                ], 404);
        });

        // Erros inesperados (500)
        $exceptions->render(function (Throwable $e, $request) {
                return response()->json([
                    'status' => 'Erro interno',
                    'message' => 'Ocorreu um erro inesperado no servidor.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
        });
    })->create();
