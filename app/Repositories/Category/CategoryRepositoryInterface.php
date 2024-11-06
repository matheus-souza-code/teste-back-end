<?php 

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function get(array $query = [], bool $paginate = true, int $limit = 20): LengthAwarePaginator;
    public function find(int $id): ?Category;
    public function create(array $data): Category;
    public function updateOrCreate(array $data): Category;
    public function update(Category $category, array $data): Category;
    public function delete(Category $category): bool;
}