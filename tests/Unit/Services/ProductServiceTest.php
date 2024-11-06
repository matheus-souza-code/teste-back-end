<?php

namespace Tests\Unit\Services;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Product\ProductRepository;
use App\Services\ProductService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $productService;
    protected $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->productService = new ProductService($this->productRepository);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_gets_products_with_pagination()
    {
        $paginator = new LengthAwarePaginator([], 10, 5);
        $this->productRepository->shouldReceive('get')->with([])->andReturn($paginator);

        $result = $this->productService->get([]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals($paginator, $result);
    }

    /** @test */
    public function it_creates_a_product()
    {
        Category::firstOrCreate(['name' => 'test']);

        $mockProduct = Product::factory()->make();

        $request = new StoreProductRequest([
            'name' => 'Test Product',
            'price' => 100,
            'description' => 'Test Description',
            'category' => 'test',
            'image_url' => 'http://example.com/image.jpg',
        ]);

        $this->productRepository->shouldReceive('create')->with($request->all())->andReturn($mockProduct);

        $response = $this->productService->create($request);

        $this->assertEquals(201, $response->status());
        
        $this->assertEquals('Product created successfully!', $response->getData()->message);

        $this->assertEquals(collect($mockProduct), collect($response->getData()->data));
    }

    /** @test */
    public function it_updates_an_existing_product()
    {
        Category::firstOrCreate(['name' => 'test']);

        $mockProduct = Product::create([
            'name' => 'Test Product',
            'price' => 100,
            'description' => 'Test Description',
            'category' => 'test',
            'image_url' => 'http://example.com/image.jpg',
        ]);

        $request = new UpdateProductRequest([
            'name' => 'Updated Product',
            'price' => 150,
        ]);

        $this->productRepository->shouldReceive('update')
            ->with($mockProduct, $request->all())
            ->andReturnUsing(function ($category, $data) {
                $category->name = $data['name'];
                return $category;
            });

        $response = $this->productService->update($mockProduct, $request);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Product updated successfully!', $response->getData()->message);
        $this->assertEquals($request->name, $response->getData()->data->name);
    }

    /** @test */
    public function it_throws_an_exception_when_updating_a_non_existent_product()
    {
        $product = new Product();
        $request = new UpdateProductRequest(['name' => 'Nonexistent Product']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product not found.');

        $this->productRepository->shouldReceive('update')->with($product, $request)->andReturn($product);
        $this->productService->update($product, $request);
    }

    /** @test */
    public function it_deletes_a_product()
    {
        Category::firstOrCreate(['name' => 'test']);

        $product = Product::create([
            'name' => 'Test Product',
            'price' => 100,
            'description' => 'Test Description',
            'category' => 'test',
            'image_url' => 'http://example.com/image.jpg',
        ]);

        $this->productRepository->shouldReceive('delete')->with($product)->andReturn(true);

        $response = $this->productService->delete($product);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Product deleted successfully!', $response->getData()->message);
        $this->assertTrue($response->getData()->success);
    }

    /** @test */
    public function it_throws_an_exception_when_deleting_a_non_existent_product()
    {
        $product = new Product();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product not found.');

        $this->productRepository->shouldReceive('delete')->with($product)->andReturn(true);
        $this->productService->delete($product);
    }
}
