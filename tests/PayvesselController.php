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

    /**
     * Handle Payvessel Webhook
     */
    public function webhook(Request $request): JsonResponse
    {
        if (!$request->isMethod('post')) {
            return response()->json(['message' => 'Method not allowed'], 405);
        }

        $payload = $request->getContent();
        $signature = $request->header('Payvessel-Http-Signature');
        $ipAddress = $request->ip();
        $allowedIps = ["3.255.23.38", "162.246.254.36"];
        $secret = env("PAYVESSEL_API_SECRET");

        $expectedHash = hash_hmac('sha512', $payload, $secret);

        if ($signature !== $expectedHash || !in_array($ipAddress, $allowedIps)) {
            return response()->json(['message' => 'Permission denied, invalid hash or IP address.'], 400);
        }

        $data = json_decode($payload, true);

        if (
            !$data ||
            !isset($data['transaction']['reference'], $data['order']['amount'], $data['virtualAccount']['virtualAccountNumber'])
        ) {
            return response()->json(['message' => 'Invalid payload structure'], 422);
        }

        $reference = $data['transaction']['reference'];
        $amount = floatval($data['order']['amount']);
        $virtualAccount = $data['virtualAccount']['virtualAccountNumber'];

        $virtualBankAccount = StaticBankAccount::where('account_number', $virtualAccount)->first();

        if (!$virtualBankAccount || !$virtualBankAccount->user_id) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user = User::find($virtualBankAccount->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (Transaction::where('reference', $reference)->exists()) {
            return response()->json(['message' => 'Transaction already exists'], 200);
        }

        $transactionData = [
            'amount' => $amount,
            'payment' => 'Wallet Funding',
            'reference' => $reference,
            'gateway_id' => 5,
        ];

        Transaction::create([
            'user_id'    => $user->id,
            'reference'  => $reference,
            'amount'     => $amount,
            'status'     => 'successful',
            'gateway_id' => 5,
        ]);

        $user->increment('balance', $amount);

        return response()->json(['message' => 'success'], 200);
    }
}
