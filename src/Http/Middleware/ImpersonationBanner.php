<?php

namespace Laravilt\Users\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravilt\Users\Services\ImpersonationService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        // Only inject banner for HTML responses (streamed/file responses cannot have their content replaced)
        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse || ! $this->isHtmlResponse($response)) {
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
        // The name is user-controlled: escape it (and the rest) before injecting into the page
        $impersonatorName = e($this->impersonationService->getImpersonator()?->name ?? '');
        $stopUrl = e(route('laravilt.users.stop-impersonation'));
        $csrfToken = e($this->getCsrfToken());

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
            <span>You are impersonating as <strong>{$impersonatorName}</strong></span>
            <form action="{$stopUrl}" method="POST" style="margin: 0;">
                <input type="hidden" name="_token" value="{$csrfToken}">
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
