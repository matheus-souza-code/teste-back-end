<?php

namespace App\Jobs;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreProductRequest;
use App\Services\CategoryService;
use App\Services\ProductService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productId;
    protected $productService;
    protected $categoryService;

    /**
     * Create a new job instance.
     */
    public function __construct(
        $productId = null,
    )
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     */
    public function handle(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    
        try {
            $response = Http::get("https://fakestoreapi.com/products/{$this->productId}");
    
            if ($response->failed()) {
                throw new Exception("Failed to fetch product data for ID {$this->productId}");
            }
            
            $productsData = $response->json();


            if(!$this->productId) {
                foreach ($productsData as $productData) {
                    $this->importCategory($productData['category']);
                    $this->importProduct($productData);
                }
            } else {
                $this->importCategory($productsData['category']);
                $this->importProduct($productsData);
            }

        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
    
    private function importCategory(string $categoryName)
    {
        try {
            $categoryRequest = new StoreCategoryRequest(['name' => $categoryName]);
            $categoryResponse = $this->categoryService->updateOrCreate($categoryRequest);
    
            if ($categoryResponse->status() !== 200) {
                throw new Exception('Failed to import category');
            }
        
        } catch (Exception $e) {
            Log::error("Category import failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function importProduct(array $productData)
    {
        try {
            $productRequest = new StoreProductRequest([
                'name' => $productData['title'],
                'price' => $productData['price'],
                'description' => $productData['description'],
                'category' => $productData['category'],
                'image_url' => $productData['image']
            ]);
    
            $productResponse = $this->productService->updateOrCreate($productRequest);
    
            if ($productResponse->status() !== 200) {
                throw new Exception('Failed to import product');
            }
    
        } catch (Exception $e) {
            Log::error("Product import failed: " . $e->getMessage());
            throw $e;
        }
    }    
}