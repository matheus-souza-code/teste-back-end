<?php 

namespace App\Services;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Repositories\Category\CategoryRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService 
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    public function get(array $querys = []): LengthAwarePaginator
    {
        return $this->categoryRepository->get($querys);
    }

    public function create(StoreCategoryRequest $request) 
    {
        return response()->json([
            'message' => 'Category created successfully!',
            'data' => $this->categoryRepository->create($request->all()),
        ], 201);
    }

    public function update(Category $category, UpdateCategoryRequest $request) 
    {
        if (!$category->exists) {
            throw new Exception('Category not found.', 404);
        }

        return response()->json([
            'message' => 'Category updated successfully!',
            'data' => $this->categoryRepository->update($category, $request->all()),
        ], 200);
    }

    public function delete(Category $category) 
    {
        if (!$category->exists) {
            throw new Exception('Category not found.', 404);
        }

        return response()->json([
            'message' => 'Category deleted successfully!',
            'success' => $this->categoryRepository->delete($category),
        ], 200);
    }
}