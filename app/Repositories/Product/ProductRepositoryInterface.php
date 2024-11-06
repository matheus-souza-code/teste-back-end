<?php 

namespace App\Repositories\Product;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function get(array $query = [], bool $paginate = true, int $limit = 20): LengthAwarePaginator;
    public function find(int $id): ?Product;
    public function create(array $data): Product;
    public function updateOrCreate(array $data): Product;
    public function update(Product $product, array $data): Product;
    public function delete(Product $product): bool;
}