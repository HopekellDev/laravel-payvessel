<?php

namespace HopekellDev\Payvessel;

use HopekellDev\Payvessel\Helpers\VirtualAccount;

class Payvessel
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $businessId;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('payvessel.base_url'), '/');
        $this->apiKey = config('payvessel.api_key');
        $this->apiSecret = config('payvessel.api_secret');
        $this->businessId = config('payvessel.business_id');
    }

    public function virtualAccounts(): VirtualAccount
    {
        return new VirtualAccount($this->apiKey,$this->apiSecret, $this->baseUrl, $this->businessId);
    }
}
