<?php 

namespace App\Repositories\Product;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function get(array $query = [], bool $paginate = true, int $limit = 20): LengthAwarePaginator
    {
        $queryBuilder = Product::query()->orderBy('id', 'desc');
    
        if (!empty($query['search'])) {
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('id', 'LIKE', "%{$query['search']}%")
                  ->orWhere('name', 'LIKE', "%{$query['search']}%");
            });
        }

        if (!empty($query['category'])) {
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('category', 'LIKE', "%{$query['category']}%");
            });
        }

        if (isset($query['allowEmptyImages']) && $query['allowEmptyImages'] == false) {
            $queryBuilder->where(function ($q) {
                $q->whereNotNull('image_url')->where('image_url', '!=', '');
            });
        }
    
        return $paginate ? $queryBuilder->paginate($limit) : $queryBuilder->get();
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function create(array $data): Product
    {
        return $this->product->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->refresh();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}