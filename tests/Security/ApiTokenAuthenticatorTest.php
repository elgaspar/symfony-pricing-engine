<?php

namespace App\Tests\Security;

use App\Security\ApiTokenAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticatorTest extends TestCase
{
    public function testSupports(): void
    {
        $authenticator = new ApiTokenAuthenticator('valid_token');
        $request = $this->createStub(Request::class);

        self::assertTrue($authenticator->supports($request));
    }

    public function testAuthenticate(): void
    {
        $validToken = 'valid_token';
        $authenticator = new ApiTokenAuthenticator($validToken);
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer ' . $validToken);

        $passport = $authenticator->authenticate($request);

        self::assertInstanceOf(SelfValidatingPassport::class, $passport);
    }

    public function testAuthenticateWhenMissingHeader(): void
    {
        $this->expectException(AuthenticationException::class);

        $validToken = 'valid_token';
        $authenticator = new ApiTokenAuthenticator($validToken);
        $request = new Request();

        $authenticator->authenticate($request);
    }

    public function testAuthenticateWhenMissingValue(): void
    {
        $this->expectException(AuthenticationException::class);

        $validToken = 'valid_token';
        $authenticator = new ApiTokenAuthenticator($validToken);
        $request = new Request();
        $request->headers->set('Authorization', null);

        $authenticator->authenticate($request);
    }

    public function testAuthenticateWhenMalformedValue(): void
    {
        $this->expectException(AuthenticationException::class);

        $validToken = 'valid_token';
        $authenticator = new ApiTokenAuthenticator($validToken);
        $request = new Request();
        $request->headers->set('Authorization', 'foo ' . $validToken);

        $authenticator->authenticate($request);
    }

    public function testAuthenticateWhenInvalidValue(): void
    {
        $this->expectException(AuthenticationException::class);

        $validToken = 'valid_token';
        $authenticator = new ApiTokenAuthenticator($validToken);
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer someInvalidToken');

        $authenticator->authenticate($request);
    }

    public function testOnAuthenticationSuccess(): void
    {
        $authenticator = new ApiTokenAuthenticator('valid_token');
        $request = $this->createStub(Request::class);
        $token = $this->createStub(TokenInterface::class);
        $firewallName = 'foo';

        self::assertNull($authenticator->onAuthenticationSuccess($request, $token, $firewallName));
    }

    public function testOnAuthenticationFailure(): void
    {
        $authenticator = new ApiTokenAuthenticator('valid_token');
        $request = $this->createStub(Request::class);
        $exception = new AuthenticationException();

        $response = $authenticator->onAuthenticationFailure($request, $exception);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(401, $response->getStatusCode());
    }
}
