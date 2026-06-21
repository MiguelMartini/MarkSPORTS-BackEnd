<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: "/api/products",
        summary: "Listar todos os produtos",
        description: "Retorna todos os produtos",
        tags: ["Produtos"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de Produtos retornada com sucesso"
            ),
        ]
    )]

    public function index()
    {
        $products = Product::get([
            'id',
            'name',
            'description',
            'price',
            'color',
            'quantity',
            'img'
        ]);

        if ($products->isEmpty()) {
            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Nenhum produto encontrado'
            ]);
        }

        return response()->json([
            'status' => "Sucesso",
            'data' => $products
        ], 200);
    }

    #[OA\Post(
    path: "/api/products",
    summary: "Cadastrar produto",
    description: "Cadastra um novo produto.",
    tags: ["Produtos"],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "price", "quantity"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Camiseta"),
                new OA\Property(property: "description", type: "string", example: "Camiseta esportiva"),
                new OA\Property(property: "price", type: "number", format: "float", example: 49.90),
                new OA\Property(property: "color", type: "string", example: "Azul"),
                new OA\Property(property: "quantity", type: "integer", example: 100),
                new OA\Property(property: "img", type: "string", example: "https://site.com/imagem.jpg")
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: "Produto cadastrado com sucesso"
        ),
        new OA\Response(
            response: 422,
            description: "Erro de validação"
        )
    ]
)]
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:45',
                'description' => 'nullable|string|max:80',
                'price' => 'required|numeric',
                'color' => 'nullable|string|max:45',
                'quantity' => 'required|integer|min:0',
                'img' => 'nullable|string'
            ],
            [
                'name.required' => 'O nome do produto é obrigatório.',
                'name.string' => 'O nome deve ser um texto.',
                'name.max' => 'O nome deve ter no máximo 45 caracteres.',

                'description.string' => 'A descrição deve ser um texto.',
                'description.max' => 'A descrição deve ter no máximo 80 caracteres.',

                'price.required' => 'O preço é obrigatório.',
                'price.numeric' => 'O preço deve ser um valor numérico.',

                'color.string' => 'A cor deve ser um texto.',
                'color.max' => 'A cor deve ter no máximo 45 caracteres.',

                'quantity.required' => 'A quantidade é obrigatória.',
                'quantity.integer' => 'A quantidade deve ser um número inteiro.',
                'quantity.min' => 'A quantidade não pode ser negativa.',

                'img.string' => 'A imagem deve ser uma string.'
            ]
        );
        $product = Product::create($validated);

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Produto cadastrado com sucesso.',
            'data' => $product
        ], 201);
    }

    #[OA\Get(
    path: "/api/product/{id}",
    summary: "Buscar produto por ID",
    description: "Retorna os dados de um produto específico.",
    tags: ["Produtos"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID do produto",
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Produto encontrado"
        ),
        new OA\Response(
            response: 404,
            description: "Produto não encontrado"
        )
    ]
)]
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'Erro',
                'message' => 'Produto não encontrado.'
            ], 404);
        }

        return response()->json([
            'status' => 'Sucesso',
            'data' => $product
        ], 200);
    }

    #[OA\Patch(
    path: "/api/product/update/{id}",
    summary: "Atualizar produto",
    description: "Atualiza parcialmente um produto.",
    tags: ["Produtos"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID do produto",
            schema: new OA\Schema(type: "integer")
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "name", type: "string", example: "Camiseta Premium"),
                new OA\Property(property: "description", type: "string", example: "Nova descrição"),
                new OA\Property(property: "price", type: "number", format: "float", example: 59.90),
                new OA\Property(property: "color", type: "string", example: "Preto"),
                new OA\Property(property: "quantity", type: "integer", example: 80),
                new OA\Property(property: "img", type: "string", example: "https://site.com/imagem.jpg")
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: "Produto atualizado com sucesso"
        ),
        new OA\Response(
            response: 404,
            description: "Produto não encontrado"
        ),
        new OA\Response(
            response: 422,
            description: "Erro de validação"
        )
    ]
)]
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'Erro',
                'message' => 'Produto não encontrado.'
            ], 404);
        }

        $validated = $request->validate(
            [
                'name' => 'sometimes|string|max:45',
                'description' => 'sometimes|nullable|string|max:80',
                'price' => 'sometimes|numeric',
                'color' => 'sometimes|nullable|string|max:45',
                'quantity' => 'sometimes|integer|min:0',
                'img' => 'sometimes|nullable|string'
            ],
            [
                'name.string' => 'O nome deve ser um texto.',
                'name.max' => 'O nome deve ter no máximo 45 caracteres.',

                'description.string' => 'A descrição deve ser um texto.',
                'description.max' => 'A descrição deve ter no máximo 80 caracteres.',

                'price.numeric' => 'O preço deve ser um valor numérico.',

                'color.string' => 'A cor deve ser um texto.',
                'color.max' => 'A cor deve ter no máximo 45 caracteres.',

                'quantity.integer' => 'A quantidade deve ser um número inteiro.',
                'quantity.min' => 'A quantidade não pode ser negativa.',

                'img.string' => 'A imagem deve ser uma string.'
            ]
        );

        $product->update($validated);

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Produto atualizado com sucesso.',
            'data' => $product->fresh()
        ], 200);
    }

    #[OA\Delete(
        path: "/api/product/delete/{id}",
        summary: "Deletar produto",
        description: "Deleta um produto pertencente ao usuário autenticado.",
        tags: ["Produtos"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID do Produto",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Produto removido com sucesso"
            ),
            new OA\Response(
                response: 404,
                description: "Produto não encontrado"
            )
        ]
    )]
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'Erro',
                'message' => 'Produto não encontrado.'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => 'Sucesso',
            'message' => 'Produto deletado com sucesso'
        ]);
    }
}
