<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class EnsureImgReferrerPolicy
{
    /**
     * Handle an incoming request and ensure all <img> tags have referrerpolicy="no-referrer".
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only modify HTML responses
        $contentType = $response->headers->get('Content-Type') ?? '';
        if (!is_string($contentType) || stripos($contentType, 'text/html') === false) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || $content === '') {
            return $response;
        }

        // Add referrerpolicy="no-referrer" to <img> tags that don't have it.
        $new = preg_replace_callback('/<img\b([^>]*?)(\/)?>/i', function ($m) {
            $attrs = $m[1] ?? '';
            $selfClose = isset($m[2]) ? $m[2] : '';

            if (preg_match('/\breferrerpolicy\s*=\s*("|\')/i', $attrs)) {
                return $m[0];
            }

            // Insert the attribute before the closing slash or end
            return '<img' . $attrs . ' referrerpolicy="no-referrer"' . $selfClose . '>';
        }, $content);

        if ($new !== null && $new !== $content) {
            $response->setContent($new);
            // Adjust content-length header if present
            if ($response->headers->has('Content-Length')) {
                $response->headers->set('Content-Length', strlen($new));
            }
        }

        return $response;
    }
}
