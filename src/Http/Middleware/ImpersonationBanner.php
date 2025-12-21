<?php

namespace Laravilt\Users\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravilt\Users\Services\ImpersonationService;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationBanner
{
    public function __construct(
        protected ImpersonationService $impersonationService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only inject banner for HTML responses
        if (! $this->isHtmlResponse($response)) {
            return $response;
        }

        // Check if we're impersonating
        if (! $this->impersonationService->isImpersonating()) {
            return $response;
        }

        // Inject the impersonation banner
        $content = $response->getContent();
        $banner = $this->renderBanner();

        // Insert banner after <body> tag
        $content = preg_replace(
            '/<body([^>]*)>/i',
            '<body$1>'.$banner,
            $content
        );

        $response->setContent($content);

        return $response;
    }

    /**
     * Check if the response is an HTML response.
     */
    protected function isHtmlResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html') || empty($contentType);
    }

    /**
     * Render the impersonation banner.
     */
    protected function renderBanner(): string
    {
        $impersonator = $this->impersonationService->getImpersonator();
        $stopUrl = route('laravilt.users.stop-impersonation');

        return <<<HTML
        <div id="impersonation-banner" style="
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: #1f2937;
            color: #f3f4f6;
            padding: 10px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 14px;
        ">
            <span>You are impersonating as <strong>{$impersonator?->name}</strong></span>
            <form action="{$stopUrl}" method="POST" style="margin: 0;">
                <input type="hidden" name="_token" value="{$this->getCsrfToken()}">
                <button type="submit" style="
                    background: #ef4444;
                    color: white;
                    border: none;
                    padding: 6px 12px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 13px;
                ">Stop Impersonation</button>
            </form>
        </div>
        <style>
            body { padding-top: 44px !important; }
        </style>
        HTML;
    }

    /**
     * Get the CSRF token.
     */
    protected function getCsrfToken(): string
    {
        return csrf_token();
    }
}
