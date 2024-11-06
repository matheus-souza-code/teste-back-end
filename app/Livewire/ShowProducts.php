<?php

namespace App\Livewire;

use App\Services\CategoryService;
use App\Services\ProductService;
use Livewire\Component;
use Livewire\WithPagination;

class ShowProducts extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $allowEmptyImages = '';
    protected $products = [];
    protected $categories = [];
    protected $productService;
    protected $categoryService;

    protected $queryString = ['search', 'category', 'allowEmptyImages'];
    protected $listeners = [ 'refresh' => '$refresh' ];

    public function boot(
        ProductService $productService,
        CategoryService $categoryService
    )
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function refreshComponent()
    {
        $this->dispatch('refresh');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->products = $this->productService->get([
            'search' => $this->search,
            'category' => $this->category,
            'allowEmptyImages' => $this->allowEmptyImages
        ]);

        $this->categories = $this->categoryService->get()->pluck('name')->unique();

        return view('livewire.show-products', [
            'products' => $this->products,
            'categories' => $this->categories
        ]);
    }
}
