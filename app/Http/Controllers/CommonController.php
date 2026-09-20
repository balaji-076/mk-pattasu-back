<?php

namespace App\Http\Controllers;

use App\Services\CommonService;
use Illuminate\Http\JsonResponse;

class CommonController extends Controller
{
    public function __construct(protected CommonService $commonService)
    {
    }

    public function lookup(string $pincode): JsonResponse
    {
        $pincode = trim($pincode);
        if (!preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
            return $this->errorResponse('Invalid pincode format. Must be a 6-digit number.', 422);
        }
        $details = $this->commonService->lookupPincode($pincode);
        if (!$details) {
            return $this->errorResponse('Pincode details not found', 404);
        }

        return $this->successResponse('Pincode details fetched successfully', $details);
    }
}