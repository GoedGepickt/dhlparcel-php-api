<?php

namespace Mvdnbrk\DhlParcel\Tests\Feature\Endpoints;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Mvdnbrk\DhlParcel\Tests\TestCase;

/** @group integration */
class AuthenticationTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_an_access_token()
    {
        $accessToken = $this->client->authentication->getAccessToken();

        $this->assertInstanceOf(
            UnencryptedToken::class,
            (new Parser(new JoseEncoder))->parse($accessToken->token)
        );
        $this->assertFalse($accessToken->isExpired());
        $this->assertEquals(getenv('DHLPARCEL_ACCOUNT_ID'), $accessToken->getAccountId());
    }
}
