<?php

namespace App\Http\Controllers;

use App\Http\Resource\ComboOfferResource;
use App\Http\Resource\ProductResource;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        protected readonly ProductService $productService
    ) {}

    // =========================================================================
    // GET /products
    // =========================================================================
    public function getAllProducts(Request $request)
    {
        $filters = $request->only(['page', 'per_page', 'category', 'search', 'min_price', 'max_price']);

        $products = $this->productService->getAllProducts($filters);

        return response()->json($products);
    }

    public function getPriceRange(): JsonResponse
    {
        $range = $this->productService->getPriceRange();

        return $this->successResponse('Price range fetched successfully', $range);  
    }

    // =========================================================================
    // GET /products/{id}
    // =========================================================================
    public function getProductById($id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            return $this->successResponse(
                'Product fetched successfully',
                new ProductResource($product)
            );
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Product not found', 404);
        }
    }

    // =========================================================================
    // POST /products
    // =========================================================================
    public function createProduct(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'             => 'required|max:255',
            'category'         => 'required|max:100',
            'rate'             => 'required|min:0',
            'variety'          => 'required|max:255',
            'image'            => 'nullable|mimes:jpg,jpeg,png,webp|max:5120',
            'applied_discount' => 'nullable|min:0|max:100',
            'meta_description' => 'nullable|max:160',
            'badge_id'         => 'nullable', // 👈 'badge_label' பதிலாக 'badge_id'
            'is_active'        => 'nullable',
        ]);

        $product = $this->productService->createProduct(
            $validated,
            $request->file('image')
        );

        return $this->successResponse(
            'Product created successfully',
            new ProductResource($product),
            201
        );
    }

    // =========================================================================
    // PUT /products/{id}
    // =========================================================================
    public function updateProduct(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name'             => 'required|max:255',
            'category'         => 'required|max:100',
            'rate'             => 'required|min:0',
            'variety'          => 'required|max:255',
            'image'            => 'nullable|mimes:jpg,jpeg,png,webp|max:5120',
            'applied_discount' => 'nullable|min:0|max:100',
            'meta_description' => 'nullable|max:160',
            'badge_id'         => 'nullable', // 👈 'badge_label' பதிலாக 'badge_id'
            'is_active'        => 'nullable',
        ]);

        try {
            $product = $this->productService->updateProduct(
                $id,
                $validated,
                $request->file('image')
            );

            return $this->successResponse(
                'Product updated successfully',
                new ProductResource($product)
            );
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Product not found', 404);
        }
    }

    // =========================================================================
    // DELETE /products/{id}
    // =========================================================================
    public function deleteProduct(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);

            return $this->successResponse('Product deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Product not found', 404);
        }
    }

    // =========================================================================
    // GET /products/category/{category}
    // =========================================================================
    public function getProductsByCategory(string $category): JsonResponse
    {
        $products = $this->productService->getProductsByCategory($category);

        if ($products->isEmpty()) {
            return $this->errorResponse(
                "No products found for category: {$category}",
                404
            );
        }

        return $this->successResponse(
            "Products fetched successfully for category: {$category}",
            ProductResource::collection($products)
        );
    }

    // =========================================================================
    // GET /products/search/{term}
    // =========================================================================
    public function searchProducts(string $term): JsonResponse
    {
        $products = $this->productService->searchProducts($term);

        if ($products->isEmpty()) {
            return $this->errorResponse(
                "No products found matching: {$term}",
                404
            );
        }

        return $this->successResponse(
            "Search results for: {$term}",
            ProductResource::collection($products)
        );
    }

    public function getMstProductBadges(): JsonResponse
    {
        $badges = $this->productService->getMstProductBadges();

        return $this->successResponse('Badges fetched successfully', $badges);
    }

    public function getCategoryById(int $id): JsonResponse
    {
        $category = $this->productService->fetchCategoryById($id);
        return $this->successResponse(
            'Category fetched successfully',
            $category
        );
    }

    public function listAllCategories(): JsonResponse
    {
        $categories = $this->productService->fetchAllCategories();
        return $this->successResponse(
            'All categories fetched successfully',
            $categories
        );
    }

    public function createCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label'         => 'required|string|max:255',
            'value'         => 'required|string|max:100|unique:cracker_categories,value',
            'seq_order'     => 'nullable|integer|min:0',
            'active_status' => 'nullable|boolean',
            'image'         => 'nullable|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $category = $this->productService->registerNewCategory(
            $validated,
            $request->file('image')
        );

        return $this->successResponse(
            'Category created successfully',
            $category,
            201
        );
    }

    public function updateCategory(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'label'         => 'sometimes|string|max:255',
            'value'         => "sometimes|string|max:100|unique:cracker_categories,value,{$id}",
            'seq_order'     => 'nullable|integer|min:0',
            'active_status' => 'nullable|boolean',
            'image'         => 'nullable|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $category = $this->productService->modifyCategoryDetails(
            $id,
            $validated,
            $request->file('image')
        );

        return $this->successResponse(
            'Category updated successfully',
            $category
        );
    }

    public function deleteCategory(int $id): JsonResponse
    {
        $this->productService->removeCategoryById($id);
        return $this->successResponse('Category deleted successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer.phone' => 'required|string|min:10|max:15',
            'customer.name'  => 'nullable|string|max:150',
            'customer.city'  => 'nullable|string|max:100',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'nullable|integer',
            'items.*.name'   => 'required|string',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.unitPrice' => 'required|numeric|min:0',
            'items.*.total'  => 'required|numeric|min:0',
            'subtotal'       => 'required|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0',
            'net_total'      => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
        ]);

        $bill = $this->productService->createBill($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Bill saved & recorded successfully',
            'data'    => $bill
        ], 201);
    }

    public function getAllBills(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'from_date', 'to_date', 'per_page']);

        $bills = $this->productService->getAllBills($filters);

        return $this->successResponse('Bills fetched successfully', $bills);
    }

    public function getBillById(int $id): JsonResponse
    {
        try {
            $bill = $this->productService->getBillById($id);

            return $this->successResponse('Bill fetched successfully', $bill);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Bill not found', 404);
        }
    }

    public function createBill(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer.phone'     => 'required|string|min:10|max:15',
            'customer.name'      => 'nullable|string|max:150',
            'customer.city'      => 'nullable|string|max:100',
            'items'               => 'required|array|min:1',
            'items.*.id'          => 'nullable|integer',
            'items.*.name'        => 'required|string',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.unitPrice'   => 'required|numeric|min:0',
            'items.*.total'       => 'required|numeric|min:0',
            'subtotal'            => 'required|numeric|min:0',
            'discount'            => 'nullable|numeric|min:0',
            'net_total'           => 'required|numeric|min:0',
            'payment_method'      => 'nullable|string',
        ]);

        $bill = $this->productService->createBill($validated);

        return $this->successResponse('Bill saved & recorded successfully', $bill, 201);
    }

    public function updateBill(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'customer.phone'     => 'sometimes|string|min:10|max:15',
            'customer.name'      => 'sometimes|nullable|string|max:150',
            'customer.city'      => 'sometimes|nullable|string|max:100',
            'items'               => 'sometimes|array|min:1',
            'items.*.id'          => 'nullable|integer',
            'items.*.name'        => 'required_with:items|string',
            'items.*.qty'         => 'required_with:items|integer|min:1',
            'items.*.unitPrice'   => 'required_with:items|numeric|min:0',
            'items.*.total'       => 'required_with:items|numeric|min:0',
            'subtotal'            => 'sometimes|numeric|min:0',
            'discount'            => 'sometimes|nullable|numeric|min:0',
            'net_total'           => 'sometimes|numeric|min:0',
            'payment_method'      => 'sometimes|nullable|string',
        ]);

        try {
            $bill = $this->productService->updateBill($id, $validated);

            return $this->successResponse('Bill updated successfully', $bill);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Bill not found', 404);
        }
    }

    public function deleteBill(int $id): JsonResponse
    {
        try {
            $this->productService->deleteBill($id);

            return $this->successResponse('Bill deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Bill not found', 404);
        }
    }

    /**
     * Retrieve all active product badges.
     */
    public function retrieveProductBadges(): JsonResponse
    {
        try {
            $badges = $this->productService->fetchActiveBadges();
            return $this->successResponse('Product badges retrieved successfully', $badges);
        } catch (Throwable $e) {
            return $this->errorResponse(
                'Failed to retrieve product badges: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Register a new master product badge.
     */
    public function registerProductBadge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label'          => 'required|string|max:60|unique:mst_product_badges,label',
            'color'          => 'required|string|max:30',
            'icon'           => 'required|string|max:50',
            'animation_type' => 'required|string|max:50',
            'seq_order'      => 'nullable|integer|min:0',
        ]);

        try {
            $badge = $this->productService->registerNewBadge($validated);
            return $this->successResponse('Product badge registered successfully', $badge, 201);
        } catch (Throwable $e) {
            return $this->errorResponse(
                'Failed to register product badge: ' . $e->getMessage(),
                400
            );
        }
    }

    /**
     * Modify details of an existing product badge.
     */
    public function modifyProductBadge(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'label'          => 'required|string|max:60|unique:mst_product_badges,label,' . $id,
            'color'          => 'required|string|max:30',
            'icon'           => 'required|string|max:50',
            'animation_type' => 'required|string|max:50',
            'seq_order'      => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        try {
            $badge = $this->productService->modifyBadgeDetails($id, $validated);
            return $this->successResponse('Product badge modified successfully', $badge);
        } catch (Throwable $e) {
            return $this->errorResponse(
                'Failed to modify product badge: ' . $e->getMessage(),
                400
            );
        }
    }

    /**
     * Remove a master product badge from the system.
     */
    public function removeProductBadge(int $id): JsonResponse
    {
        try {
            $this->productService->removeBadge($id);
            return $this->successResponse('Product badge removed successfully');
        } catch (Throwable $e) {
            return $this->errorResponse(
                'Failed to remove product badge: ' . $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }
    public function getAllCombos(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'is_active', 'per_page']);
        $combos = $this->productService->getAllCombos($filters);

        return response()->json($combos);
    }

    public function getComboById(int $id): JsonResponse
    {
        try {
            $combo = $this->productService->getComboById($id);

            return $this->successResponse(
                'Combo offer fetched successfully',
                new ComboOfferResource($combo)
            );
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Combo offer not found', 404);
        }
    }

    public function createCombo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'description'           => 'nullable|string',
            'image'                 => 'nullable|mimes:jpg,jpeg,png,webp|max:5120',
            'combo_price'           => 'required|numeric|min:0',
            'starts_at'             => 'nullable|date',
            'ends_at'               => 'nullable|date|after_or_equal:starts_at',
            'is_active'             => 'nullable',
            'sort_order'            => 'nullable|integer|min:0',
            'items'                 => 'required|array|min:2',   // combo needs at least 2 products
            'items.*.product_id'    => 'required|integer|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:5120', 
        ]);

        $combo = $this->productService->createCombo($validated, $request->file('image'));

        return $this->successResponse(
            'Combo offer created successfully',
            new ComboOfferResource($combo),
            201
        );
    }

    public function updateCombo(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => 'sometimes|string|max:255',
            'description'           => 'nullable|string',
            'image'                 => 'nullable|mimes:jpg,jpeg,png,webp|max:5120',
            'combo_price'           => 'sometimes|numeric|min:0',
            'starts_at'             => 'nullable|date',
            'ends_at'               => 'nullable|date|after_or_equal:starts_at',
            'is_active'             => 'nullable',
            'sort_order'            => 'nullable|integer|min:0',
            'items'                 => 'sometimes|array|min:2',
            'items.*.product_id'    => 'required_with:items|integer|exists:products,id',
            'items.*.quantity'      => 'required_with:items|integer|min:1',
        ]);

        try {
            $combo = $this->productService->updateCombo($id, $validated, $request->file('image'));

            return $this->successResponse(
                'Combo offer updated successfully',
                new ComboOfferResource($combo)
            );
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Combo offer not found', 404);
        }
    }

    public function deleteCombo(int $id): JsonResponse
    {
        try {
            $this->productService->deleteCombo($id);

            return $this->successResponse('Combo offer deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Combo offer not found', 404);
        }
    }

}