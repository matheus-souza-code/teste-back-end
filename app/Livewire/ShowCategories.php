<?php

namespace App\Livewire;

use App\Services\CategoryService;
use Livewire\Component;
use Livewire\WithPagination;

class ShowCategories extends Component
{
    use WithPagination;

    public $search = '';
    protected $categories = [];
    protected $categoryService;

    protected $queryString = ['search'];
    protected $listeners = [ 'refresh' => '$refresh' ];

    public function boot(CategoryService $categoryService)
    {
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
        $this->categories = $this->categoryService->get([
            'search' => $this->search
        ]);

        return view('livewire.show-categories', [
            'categories' => $this->categories,
        ]);
    }
}
