<?php

namespace spec\Zaralab\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Http\Body;
use Slim\Views\Twig;

class ErrorHandlerSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger, Twig $view)
    {
        $this->beConstructedWith(Argument::type('bool'), $logger, $view);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Zaralab\Service\ErrorHandler');
    }

    function it_is_callable()
    {
        $this->shouldBeCallable();
    }

    function it_should_return_response_when_invoked(ServerRequestInterface $request, ResponseInterface $response, \Exception $exception)
    {
        $response->withStatus(Argument::type('integer'))->shouldBeCalledTimes(1)->willReturn($response);
        $response->withHeader(Argument::type('string'), Argument::type('string'))->shouldBeCalledTimes(1)->willReturn($response);
        $response->withBody(Argument::type('Slim\Http\Body'))->shouldBeCalledTimes(1)->willReturn($response);
        $response->getHeader('Content-type')->shouldBeCalledTimes(1)->willReturn('application/json');

        $this($request, $response, $exception)->shouldReturn($response);
    }
}
