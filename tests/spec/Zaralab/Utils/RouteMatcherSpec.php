<?php

namespace spec\Zaralab\Utils;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Uri;

class RouteMatcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Zaralab\Utils\RouteMatcher');
    }

    function it_is_callable()
    {
        $this->shouldBeCallable();
    }

    function it_should_match_request_path(ServerRequestInterface $request, Uri $uri)
    {
        $this->beConstructedWith([ '/api' ], []);

        $request->getUri()->shouldBeCalled()->willReturn($uri);
        $uri->getPath()->shouldBeCalled()->willReturn('/api');
        $uri->getBasePath()->shouldBeCalled()->willReturn('');

        $this($request)->shouldBe(true);
    }

    function it_should_match_deeper_route_and_ignore_base(ServerRequestInterface $request, Uri $uri)
    {
        $this->beConstructedWith([ '/api' ], []);

        $request->getUri()->willReturn($uri);
        $uri->getPath()->willReturn('/app/base/path/api/deeper/route');
        $uri->getBasePath()->willReturn('/app/base/path');

        $this($request)->shouldBe(true);
    }

    function it_should_not_always_match_____right(ServerRequestInterface $request, Uri $uri)
    {
        $this->beConstructedWith([ '/api/you/shell/not/match/me' ], []);

        $request->getUri()->willReturn($uri);
        $uri->getPath()->willReturn('/api/authorize');
        $uri->getBasePath()->willReturn('');

        $this($request)->shouldBe(false);
    }

    function it_can_match_all_____beware(ServerRequestInterface $request, Uri $uri)
    {
        $this->beConstructedWith([ '/' ], []);
        $request->getUri()->willReturn($uri);
        $uri->getPath()->willReturn('/whatever/it/is');
        $uri->getBasePath()->willReturn('');

        $this($request)->shouldBe(true);
    }

    function it_is_normalizing_uri_paths(ServerRequestInterface $request, Uri $uri)
    {
        $this->beConstructedWith([ '//broken/path//' ], []);

        $request->getUri()->willReturn($uri);
        $uri->getPath()->willReturn('/not/broken/path');
        $uri->getBasePath()->willReturn('not//');

        $this($request)->shouldBe(true);

        $uri->getPath()->willReturn('//so/broken/path///');
        $uri->getBasePath()->willReturn('so');
        $this($request)->shouldBe(true);
    }

    function it_can_ignore_paths_by_configuration(ServerRequestInterface $request, Uri $uri)
    {
        $this->beConstructedWith([ '/api' ], [ '/api/ignore' ]);

        $request->getUri()->willReturn($uri);
        $uri->getPath()->willReturn('/api/ignore/path');
        $uri->getBasePath()->willReturn('');

        $this($request)->shouldBe(false);
    }
}
