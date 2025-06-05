<?php

namespace App\Http\Controllers;

use App\Contracts\LocationRepository;
use App\Http\Resources\LocationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationController extends Controller
{
    public function __construct(protected readonly LocationRepository $locationRepository) {}

    public function index(): ResourceCollection
    {
        return LocationResource::collection($this->locationRepository->getAll());
    }
}
