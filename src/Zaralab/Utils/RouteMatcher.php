<?php
/**
 * Project: zaralab
 * Filename: RouteMatcher.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Utils;


use Psr\Http\Message\ServerRequestInterface;

class RouteMatcher
{
    protected $path;
    protected $ignore;

    public function __construct(array $path = [], array $ignore = [])
    {
        $this->path = $path;
        $this->ignore = $ignore;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $uriPath = $this->normalizePath($request->getUri()->getPath());
        $basePath = $request->getUri()->getBasePath();

        if (!empty($basePath)) {
            $basePath = $this->normalizePath($basePath);
            $uriPath = $this->normalizePath(
                            substr_replace(
                                $uriPath, '',
                                strpos($uriPath, $basePath),
                                strlen($basePath)
                            )
                        );
        }

        foreach ($this->ignore as $path) {
            $path = $this->normalizePath($path);
            if (strpos($uriPath, $path) !== false) {
                return false;
            }
        }

        foreach ($this->path as $path) {
            $path = $this->normalizePath($path);
            if (strpos($uriPath, $path) !== false) {
                return true;
            }
        }

        return false;
    }

    public function normalizePath($path)
    {
        return '/'.trim($path, '/');
    }
}