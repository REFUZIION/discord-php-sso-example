<?php

class DiscordSSO
{

    private string $clientSecret;
    private string $clientId;
    private string $redirectUri;
    private array $scopes;
    private string $tokenUrl = 'https://discord.com/api/oauth2/token';
    private string $apiUrl = 'https://discord.com/api/users/@me';

    public function __construct($clientId, $clientSecret, $redirectUri, $scopes = ['identify'])
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->scopes = $scopes;
    }

    /**
     * Get the Discord OAuth2 login URL.
     */
    public function getLoginUrl(): string
    {
        $scopes = implode(' ', $this->scopes);
        return sprintf('https://discord.com/api/oauth2/authorize?client_id=%s&redirect_uri=%s&response_type=code&scope=%s', $this->clientId, urlencode($this->redirectUri), urlencode($scopes));
    }

    /**
     * Exchange the authorization code for an access token.
     */
    public function getAccessToken(string $code): ?string
    {
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        ];

        $response = $this->makeRequest($this->tokenUrl, $data);

        if (isset($response['access_token'])) {
            return $response['access_token'];
        }

        return null;
    }

    /**
     * Helper function to make HTTP requests.
     */
    private function makeRequest(string $url, array $postData = [], array $headers = []): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if (!empty($postData)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [];
        }

        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    /**
     * Fetch the user's information using the access token.
     */
    public function getUserInfo(string $accessToken): ?array
    {
        $response = $this->makeRequest($this->apiUrl, [], [
            'Authorization: Bearer ' . $accessToken,
        ]);

        return $response ?: null;
    }
}
