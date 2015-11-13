<?php

namespace spec\Zaralab\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Uri;

class ContentTypeMiddleware extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'application/json' => [
                'path'   => [ '/api' ],
                'ignore' => [ 'api/ping' ]
            ]
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Zaralab\Service\ApiMiddleware');
    }

    function it_is_callable()
    {
        $this->shouldBeCallable();
    }

    function it_should_return_response(ServerRequestInterface $request, ResponseInterface $response, Uri $uri)
    {
        $next = function() use ($response) { return $response->getWrappedObject(); };
        $request->getUri()->shouldBeCalled()->willReturn($uri);
        $this($request, $response, $next)->shouldReturn($response);
    }
}
