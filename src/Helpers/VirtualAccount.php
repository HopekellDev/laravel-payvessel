<?php

namespace HopekellDev\Payvessel\Helpers;

use Illuminate\Support\Facades\Http;

/**
 * Payvessel Virtual Account Handler
 * 
 * Handles virtual account operations with Payvessel API
 * 
 * @author Hope Ezenwa
 * @version 1.0.0
 */
class VirtualAccount
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $baseUrl;
    protected string $businessId;

    public function __construct(string $apiKey, string $apiSecret, string $baseUrl, string $businessId)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->businessId = $businessId;
    }

    /**
     * Returns the HTTP client with default headers
     */
    protected function httpClient()
    {
        return Http::withHeaders([
            'api-key' => $this->apiKey,
            'api-secret' => "Bearer {$this->apiSecret}",
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Create a Virtual Bank Account
     * 
     * @param array $payload
     * @return array
     * @throws \InvalidArgumentException
     */
    public function createVirtualAccount(array $payload): array
    {
        if ((!isset($payload['bvn']) && !isset($payload['nin'])) || (isset($payload['bvn']) && isset($payload['nin']))) {
            throw new \InvalidArgumentException('Either "bvn" or "nin" must be provided, but not both.');
        }

        $response = $this->httpClient()->post(
            "{$this->baseUrl}/pms/api/external/request/customerReservedAccount/",
            array_merge($payload, [
                'businessid' => $this->businessId,
            ])
        );

        return $response->json();
    }

    /**
     * Get Single Virtual Account Details
     * 
     * @param string|int $account
     * @return array
     */
    public function getSingleVirtualAccount(string|int $account): array
    {
        $response = $this->httpClient()->get(
            "{$this->baseUrl}/pms/api/external/request/virtual-account/{$this->businessId}/{$account}/"
        );

        return $response->json();
    }

    /**
     * Update Virtual Account BVN
     * 
     * @param string|int $account
     * @param string|int $bvn
     * @return array
     */
    public function accountBVNUpdate(string|int $account, string|int $bvn): array
    {
        $response = $this->httpClient()->post(
            "{$this->baseUrl}/pms/api/external/request/virtual-account/{$this->businessId}/{$account}/",
            ['bvn' => $bvn]
        );

        return $response->json();
    }
}
