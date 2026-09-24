<?php

namespace App\Services;

use App\Models\product\PosBill;
use App\Models\Product;
use App\Models\product\ComboOffer;
use App\Models\product\CrackerCategory;
use App\Models\product\ProductBadge;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\CloudinaryServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        protected readonly ProductRepositoryInterface $productRepository,
        protected readonly CloudinaryServiceInterface $cloudinaryService,
    ) {}

    // -------------------------------------------------------------------------
    // Product Queries
    // -------------------------------------------------------------------------

    public function getAllProducts(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->getAllProducts($filters);
    }

    public function getPriceRange(): array
    {
        return $this->productRepository->getPriceRange();
    }

    public function getProductById(int $id): Product
    {
        return $this->productRepository->getProductById($id);
    }

    public function getProductsByCategory(string $category): Collection
    {
        return $this->productRepository->getProductsByCategory($category);
    }

    public function searchProducts(string $term): Collection
    {
        return $this->productRepository->searchProducts($term);
    }

    // -------------------------------------------------------------------------
    // Product Commands
    // -------------------------------------------------------------------------

    public function createProduct(array $validated, ?UploadedFile $image): Product
    {
        $uploadedImage = null;

        return DB::transaction(function () use ($validated, $image, &$uploadedImage) {
            try {
                if ($image) {
                    $uploadedImage = $this->cloudinaryService->uploadProductImage(
                        $image,
                        $validated['category']
                    );

                    if (!$uploadedImage) {
                        throw new \RuntimeException('Image upload failed. Please try again.');
                    }

                    $validated['image_url']      = $uploadedImage['url'];
                    $validated['image_public_id'] = $uploadedImage['public_id'];
                }

                 // is_active default to true
                $validated['is_active'] = filter_var(
                    $validated['is_active'] ?? true,
                    FILTER_VALIDATE_BOOLEAN
                );

                $rate            = (float) ($validated['rate'] ?? 0);
                $appliedDiscount = (float) ($validated['applied_discount'] ?? 0);
                $validated['rate']             = $rate;
                $validated['applied_discount'] = $appliedDiscount;
                $validated['discount_rate']    = $appliedDiscount > 0
                    ? round($rate - ($rate * $appliedDiscount / 100), 2)
                    : null;

                // Sanitize badge_id
                $validated['badge_id'] = $this->sanitizeBadgeId($validated['badge_id'] ?? null);

                return $this->productRepository->createProduct($validated);
            } catch (\Throwable $e) {
                $this->rollbackCloudinaryUpload($uploadedImage);

                Log::error('ProductService::createProduct failed', [
                    'error' => $e->getMessage(),
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                ]);

                throw $e;
            }
        });
    }

    public function updateProduct(int $id, array $validated, ?UploadedFile $image): Product
{
    $product     = $this->productRepository->getProductById($id);
    $newUpload   = null;
    $oldPublicId = $product->image_public_id;

    $updatedProduct = DB::transaction(function () use ($product, $validated, $image, &$newUpload) {
        try {
            if ($image) {
                $newUpload = $this->cloudinaryService->uploadProductImage(
                    $image,
                    $validated['category'] ?? $product->category
                );

                if (!$newUpload) {
                    throw new \RuntimeException('Image upload failed. Please try again.');
                }

                $validated['image_url']       = $newUpload['url'];
                $validated['image_public_id'] = $newUpload['public_id'];
            }

            if (array_key_exists('is_active', $validated)) {
                $validated['is_active'] = filter_var(
                    $validated['is_active'],
                    FILTER_VALIDATE_BOOLEAN
                );
            }

            $rate            = (float) ($validated['rate'] ?? $product->rate);
            $appliedDiscount = (float) ($validated['applied_discount'] ?? $product->applied_discount);
            $validated['rate']             = $rate;
            $validated['applied_discount'] = $appliedDiscount;
            $validated['discount_rate']    = $appliedDiscount > 0
                ? round($rate - ($rate * $appliedDiscount / 100), 2)
                : null;

            // 👇 badge_id Integer Casting Fix
            if (array_key_exists('badge_id', $validated)) {
                $rawBadge = $validated['badge_id'];
                $validated['badge_id'] = (!empty($rawBadge) && $rawBadge !== 'null' && $rawBadge !== '0')
                    ? (int) $rawBadge
                    : null;
            }

            return $this->productRepository->updateProduct($product, $validated);
        } catch (\Throwable $e) {
            $this->rollbackCloudinaryUpload($newUpload);

            Log::error('ProductService::updateProduct failed', [
                'product_id' => $product->id,
                'error'      => $e->getMessage(),
            ]);

            throw $e;
        }
    });

    if ($newUpload && $oldPublicId) {
        $this->cloudinaryService->deleteProductImage($oldPublicId);
    }

    return $updatedProduct;
}

    public function deleteProduct(int $id): void
    {
        $product  = $this->productRepository->getProductById($id);
        $publicId = $product->image_public_id;

        DB::transaction(fn () => $this->productRepository->deleteProduct($product));

        if ($publicId) {
            $this->cloudinaryService->deleteProductImage($publicId);
        }
    }

    private function sanitizeBadgeId(mixed $value): ?int
    {
        if (is_null($value)) return null;
        $val = trim((string) $value);
        return ($val !== '' && $val !== 'null' && $val !== '0') ? (int) $val : null;
    }

    private function rollbackCloudinaryUpload(?array $uploadedImage): void
    {
        if (!empty($uploadedImage['public_id'])) {
            $this->cloudinaryService->deleteProductImage($uploadedImage['public_id']);
        }
    }

    // -------------------------------------------------------------------------
    // Master Product Badges
    // -------------------------------------------------------------------------

    public function fetchActiveBadges(): Collection
    {
        return $this->productRepository->getAllActiveBadges();
    }

    public function getMstProductBadges(): Collection
    {
        return $this->fetchActiveBadges();
    }

    public function fetchBadgeById(int $id): ProductBadge
    {
        return $this->productRepository->getBadgeById($id);
    }

    public function registerNewBadge(array $data): ProductBadge
    {
        $data['value']     = Str::slug($data['label'], '_');
        $data['seq_order'] = $data['seq_order'] ?? ((ProductBadge::max('seq_order') ?? 0) + 1);
        $data['is_active'] = true;

        return DB::transaction(fn () => $this->productRepository->createBadge($data));
    }

    public function modifyBadgeDetails(int $id, array $data): ProductBadge
    {
        $badge = $this->productRepository->getBadgeById($id);

        if (isset($data['label'])) {
            $data['value'] = Str::slug($data['label'], '_');
        }

        return DB::transaction(fn () => $this->productRepository->updateBadge($badge, $data));
    }

    public function removeBadge(int $id): bool
    {
        $badge = $this->productRepository->getBadgeById($id);

        return DB::transaction(fn () => $this->productRepository->deleteBadge($badge));
    }

    // -------------------------------------------------------------------------
    // Categories
    // -------------------------------------------------------------------------

    public function fetchAllCategories(): Collection
    {
        return $this->productRepository->retrieveAllCategories();
    }

    public function fetchCategoryById(int $id): CrackerCategory
    {
        return $this->productRepository->retrieveCategoryById($id);
    }

    public function registerNewCategory(array $data, ?UploadedFile $image = null): CrackerCategory
    {
        if (empty($data['seq_order'])) {
            $data['seq_order'] = (CrackerCategory::max('seq_order') ?? 0) + 10;
        }
        $uploadedImage = null;

        return DB::transaction(function () use ($data, $image, &$uploadedImage) {
            try {
                if ($image) {
                    $uploadedImage = $this->cloudinaryService->uploadProductImage(
                        $image,
                        'categories/' . ($data['value'] ?? 'general')
                    );

                    if (!$uploadedImage) {
                        throw new \RuntimeException('Image upload failed. Please try again.');
                    }

                    $data['image_url']       = $uploadedImage['url'];
                    $data['image_public_id'] = $uploadedImage['public_id'];
                }

                return $this->productRepository->persistNewCategory($data);
            } catch (\Throwable $e) {
                $this->rollbackCloudinaryUpload($uploadedImage);

                Log::error('ProductService::registerNewCategory failed', [
                    'error' => $e->getMessage(),
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                ]);

                throw $e;
            }
        });
    }

    public function modifyCategoryDetails(int $id, array $data, ?UploadedFile $image = null): CrackerCategory
    {
        $category    = $this->productRepository->retrieveCategoryById($id);
        $newUpload   = null;
        $oldPublicId = $category->image_public_id;

        $updatedCategory = DB::transaction(function () use ($category, $data, $image, &$newUpload) {
            try {
                if ($image) {
                    $newUpload = $this->cloudinaryService->uploadProductImage(
                        $image,
                        'categories/' . ($data['value'] ?? $category->value)
                    );

                    if (!$newUpload) {
                        throw new \RuntimeException('Image upload failed. Please try again.');
                    }

                    $data['image_url']       = $newUpload['url'];
                    $data['image_public_id'] = $newUpload['public_id'];
                }

                return $this->productRepository->updateCategoryById($category->id, $data);
            } catch (\Throwable $e) {
                $this->rollbackCloudinaryUpload($newUpload);
                Log::error('ProductService::modifyCategoryDetails failed', [
                    'category_id' => $category->id,
                    'error'       => $e->getMessage(),
                    'file'        => $e->getFile(),
                    'line'        => $e->getLine(),
                ]);

                throw $e;
            }
        });
        if ($newUpload && $oldPublicId) {
            $this->cloudinaryService->deleteProductImage($oldPublicId);
        }

        return $updatedCategory;
    }

    public function removeCategoryById(int $id): bool
    {
        $category = $this->productRepository->retrieveCategoryById($id);
        $publicId = $category->image_public_id;
        $deleted = DB::transaction(fn () => $this->productRepository->deleteCategoryById($id));

        if ($publicId) {
            $this->cloudinaryService->deleteProductImage($publicId);
        }

        return $deleted;
    }

    // -------------------------------------------------------------------------
    // POS Bills
    // -------------------------------------------------------------------------

    public function createBill(array $data): PosBill
    {
        $billNo = 'MK-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        return PosBill::create([
            'bill_no'         => $billNo,
            'customer_name'   => $data['customer']['name'] ?? 'Counter Customer',
            'customer_phone'  => $data['customer']['phone'],
            'customer_city'   => $data['customer']['city'] ?? 'Sivakasi',
            'items'           => $data['items'],
            'subtotal'        => $data['subtotal'],
            'discount'        => $data['discount'] ?? 0,
            'net_total'       => $data['net_total'],
            'payment_method'  => $data['payment_method'] ?? 'Cash',
        ]);
    }

    public function getAllBills(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->getAllBills($filters);
    }

    public function getBillById(int $id): PosBill
    {
        return $this->productRepository->getBillById($id);
    }

    public function updateBill(int $id, array $data): PosBill
    {
        $bill = $this->productRepository->getBillById($id);

        $updateData = [];

        if (isset($data['customer'])) {
            $updateData['customer_name']  = $data['customer']['name'] ?? $bill->customer_name;
            $updateData['customer_phone'] = $data['customer']['phone'] ?? $bill->customer_phone;
            $updateData['customer_city']  = $data['customer']['city'] ?? $bill->customer_city;
        }

        if (isset($data['items'])) {
            $updateData['items'] = $data['items'];
        }

        if (isset($data['subtotal'])) {
            $updateData['subtotal'] = $data['subtotal'];
        }

        if (isset($data['discount'])) {
            $updateData['discount'] = $data['discount'];
        }

        if (isset($data['net_total'])) {
            $updateData['net_total'] = $data['net_total'];
        }

        if (isset($data['payment_method'])) {
            $updateData['payment_method'] = $data['payment_method'];
        }

        return $this->productRepository->updateBill($bill, $updateData);
    }

    public function deleteBill(int $id): bool
    {
        $bill = $this->productRepository->getBillById($id);

        return $this->productRepository->deleteBill($bill);
    }

    public function getAllCombos(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->getAllCombos($filters);
    }

    public function getComboById(int $id): ComboOffer
    {
        return $this->productRepository->getComboById($id);
    }

    public function createCombo(array $data, ?UploadedFile $image = null): ComboOffer
    {
        $uploadedImage = null;

        return DB::transaction(function () use ($data, $image, &$uploadedImage) {
            try {
                if ($image) {
                    $uploadedImage = $this->cloudinaryService->uploadProductImage(
                        $image,
                        'combos'
                    );

                    if (!$uploadedImage) {
                        throw new \RuntimeException('Image upload failed. Please try again.');
                    }

                    $data['image_url']       = $uploadedImage['url'];
                    $data['image_public_id'] = $uploadedImage['public_id'];
                }

                $items = $data['items'];
                unset($data['items']);

                $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

                // Calculate original_total from selected products
                $originalTotal = 0;
                foreach ($items as $item) {
                    $product = $this->productRepository->getProductById($item['product_id']);
                    $originalTotal += (float) $product->rate * (int) ($item['quantity'] ?? 1);
                }

                $data['original_total'] = $originalTotal;
                $data['discount_percent'] = $originalTotal > 0
                    ? round((($originalTotal - $data['combo_price']) / $originalTotal) * 100, 2)
                    : 0;

                $combo = $this->productRepository->createCombo($data);
                $this->productRepository->syncComboItems($combo, $items);

                return $this->productRepository->getComboById($combo->id);

            } catch (\Throwable $e) {
                $this->rollbackCloudinaryUpload($uploadedImage);

                Log::error('ProductService::createCombo failed', [
                    'error' => $e->getMessage(),
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                ]);

                throw $e;
            }
        });
    }

    public function updateCombo(int $id, array $data, ?UploadedFile $image = null): ComboOffer
    {
        $combo       = $this->productRepository->getComboById($id);
        $newUpload   = null;
        $oldPublicId = $combo->image_public_id;

        $updatedCombo = DB::transaction(function () use ($combo, $data, $image, &$newUpload) {
            try {
                if ($image) {
                    $newUpload = $this->cloudinaryService->uploadProductImage($image, 'combos');

                    if (!$newUpload) {
                        throw new \RuntimeException('Image upload failed. Please try again.');
                    }

                    $data['image_url']       = $newUpload['url'];
                    $data['image_public_id'] = $newUpload['public_id'];
                }

                if (array_key_exists('is_active', $data)) {
                    $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
                }

                $items = $data['items'] ?? null;
                unset($data['items']);

                if ($items !== null) {
                    $originalTotal = 0;
                    foreach ($items as $item) {
                        $product = $this->productRepository->getProductById($item['product_id']);
                        $originalTotal += (float) $product->rate * (int) ($item['quantity'] ?? 1);
                    }

                    $comboPrice = $data['combo_price'] ?? $combo->combo_price;
                    $data['original_total']   = $originalTotal;
                    $data['discount_percent'] = $originalTotal > 0
                        ? round((($originalTotal - $comboPrice) / $originalTotal) * 100, 2)
                        : 0;
                }

                $this->productRepository->updateCombo($combo, $data);

                if ($items !== null) {
                    $this->productRepository->syncComboItems($combo, $items);
                }

                return $this->productRepository->getComboById($combo->id);

            } catch (\Throwable $e) {
                $this->rollbackCloudinaryUpload($newUpload);

                Log::error('ProductService::updateCombo failed', [
                    'combo_id' => $combo->id,
                    'error'    => $e->getMessage(),
                    'file'     => $e->getFile(),
                    'line'     => $e->getLine(),
                ]);

                throw $e;
            }
        });

        if ($newUpload && $oldPublicId) {
            $this->cloudinaryService->deleteProductImage($oldPublicId);
        }

        return $updatedCombo;
    }

    public function deleteCombo(int $id): void
    {
        $combo    = $this->productRepository->getComboById($id);
        $publicId = $combo->image_public_id;

        DB::transaction(fn () => $this->productRepository->deleteCombo($combo));

        if ($publicId) {
            $this->cloudinaryService->deleteProductImage($publicId);
        }
    }
}