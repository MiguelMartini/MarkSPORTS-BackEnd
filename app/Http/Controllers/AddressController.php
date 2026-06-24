<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AddressController extends Controller
{

    #[OA\Get(
        path: "/api/addresses",
        summary: "Listar endereços do usuário autenticado",
        description: "Retorna todos os endereços pertencentes ao usuário autenticado.",
        tags: ["Endereços"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de endereços retornada com sucesso"
            ),
            new OA\Response(
                response: 401,
                description: "Usuário não autenticado"
            )
        ]
    )]
    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)
            ->get();
        return response()->json($addresses, 200);
    }

    #[OA\Get(
        path: "/api/addresses/{id}",
        summary: "Busca endereço o endereço específico",
        description: "Retorna um endereço específico do usuário autenticado.",
        tags: ["Endereços"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID do endereço",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Endereço encontrado"
            ),
            new OA\Response(
                response: 404,
                description: "Endereço não encontrado"
            )
        ]
    )]
    public function show(Request $request, string $id)
    {
        $address = Address::where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json($address, 200);
    }
    #[OA\Post(
        path: "/api/addresses",
        summary: "Cadastrar endereço",
        description: "Cria um novo endereço para o usuário autenticado.",
        tags: ["Endereços"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["address", "city", "state", "cep", "number"],
                properties: [
                    new OA\Property(
                        property: "address",
                        type: "string",
                        example: "Rua das Flores"
                    ),
                    new OA\Property(
                        property: "city",
                        type: "string",
                        example: "Lages"
                    ),
                    new OA\Property(
                        property: "state",
                        type: "string",
                        example: "SC"
                    ),
                    new OA\Property(
                        property: "cep",
                        type: "string",
                        example: "88501-000"
                    ),
                    new OA\Property(
                        property: "number",
                        type: "string",
                        example: "123"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Endereço criado com sucesso"
            ),
            new OA\Response(
                response: 422,
                description: "Erro de validação"
            )
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'cep' => 'required|string|max:20',
            'number' => 'required|string|max:20',
        ]);

        $address = Address::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Endereço criado com sucesso.',
            'data' => $address
        ], 201);
    }

    #[OA\Patch(
        path: "/api/addresses/update/{id}",
        summary: "Atualizar endereço",
        description: "Atualiza um endereço pertencente ao usuário autenticado.",
        tags: ["Endereços"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID do endereço",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "address", type: "string", example: "Rua Central"),
                    new OA\Property(property: "city", type: "string", example: "Florianópolis"),
                    new OA\Property(property: "state", type: "string", example: "SC"),
                    new OA\Property(property: "cep", type: "string", example: "88000-000"),
                    new OA\Property(property: "number", type: "string", example: "456")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Endereço atualizado com sucesso"
            ),
            new OA\Response(
                response: 404,
                description: "Endereço não encontrado"
            ),
            new OA\Response(
                response: 422,
                description: "Erro de validação"
            )
        ]
    )]
    public function update(Request $request, string $id)
    {
        $address = Address::where('user_id', auth()->id())
            ->findOrFail($id);

        $validated = $request->validate([
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|max:255',
            'cep' => 'sometimes|string|max:20',
            'number' => 'sometimes|string|max:20',
        ]);

        $address->update($validated);

        return response()->json([
            'message' => 'Endereço atualizado com sucesso.',
            'data' => $address
        ], 200);
    }

    #[OA\Delete(
        path: "/api/addresses/delete/{id}",
        summary: "Excluir endereço",
        description: "Remove um endereço pertencente ao usuário autenticado.",
        tags: ["Endereços"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID do endereço",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Endereço removido com sucesso"
            ),
            new OA\Response(
                response: 404,
                description: "Endereço não encontrado"
            )
        ]
    )]
    public function destroy(string $id)
    {
        $address = Address::where('user_id', auth()->id())
            ->findOrFail($id);

        $address->delete();

        return response()->json([
            'message' => 'Endereço removido com sucesso.'
        ], 200);
    }
}
