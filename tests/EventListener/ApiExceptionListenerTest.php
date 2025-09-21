<?php

namespace App\Tests\EventListener;

use App\EventListener\ApiExceptionListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ApiExceptionListenerTest extends TestCase
{
    public function testOnKernelExceptionWhenHttpException(): void
    {
        $exception = new NotFoundHttpException();

        $exceptionEvent = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            $this->createStub(Request::class),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener = new ApiExceptionListener();
        $listener->onKernelException($exceptionEvent);

        $response = $exceptionEvent->getResponse();
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(404, $response->getStatusCode());
    }

    public function testOnKernelExceptionWhenGeneralException(): void
    {
        $exception = new \Exception('Some random error');

        $exceptionEvent = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            $this->createStub(Request::class),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener = new ApiExceptionListener();
        $listener->onKernelException($exceptionEvent);

        $response = $exceptionEvent->getResponse();
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(500, $response->getStatusCode());
    }
}
