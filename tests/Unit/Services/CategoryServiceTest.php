<?php

namespace Tests\Unit\Services;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Repositories\Category\CategoryRepository;
use App\Services\CategoryService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $categoryService;
    protected $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = Mockery::mock(CategoryRepository::class);
        $this->categoryService = new CategoryService($this->categoryRepository);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_gets_categories_with_pagination()
    {
        $paginator = new LengthAwarePaginator([], 10, 5);
        $this->categoryRepository->shouldReceive('get')->with([])->andReturn($paginator);

        $result = $this->categoryService->get([]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals($paginator, $result);
    }

    /** @test */
    public function it_creates_a_category()
    {
        $mockCategory = Category::factory()->make();

        $request = new StoreCategoryRequest([
            'name' => 'Test Category',
        ]);

        $this->categoryRepository->shouldReceive('create')->with($request->all())->andReturn($mockCategory);

        $response = $this->categoryService->create($request);

        $this->assertEquals(201, $response->status());
        $this->assertEquals('Category created successfully!', $response->getData()->message);
        $this->assertEquals(collect($mockCategory), collect($response->getData()->data));
    }

    /** @test */
    public function it_updates_an_existing_category()
    {
        $mockCategory = Category::factory()->create([
            'name' => 'Test Category',
        ]);

        $request = new UpdateCategoryRequest([
            'name' => 'Updated Category',
        ]);
    
        $this->categoryRepository->shouldReceive('update')
            ->with($mockCategory, $request->all())
            ->andReturnUsing(function ($category, $data) {
                $category->name = $data['name'];
                return $category;
            });

        $response = $this->categoryService->update($mockCategory, $request);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Category updated successfully!', $response->getData()->message);
        $this->assertEquals($request->name, $response->getData()->data->name);
    }

    /** @test */
    public function it_throws_an_exception_when_updating_a_non_existent_category()
    {
        $category = new Category();
        $request = new UpdateCategoryRequest(['name' => 'Nonexistent Category']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Category not found.');

        $this->categoryRepository->shouldReceive('update')->with($category, $request->all())->andThrow(new Exception('Category not found.'));

        $this->categoryService->update($category, $request);
    }

    /** @test */
    public function it_deletes_a_category()
    {
        $category = Category::create(['name' => 'Test Category']);

        $this->categoryRepository->shouldReceive('delete')->with($category)->andReturn(true);

        $response = $this->categoryService->delete($category);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Category deleted successfully!', $response->getData()->message);
        $this->assertTrue($response->getData()->success);
    }

    /** @test */
    public function it_throws_an_exception_when_deleting_a_non_existent_category()
    {
        $category = new Category();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Category not found.');

        $this->categoryRepository->shouldReceive('delete')->with($category)->andThrow(new Exception('Category not found.'));

        $this->categoryService->delete($category);
    }
}
