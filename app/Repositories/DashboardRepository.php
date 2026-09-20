<?php

namespace App\Repositories;

use App\Models\HeroSlider;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardRepository implements DashboardRepositoryInterface
{
    protected const CACHE_TTL = 604800;

    public function __construct(
        protected readonly HeroSlider $model
    ) {}

    public function getAllSliders(): Collection
    {
        return Cache::remember('hero-sliders.all', self::CACHE_TTL, function () {
            return $this->model->ordered()->get();
        });
    }

    public function getActiveSliders(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    public function getSliderById(int $id): HeroSlider
    {
        return $this->model->findOrFail($id);
    }

    public function createSlider(array $data): HeroSlider
    {
        $slider = $this->model->create($data);

        Cache::forget('hero-sliders.all');

        return $slider;
    }

    public function updateSlider(HeroSlider $slider, array $data): HeroSlider
    {
        $slider->update($data);

        Cache::forget('hero-sliders.all');

        return $slider->refresh();
    }

    public function deleteSlider(HeroSlider $slider): bool
    {
        $deleted = (bool) $slider->delete();

        Cache::forget('hero-sliders.all');

        return $deleted;
    }
}