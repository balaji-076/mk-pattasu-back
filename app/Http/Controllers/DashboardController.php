<?php

namespace App\Http\Controllers;

use App\Http\Resource\HeroSliderResource;
use App\Services\DashboardService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(
        protected readonly DashboardService $dashboardService
    ) {}
 
     public function listActiveSliders(): JsonResponse
    {
        $sliders = $this->dashboardService->fetchActiveSliders();
 
        return $this->successResponse(
            'Active sliders fetched successfully',
            HeroSliderResource::collection($sliders)
        );
    }
 
    // ── GET /admin/hero-sliders  (admin — all) ───────────────────────────────
    public function listAllSliders(): JsonResponse
    {
        $sliders = $this->dashboardService->fetchAllSliders();
 
        return $this->successResponse(
            'All sliders fetched successfully',
            HeroSliderResource::collection($sliders)
        );
    }
 
    // ── POST /admin/hero-sliders ─────────────────────────────────────────────
    public function createSlider(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image'      => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'cta_link'   => 'nullable|string|max:500',
            'is_active'  => 'nullable',
            'seq_order'  => 'nullable|integer|min:0',
            'valid_from' => 'nullable|date',
            'valid_to'   => 'nullable|date|after_or_equal:valid_from',
        ]);

        try {
            $slider = $this->dashboardService->addSlider(
                $validated,
                $request->file('image')
            );

            return $this->successResponse(
                'Slider created successfully',
                new HeroSliderResource($slider),
                201
            );
        }catch (\Throwable $e) {
            Log::error('DashboardController::createSlider failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return $this->errorResponse('Something went wrong. Please try again.', 500);
        }
    }
    // ── POST /admin/hero-sliders/{id}  (_method=PUT) ─────────────────────────
    public function updateSlider(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'cta_link'   => 'sometimes|nullable|string|max:500',
            'is_active'  => 'sometimes',
            'seq_order'  => 'sometimes|integer|min:0',
            'valid_from' => 'sometimes|nullable|date',
            'valid_to'   => 'sometimes|nullable|date|after_or_equal:valid_from',
        ]);
 
        try {
            $slider = $this->dashboardService->modifySlider(
                $id,
                $validated,
                $request->file('image')
            );
 
            return $this->successResponse(
                'Slider updated successfully',
                new HeroSliderResource($slider)
            );
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Slider not found', 404);
        }
    }
 
    // ── DELETE /admin/hero-sliders/{id} ──────────────────────────────────────
    public function deleteSlider(int $id): JsonResponse
    {
        try {
            $this->dashboardService->removeSlider($id);
 
            return $this->successResponse('Slider deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Slider not found', 404);
        }
    }

}
