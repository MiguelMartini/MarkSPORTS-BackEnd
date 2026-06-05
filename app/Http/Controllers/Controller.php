<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Minha API Laravel 12",
    version: "1.0.0",
    description: "Documentação oficial das rotas da API desenvolvida em PHP 8.2."
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Servidor Local de Desenvolvimento"
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    name: "Authorization",
    in: "header",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Insira o token gerado no login para acessar as rotas protegidas."
)]

abstract class Controller
{
    //
}

