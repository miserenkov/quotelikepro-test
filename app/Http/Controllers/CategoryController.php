<?php

namespace App\Http\Controllers;

use App\Contracts\CategoryRepository;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryController extends Controller
{
    public function __construct(protected readonly CategoryRepository $categoryRepository) {}

    public function index(): ResourceCollection
    {
        return CategoryResource::collection($this->categoryRepository->getAll());
    }
}
