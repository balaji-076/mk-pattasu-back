<?php


namespace App\Services;
 
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Services\Contracts\CloudinaryServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    public function __construct(
        protected readonly DashboardRepositoryInterface $dashboardRepository,
        protected readonly CloudinaryServiceInterface    $cloudinaryService,

    ) {}

public function fetchAllSliders(): Collection
    {
        return $this->dashboardRepository->getAllSliders();
    }
 
    public function fetchActiveSliders(): Collection
    {
        return $this->dashboardRepository->getActiveSliders();
    }
 
    public function addSlider(array $validated, ?UploadedFile $image) 
    {
        $uploadedImage = null;
 
        return DB::transaction(function () use ($validated, $image, &$uploadedImage) {
            try {
                if ($image) {
                    $uploadedImage = $this->cloudinaryService->uploadProductImage(
                        $image,
                        'hero_sliders'
                    );
 
                    if (!$uploadedImage) {
                        throw new \RuntimeException('Image upload failed. Please try again.');
                    }
 
                    $validated['image_url']      = $uploadedImage['url'];
                    $validated['image_public_id'] = $uploadedImage['public_id'];
                }
 
                $validated['is_active'] = filter_var(
                    $validated['is_active'] ?? true,
                    FILTER_VALIDATE_BOOLEAN
                );
                $validated['seq_order'] = (int) ($validated['seq_order'] ?? 0);
 
                return $this->dashboardRepository->createSlider($validated);
 
            } catch (\Throwable $e) {
                $this->rollbackCloudinaryUpload($uploadedImage);
 
                Log::error('HeroSliderService::addSlider failed', [
                    'error' => $e->getMessage(),
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                ]);
 
                throw $e;
            }
        });
    }
 
    public function modifySlider(int $id, array $validated, ?UploadedFile $image)
    {
        $slider      = $this->dashboardRepository->getSliderById($id);
        $newUpload   = null;
        $oldPublicId = $slider->image_public_id;
 
        $updatedSlider = DB::transaction(function () use ($slider, $validated, $image, &$newUpload) {
            try {
                if ($image) {
                    $newUpload = $this->cloudinaryService->uploadProductImage(
                        $image,
                        'hero_sliders'
                    );
 
                    if (!$newUpload) {
                        throw new \RuntimeException('Image upload failed. Please try again.');
                    }
 
                    $validated['image_url']      = $newUpload['url'];
                    $validated['image_public_id'] = $newUpload['public_id'];
                }
 
                if (array_key_exists('is_active', $validated)) {
                    $validated['is_active'] = filter_var(
                        $validated['is_active'],
                        FILTER_VALIDATE_BOOLEAN
                    );
                }
 
                if (array_key_exists('seq_order', $validated)) {
                    $validated['seq_order'] = (int) $validated['seq_order'];
                }
 
                return $this->dashboardRepository->updateSlider($slider, $validated);
 
            } catch (\Throwable $e) {
                $this->rollbackCloudinaryUpload($newUpload);
 
                Log::error('DashboardService::modifySlider failed', [
                    'slider_id' => $slider->id,
                    'error'     => $e->getMessage(),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                ]);
 
                throw $e;
            }
        });
 
        // Delete old Cloudinary image only after DB commit
        if ($newUpload && $oldPublicId) {
            $this->cloudinaryService->deleteProductImage($oldPublicId);
        }
 
        return $updatedSlider;
    }
 
    public function removeSlider(int $id): void
    {
        $slider = $this->dashboardRepository->getSliderById($id);
 
        DB::transaction(function () use ($slider) {
            $this->dashboardRepository->deleteSlider($slider);
        });
 
        if ($slider->image_public_id) {
            $this->cloudinaryService->deleteProductImage($slider->image_public_id);
        }
    }
 
    // ── Private ─────────────────────────────────────────────────────────────
 
    private function rollbackCloudinaryUpload(?array $uploadedImage): void
    {
        if ($uploadedImage && !empty($uploadedImage['public_id'])) {
            $this->cloudinaryService->deleteProductImage($uploadedImage['public_id']);
        }
    }

}
