<?php 

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function get(array $query = [], bool $paginate = true, int $limit = 20): LengthAwarePaginator
    {
        $queryBuilder = Category::query()->orderBy('id', 'desc');
    
        if (!empty($query['search'])) {
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('id', 'LIKE', "%{$query['search']}%")
                  ->orWhere('name', 'LIKE', "%{$query['search']}%");
            });
        }
    
        return $paginate ? $queryBuilder->paginate($limit) : $queryBuilder->get();
    }

    public function find(int $id): ?Category
    {
        return Category::find($id);
    }

    public function create(array $data): Category
    {
        return $this->category->create($data);
    }

    public function updateOrCreate(array $data): Category
    {
        return $this->category->updateOrCreate($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->refresh();
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}