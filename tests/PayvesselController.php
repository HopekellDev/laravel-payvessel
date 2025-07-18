<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use HopekellDev\Payvessel\Facades\Payvessel;

class PayvesselController extends Controller
{
    public function createVirtualAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'phoneNumber' => 'required|string',
            'bankcode' => 'required|array',
            'account_type' => 'required|string|in:STATIC,DYNAMIC',
            'bvn' => 'nullable|string',
            'nin' => 'nullable|string',
        ]);

        try {
            $result = Payvessel::virtualAccounts()->createVirtualAccount($validated);
            return response()->json($result, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    public function getVirtualAccount($account): JsonResponse
    {
        try {
            $result = Payvessel::virtualAccounts()->getSingleVirtualAccount($account);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    public function updateAccountBVN(Request $request, $account): JsonResponse
    {
        $validated = $request->validate([
            'bvn' => 'required|string',
        ]);

        try {
            $result = Payvessel::virtualAccounts()->accountBVNUpdate($account, $validated['bvn']);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}
