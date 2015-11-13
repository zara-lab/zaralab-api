<?php

namespace spec\Zaralab\Utils;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Zaralab\Exception\ResourceNotFoundException;
use Zaralab\Spec\Matcher as Match;

class ErrorHelperSpec extends ObjectBehavior
{
    function it_is_initializable(\Exception $exception)
    {
        $this->beConstructedWith($exception);
        $this->shouldHaveType('Zaralab\Utils\ErrorHelper');
    }

    function it_should_have_default_status_code(\Exception $exception)
    {
        $this->beConstructedWith($exception);
        $this->detectStatusCode()->shouldReturn(500);
    }

    function it_does_override_status_code_with_exception_code()
    {
        $exception = new \Exception('', 401);
        $this->beConstructedWith($exception, ['\Exception' => 500]);
        $this->detectStatusCode()->shouldReturn(401);
    }

    function it_does_override_status_code_with_default_when_exception_code_is_missing()
    {
        $exception = new \Exception();
        $this->beConstructedWith($exception, ['\Exception' => 401  ]);
        $this->detectStatusCode()->shouldReturn(401);
    }

    function it_does_override_status_code_with_exception_by_configured_value_only()
    {
        $exception = new \Exception('', 404);
        $this->beConstructedWith($exception, null, ['\Exception' => 401]);
        $this->detectStatusCode()->shouldReturn(401);
    }

    function it_should_always_detect_content_type_from_request(ServerRequestInterface $request, \Exception $exception)
    {
        $this->beConstructedWith($exception);
        $request->getHeaderLine('Accept')->shouldBeCalled()->willReturn('invalid');
        $this->detectContentType($request)->shouldReturn('text/html');

        $request->getHeaderLine('Accept')->shouldBeCalled()->willReturn('application/json');
        $this->detectContentType($request)->shouldReturn('application/json');

        $request->getHeaderLine('Accept')->shouldBeCalled()->willReturn('application/xml');
        $this->detectContentType($request)->shouldReturn('application/xml');

        $request->getHeaderLine('Accept')->shouldBeCalled()->willReturn('text/xml');
        $this->detectContentType($request)->shouldReturn('text/xml');

        $request->getHeaderLine('Accept')->shouldBeCalled()->willReturn('application/json,application/xml');
        $this->detectContentType($request)->shouldReturn('application/json');
    }

    function it_should_be_able_to_configure_default_content_type(ServerRequestInterface $request, \Exception $exception)
    {
        $this->beConstructedWith($exception);
        $request->getHeaderLine('Accept')->shouldBeCalled()->willReturn('invalid');
        $this->detectContentType($request, 'application/json')->shouldReturn('application/json');
    }

    function it_should_always_detect_error_message(\Exception $exception)
    {
        $this->beConstructedWith($exception);
        $this->detectErrorMessage()->shouldReturn('Internal Server Error');
    }

    function it_should_detect_error_message_for_a_matched_exception()
    {
        $exception = new \Exception('ErrorMessage');
        $this->beConstructedWith($exception, null, null, ['\Exception']);
        $this->detectErrorMessage()->shouldReturn('ErrorMessage');
    }

    function it_should_build_array_error_stack_from_exception(\Exception $exception)
    {
        $this->beConstructedWith($exception);
        $this->buildErrorStack(Argument::type('bool'))->shouldBeArray();
        $this->buildErrorStack(true)->shouldContainArrayKeys(['error', 'message', 'code', 'exception']);
        $this->buildErrorStack(false)->shouldContainArrayKeys(['error', 'message', 'code']);
        $this->buildErrorStack(false)->shouldNotContainArrayKey('exception');
    }

    function it_should_be_able_to_render_json_error_response(\Exception $exception)
    {
        $this->beConstructedWith($exception);
        $this->renderJsonErrorResponse(true)->shouldContainJsonKeys(['error', 'message', 'exception']);
        $this->renderJsonErrorResponse(false)->shouldNotContainJsonKey('exception');
    }

    function it_should_be_able_to_render_default_html_error_response(\Exception $exception)
    {
        $this->beConstructedWith($exception);

        $this->renderHtmlErrorResponse(true)->shouldMatchAllInString([
            '#\<p>The application could not run because of the following error:\</p>#',
            '#\<h2\>Trace\</h2\>#'
        ]);

        $this->renderHtmlErrorResponse(false)->shouldMatchInString(
            '#\<p>A website error has occurred. Sorry for the temporary inconvenience.\</p>#'
        );

        $this->renderHtmlErrorResponse(false)->shouldNotMatchAllInString([
            '#\<p>The application could not run because of the following error:\</p>#',
            '#\<h2\>Trace\</h2\>#'
        ]);
    }

    function it_should_be_able_to_render_default_xhtml_error_response(\Exception $exception)
    {
        $this->beConstructedWith($exception);

        $this->renderXmlErrorResponse(true)->shouldMatchAllInString([
            '#\<error>[^\<]+\<message>#',
            '#\<exception>#'
        ]);

        $this->renderXmlErrorResponse(false)->shouldMatchInString(
            '#\<error>[\W]+\<message>#'
        );

        $this->renderXmlErrorResponse(false)->shouldNotMatchInString('#\<exception>#');
    }

    public function getMatchers()
    {
        return [
            'beEmptyString'     => new Match\StringEmpty(),
            'containJsonKeys'   => new Match\JsonKeysAny(),
            'containJsonKey'    => new Match\JsonKeyExists(),
            'containArrayKeys'  => new Match\ArrayKeysExists(),
            'containArrayKey'   => new Match\ArrayKeyExists(),
            'matchAllInString'  => new Match\StringMatchAll(),
            'matchInString'     => new Match\StringMatch(),
        ];
    }
}
