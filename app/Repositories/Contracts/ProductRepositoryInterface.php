<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use App\Models\product\CrackerCategory;
use App\Models\product\PosBill;
use App\Models\product\ProductBadge;
use App\Models\product\ComboOffer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    // Products
    public function getAllProducts(array $filters = []): LengthAwarePaginator;
    public function getProductById(int $id): Product;
    public function getPriceRange(): array;
    public function createProduct(array $data): Product;
    public function updateProduct(Product $product, array $data): Product;
    public function deleteProduct(Product $product): bool;
    public function getProductsByCategory(string $category): Collection;
    public function searchProducts(string $term): Collection;

    // Categories
    public function retrieveAllCategories(): Collection;
    public function retrieveCategoryById(int $id): CrackerCategory;
    public function persistNewCategory(array $data): CrackerCategory;
    public function updateCategoryById(int $id, array $data): CrackerCategory;
    public function deleteCategoryById(int $id): bool;

    // POS Bills
    public function getAllBills(array $filters = []): LengthAwarePaginator;
    public function getBillById(int $id): PosBill;
    public function createBill(array $data): PosBill;
    public function updateBill(PosBill $bill, array $data): PosBill;
    public function deleteBill(PosBill $bill): bool;

    // Master Product Badges
    public function getAllActiveBadges(): Collection;
    public function getBadgeById(int $id): ProductBadge;
    public function createBadge(array $attributes): ProductBadge;
    public function updateBadge(ProductBadge $badge, array $attributes): ProductBadge;
    public function deleteBadge(ProductBadge $badge): bool;

    // Combo Offers
    public function getAllCombos(array $filters = []): LengthAwarePaginator;
    public function getComboById(int $id): ComboOffer;
    public function createCombo(array $data): ComboOffer;
    public function syncComboItems(ComboOffer $combo, array $items): void;
    public function updateCombo(ComboOffer $combo, array $data): ComboOffer;
    public function deleteCombo(ComboOffer $combo): bool;
}