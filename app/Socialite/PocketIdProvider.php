<?php

namespace App\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class PocketIdProvider extends AbstractProvider
{
    protected $scopes = ['openid', 'profile', 'email'];

    protected $scopeSeparator = ' ';

    protected $usesPKCE = true;

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(config('services.pocketid.base_url').'/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return config('services.pocketid.base_url').'/api/oidc/token';
    }

    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get(config('services.pocketid.base_url').'/api/oidc/userinfo', [
            'headers' => ['Authorization' => 'Bearer '.$token],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['sub'],
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['picture'] ?? null,
            'nickname' => $user['preferred_username'] ?? null,
        ]);
    }
}
