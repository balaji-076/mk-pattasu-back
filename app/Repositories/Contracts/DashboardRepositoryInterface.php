<?php
 
namespace App\Repositories\Contracts;

use App\Models\HeroSlider;
use Illuminate\Database\Eloquent\Collection;

interface DashboardRepositoryInterface
{
    public function getAllSliders(): Collection;
    public function getActiveSliders(): Collection;
    public function getSliderById(int $id): HeroSlider;
    public function createSlider(array $data): HeroSlider;
    public function updateSlider(HeroSlider $slider, array $data): HeroSlider;
    public function deleteSlider(HeroSlider $slider): bool;
}
 