<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        summary: "Registrar um novo usuário",
        description: "Cria um novo usuário no banco de dados e inicializa um carrinho vazio para ele.",
        tags: ["Autenticação"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "last_name", "email", "password", "password_confirmation", "phone"],
                properties: [
                    new OA\Property(property: "name", type: "string", maxLength: 20, example: "João"),
                    new OA\Property(property: "last_name", type: "string", maxLength: 255, example: "Silva"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "joao.silva@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", minLength: 8, example: "senha123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", minLength: 8, example: "senha123"),
                    new OA\Property(property: "phone", type: "string", maxLength: 20, example: "11999998888")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Usuário registrado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Usuário registrado com sucesso"),
                        new OA\Property(property: "user", type: "object", additionalProperties: true)
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Erro de validação nos campos enviados"
            )
        ]
    )]
    public function register(Request $request)
    {
        $messages = [
            // Mensagens para o campo Name
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais de 20 caracteres.',

            // Mensagens para o campo Last Name
            'last_name.required' => 'O sobrenome é obrigatório.',
            'last_name.string' => 'O sobrenome deve ser um texto válido.',
            'last_name.max' => 'O sobrenome não pode ter mais de 255 caracteres.',

            // Mensagens para o campo Email
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Por favor, insira um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está sendo utilizado por outra conta.',

            // Mensagens para o campo Password (incluindo o mínimo)
            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser um texto válido.',
            'password.min' => 'A senha deve conter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não coincide.',

            // Mensagens para o campo Phone
            'phone.required' => 'O número de telefone é obrigatório.',
            'phone.string' => 'O telefone deve ser um texto válido.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
        ];
        $validated = $request->validate([
            'name' => 'required|string|max:20',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
        ], $messages);

        $user = DB::transaction(function () use ($validated) {

            $user = User::create($validated);

            $user->cart()->create();

            return $user;
        });

        return response()->json([
            'message' => 'Usuário registrado com sucesso',
            'user' => $user,
        ], 201);
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Autenticar usuário",
        description: "Verifica as credenciais do usuário e retorna um token de acesso Bearer (Sanctum).",
        tags: ["Autenticação"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "joao.silva@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "senha123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login realizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Login realizado com sucesso."),
                        new OA\Property(property: "token", type: "string", example: "1|abcdefghijklmnopqrstuvwxyz..."),
                        new OA\Property(property: "user", type: "object", additionalProperties: true)
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Credenciais inválidas (E-mail ou senha incorretos)"
            ),
            new OA\Response(
                response: 422,
                description: "Erro de validação (campos obrigatórios ausentes)"
            )
        ]
    )]

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas.'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'token' => $token,
            'user' => $user
        ]);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Revogar token de acesso (Logout)",
        description: "Invalida o token atual do usuário autenticado.",
        tags: ["Autenticação"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout realizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Logout realizado com sucesso")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autorizado (Token ausente ou inválido)"
            )
        ]
    )]
    public function Logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }
}
