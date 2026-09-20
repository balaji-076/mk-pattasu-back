<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\product\ComboOffer;
use App\Models\product\ProductBadge;
use App\Models\product\CrackerCategory;
use App\Models\product\PosBill;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{
    protected const CACHE_TTL = 604800; // 7 Days
    protected const BADGE_CACHE_KEY = 'mst_product_badges.active';

    public function __construct(
        protected readonly Product $model,
        protected readonly ProductBadge $badgeModel
    ) {}

    // -------------------------------------------------------------------------
    // Products
    // -------------------------------------------------------------------------

    public function getAllProducts(array $filters = []): LengthAwarePaginator
    {
        $page    = (int) ($filters['page'] ?? 1);
        $perPage = (int) ($filters['per_page'] ?? 16);
        $version = $this->productsCacheVersion();

        $cacheKey = "products.page{$page}.per{$perPage}"
            . ".cat_" . ($filters['category'] ?? 'all')
            . ".search_" . ($filters['search'] ?? '')
            . ".min_" . ($filters['min_price'] ?? '')
            . ".max_" . ($filters['max_price'] ?? '')
            . ".v{$version}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters, $page, $perPage) {
            $query = $this->model->with('badge')->orderBy('id', 'asc');

            if (!empty($filters['category']) && $filters['category'] !== 'all') {
                $query->where('category', $filters['category']);
            }

            if (!empty($filters['search'])) {
                $term = $filters['search'];
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'ILIKE', "%{$term}%")
                      ->orWhere('category', 'ILIKE', "%{$term}%")
                      ->orWhere('variety', 'ILIKE', "%{$term}%");
                });
            }

            if (isset($filters['min_price']) && $filters['min_price'] !== '') {
                $query->where('rate', '>=', (float) $filters['min_price']);
            }

            if (isset($filters['max_price']) && $filters['max_price'] !== '') {
                $query->where('rate', '<=', (float) $filters['max_price']);
            }

            return $query->paginate($perPage, ['*'], 'page', $page);
        });
    }

    public function getProductById(int $id): Product
    {
        return $this->model->with('badge')->findOrFail($id);
    }

    public function createProduct(array $data): Product
    {
        if (array_key_exists('badge_id', $data)) {
            $val = $data['badge_id'];
            $data['badge_id'] = (!empty($val) && $val !== 'null' && $val !== '0') ? (int) $val : null;
        }

        $product = $this->model->create($data);

        $this->bumpProductsCacheVersion();
        Cache::forget('products.price_range');
        Cache::forget('categories.all'); 

        return $product->load('badge');
    }

    public function updateProduct(Product $product, array $data): Product
    {
        if (array_key_exists('badge_id', $data)) {
            $val = $data['badge_id'];
            $data['badge_id'] = (!empty($val) && $val !== 'null' && $val !== '0') ? (int) $val : null;
        }

        $product->update($data);

        $this->bumpProductsCacheVersion();
        Cache::forget('products.price_range');
        Cache::forget('categories.all'); 

        return $product->fresh(['badge']);
    }

    public function deleteProduct(Product $product): bool
    {
        $deleted = (bool) $product->delete();

        $this->bumpProductsCacheVersion();
        Cache::forget('products.price_range');
        Cache::forget('categories.all');

        return $deleted;
    }

    public function getProductsByCategory(string $category): Collection
    {
        return $this->model->with('badge')
            ->where('category', $category)
            ->get();
    }

    public function searchProducts(string $term): Collection
    {
        return $this->model->with('badge')
            ->where('name', 'ILIKE', "%{$term}%")
            ->orWhere('category', 'ILIKE', "%{$term}%")
            ->orWhere('variety', 'ILIKE', "%{$term}%")
            ->get();
    }

    public function getPriceRange(): array
    {
        return Cache::remember('products.price_range', self::CACHE_TTL, function () {
            return [
                'min' => (float) ($this->model->min('rate') ?? 0),
                'max' => (float) ($this->model->max('rate') ?? 0),
            ];
        });
    }

    // -------------------------------------------------------------------------
    // Categories
    // -------------------------------------------------------------------------

    public function retrieveAllCategories(): Collection
    {
        return Cache::remember('categories.all', self::CACHE_TTL, function () {
            return CrackerCategory::ordered()
                ->withCount(['products as product_count'])
                ->get();
        });
    }

    public function retrieveCategoryById(int $id): CrackerCategory
    {
        return CrackerCategory::findOrFail($id);
    }

    public function persistNewCategory(array $data): CrackerCategory
    {
        $category = CrackerCategory::create($data);
        Cache::forget('categories.all');
        return $category;
    }

    public function updateCategoryById(int $id, array $data): CrackerCategory
    {
        $category = $this->retrieveCategoryById($id);

        if (isset($data['seq_order']) && $data['seq_order'] !== $category->seq_order) {
            $newOrder = (int) $data['seq_order'];
            $oldOrder = (int) $category->seq_order;

            if ($newOrder < $oldOrder) {
                CrackerCategory::where('id', '!=', $id)
                    ->whereBetween('seq_order', [$newOrder, $oldOrder - 1])
                    ->increment('seq_order');
            } else {
                CrackerCategory::where('id', '!=', $id)
                    ->whereBetween('seq_order', [$oldOrder + 1, $newOrder])
                    ->decrement('seq_order');
            }
        }

        $category->update($data);
        Cache::forget('categories.all');

        return $category->fresh();
    }

    public function deleteCategoryById(int $id): bool
    {
        $deleted = $this->retrieveCategoryById($id)->delete();
        Cache::forget('categories.all');
        return $deleted;
    }

    // -------------------------------------------------------------------------
    // POS Bills
    // -------------------------------------------------------------------------

    public function getAllBills(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        $query = PosBill::orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('bill_no', 'ILIKE', "%{$term}%")
                  ->orWhere('customer_name', 'ILIKE', "%{$term}%")
                  ->orWhere('customer_phone', 'ILIKE', "%{$term}%");
            });
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->paginate($perPage);
    }

    public function getBillById(int $id): PosBill
    {
        return PosBill::findOrFail($id);
    }

    public function createBill(array $data): PosBill
    {
        return PosBill::create($data);
    }

    public function updateBill(PosBill $bill, array $data): PosBill
    {
        $bill->update($data);
        return $bill->refresh();
    }

    public function deleteBill(PosBill $bill): bool
    {
        return $bill->delete();
    }

    // -------------------------------------------------------------------------
    // Master Product Badges
    // -------------------------------------------------------------------------

    public function getAllActiveBadges(): Collection
    {
        return Cache::remember(self::BADGE_CACHE_KEY, self::CACHE_TTL, function () {
            return $this->badgeModel->newQuery()
                ->active()
                ->get(['id', 'label', 'value', 'color', 'icon', 'animation_type', 'seq_order']);
        });
    }

    public function getBadgeById(int $id): ProductBadge
    {
        return $this->badgeModel->newQuery()->findOrFail($id);
    }

    public function createBadge(array $attributes): ProductBadge
    {
        $badge = $this->badgeModel->newQuery()->create($attributes);
        $this->flushBadgeCache();

        return $badge;
    }

    public function updateBadge(ProductBadge $badge, array $attributes): ProductBadge
    {
        $badge->update($attributes);
        $this->flushBadgeCache();

        return $badge->refresh();
    }

    public function deleteBadge(ProductBadge $badge): bool
    {
        $deleted = (bool) $badge->delete();
        $this->flushBadgeCache();

        return $deleted;
    }

    // -------------------------------------------------------------------------
    // Cache Helpers
    // -------------------------------------------------------------------------

    protected function flushBadgeCache(): void
    {
        Cache::forget(self::BADGE_CACHE_KEY);
        $this->bumpProductsCacheVersion();
    }

    private function productsCacheVersion(): int
    {
        Cache::add('products.cache_version', 1, now()->addYears(10));
        return (int) Cache::get('products.cache_version');
    }

    private function bumpProductsCacheVersion(): void
    {
        Cache::add('products.cache_version', 1, now()->addYears(10));
        Cache::increment('products.cache_version');
    }

    public function getAllCombos(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);

        $query = ComboOffer::with('products')->orderBy('sort_order', 'asc');

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where('name', 'ILIKE', "%{$term}%");
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate($perPage);
    }

    public function getComboById(int $id): ComboOffer
    {
        return ComboOffer::with('products')->findOrFail($id);
    }

    public function createCombo(array $data): ComboOffer
    {
        return ComboOffer::create($data);
    }

    public function syncComboItems(ComboOffer $combo, array $items): void
    {
        // items = [['product_id' => 5, 'quantity' => 2], ...]
        $syncData = [];
        foreach ($items as $item) {
            $syncData[$item['product_id']] = ['quantity' => $item['quantity'] ?? 1];
        }

        $combo->products()->sync($syncData);
    }

    public function updateCombo(ComboOffer $combo, array $data): ComboOffer
    {
        $combo->update($data);

        return $combo->fresh(['products']);
    }

    public function deleteCombo(ComboOffer $combo): bool
    {
        return (bool) $combo->delete();
    }

}