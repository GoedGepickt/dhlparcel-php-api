<?php

namespace Mvdnbrk\DhlParcel\Tests\Unit\Resources;

use Mvdnbrk\DhlParcel\Resources\AccessToken;
use Mvdnbrk\DhlParcel\Tests\TestCase;

class AccessTokenTest extends TestCase
{
    /** @test */
    public function create_a_new_access_token()
    {
        $accessToken = new AccessToken($this->jwt(9));

        $this->assertSame(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJleHAiOjksImFjY291bnRzIjpbXSwicm9sZXMiOltdfQ.c2lnbmF0dXJl',
            $accessToken->token
        );
        $this->assertEquals('1970-01-01 00:00:09', $accessToken->expiresAt->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_can_determine_if_the_access_token_has_expired()
    {
        $accessToken = new AccessToken($this->jwt(0));

        $this->assertTrue($accessToken->isExpired());

        $accessToken = new AccessToken($this->jwt(time() + 999));

        $this->assertFalse($accessToken->isExpired());
    }

    /** @test */
    public function it_can_retrieve_the_account_id_from_the_token()
    {
        $accessToken = new AccessToken($this->jwt(0, ['123456']));

        $this->assertEquals('123456', $accessToken->getAccountId());
    }

    /** @test */
    public function it_can_set_the_account_id()
    {
        $accessToken = new AccessToken($this->jwt(0, ['1111', '2222']));

        $accessToken->setAccountId('does-not-exist');

        $this->assertEquals('1111', $accessToken->getAccountId());

        $accessToken->setAccountId('2222');

        $this->assertEquals('2222', $accessToken->getAccountId());
    }

    /**
     * Build a signed-looking JWT. The signature is never verified, but it must be
     * present: lcobucci/jwt ^5.0 rejects tokens without a signature part.
     */
    private function jwt(int $expiration, array $accounts = [], array $roles = []): string
    {
        return collect([
            ['typ' => 'JWT', 'alg' => 'RS256'],
            ['exp' => $expiration, 'accounts' => $accounts, 'roles' => $roles],
        ])->map(function ($part) {
            return $this->base64UrlEncode(json_encode($part));
        })->push($this->base64UrlEncode('signature'))->implode('.');
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
