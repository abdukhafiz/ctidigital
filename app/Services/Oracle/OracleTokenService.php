<?php

namespace App\Services\Oracle;

use App\Exceptions\OracleException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OracleTokenService
{

    const ORACLE_TOKEN_CACHE_KEY = 'oracle_token_cache_key';

    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.oracle.base_url');
        $this->clientId = config('services.oracle.client_id');
        $this->clientSecret = config('services.oracle.client_secret');
        $this->username = config('services.oracle.username');
        $this->password = config('services.oracle.password');
    }

    /**
     * Get the access token from cache or request a new one
     *
     * @return string
     * @throws ConnectionException
     * @throws OracleException
     */
    protected function getAccessToken(): string
    {
        $token = Cache::get(self::ORACLE_TOKEN_CACHE_KEY);

        if ($token) {
            return $token;
        }

        return $this->requestNewAccessToken();
    }

    /**
     * Request a new access token from Oracle
     *
     * @return string
     * @throws ConnectionException
     * @throws OracleException
     */
    protected function requestNewAccessToken(): string
    {
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->post($this->baseUrl . '/oauth/v1/tokens', [
                'username' => $this->username,
                'password' => $this->password
            ]);

        if ($response->successful()) {
            $token = $response->json('access_token');
            Cache::put(self::ORACLE_TOKEN_CACHE_KEY, $token, 55 * 60);              // 55 minutes

            Log::info('New Oracle access token fetched and cached');

            return $token;
        }

        Log::error('Failed to fetch Oracle access token', [
            'status' => $response->status(),
            'response' => $response->json(),
        ]);

        throw new OracleException('Failed to fetch Oracle access token');
    }

}
