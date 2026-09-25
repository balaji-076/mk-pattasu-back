<?php

namespace App\Http\Controllers;

use App\Models\ComboPackItems;
use Illuminate\Http\JsonResponse;

class ComboPackController extends Controller
{
    // GET /get/combo-offers/{id}/items
    public function show(int $id): JsonResponse
    {
        $items = ComboPackItems::where('combo_pack_id', $id)
            ->orderBy('sort_order')
            ->get(['id', 'combo_pack_id', 'name', 'sort_order']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Combo pack items fetched successfully',
            'data'    => $items,
        ]);
    }
}