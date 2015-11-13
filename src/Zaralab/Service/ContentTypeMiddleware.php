<?php
/**
 * Project: zaralab
 * Filename: ContentTypeMiddleware.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Service;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zaralab\Utils\RouteMatcher;

class ContentTypeMiddleware
{
    const MATCHER_TYPE_CT = 'content_type';
    const MATCHER_TYPE_AUTH = 'auth';

    /**
     * @var \SplStack|callable[]
     */
    private $matchers;

    public function __construct(array $matchers = [])
    {
        $this->matchers = new \SplStack();

        foreach ($matchers as $type => $matcher) {
            $this->addMatcher($type, new RouteMatcher($matcher['path'], $matcher['ignore']));
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $response = $this->execute($request, $response);

        return $next($request, $response);
    }

    /**
     * Add a matcher to the appropriate stack.
     *
     * @param string $type matcher content-type
     * @param callable $callable Callable which returns a boolean.
     *
     * @return $this
     */
    public function addMatcher($type, callable $callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('Matcher is not callable');
        }

        $this->matchers[] = function(ServerRequestInterface $request, ResponseInterface $response) use($type, $callable) {
            if ($callable($request)) {
                return $response->withHeader('Content-type', $type);
            }
            return $response;
        };

        return $this;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response)
    {
        foreach ($this->matchers as $matcher) {
            $response = $matcher($request, $response);
        }

        return $response;
    }
}