<?php 

namespace App\Services;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Repositories\Product\ProductRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService 
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function get(array $querys = []): LengthAwarePaginator
    {
        return $this->productRepository->get($querys);
    }

    public function create(StoreProductRequest $request) 
    {
        return response()->json([
            'message' => 'Product created successfully!',
            'data' => $this->productRepository->create($request->all()),
        ], 201);
    }

    public function update(Product $product, UpdateProductRequest $request) 
    {
        if (!$product->exists) {
            throw new Exception('Product not found.', 404);
        }

        return response()->json([
            'message' => 'Product updated successfully!',
            'data' => $this->productRepository->update($product, $request->all()),
        ], 200);
    }

    public function delete(Product $product) 
    {
        if (!$product->exists) {
            throw new Exception('Product not found.', 404);
        }

        return response()->json([
            'message' => 'Product deleted successfully!',
            'success' => $this->productRepository->delete($product),
        ], 200);
    }
}