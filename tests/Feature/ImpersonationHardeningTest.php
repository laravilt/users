<?php

use Illuminate\Http\Request;
use Laravilt\Users\Http\Middleware\ImpersonationBanner;
use Laravilt\Users\Services\ImpersonationService;
use Laravilt\Users\Tests\Models\User;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

it('escapes the impersonator name in the injected banner', function () {
    $impersonator = User::factory()->create(['name' => '<script>alert(1)</script>']);
    $target = User::factory()->create();
    $this->actingAs($impersonator);

    app(ImpersonationService::class)->impersonate($impersonator, $target);

    $response = app(ImpersonationBanner::class)->handle(
        Request::create('/admin'),
        fn () => response('<html><body><p>page</p></body></html>')
    );

    expect($response->getContent())
        ->toContain('id="impersonation-banner"')
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;')
        ->not->toContain('<script>alert(1)</script>');
});

it('passes streamed responses through untouched while impersonating', function () {
    $impersonator = User::factory()->create();
    $target = User::factory()->create();
    $this->actingAs($impersonator);

    app(ImpersonationService::class)->impersonate($impersonator, $target);

    $streamed = new StreamedResponse(fn () => print ('chunk'));

    $response = app(ImpersonationBanner::class)->handle(Request::create('/export'), fn () => $streamed);

    expect($response)->toBe($streamed);
});

it('passes file download responses through untouched while impersonating', function () {
    $impersonator = User::factory()->create();
    $target = User::factory()->create();
    $this->actingAs($impersonator);

    app(ImpersonationService::class)->impersonate($impersonator, $target);

    // HTML-looking body: if the middleware treated it as HTML it would try to inject after <body>.
    $body = '<html><body><p>report</p></body></html>';
    $path = tempnam(sys_get_temp_dir(), 'laravilt-download-');
    file_put_contents($path, $body);

    $download = new BinaryFileResponse($path, 200, [], true, 'attachment');
    $download->setContentDisposition('attachment', 'report.csv');

    // getContent() is false for file responses; no Content-Type until prepare().
    expect($download->getContent())->toBeFalse();

    $response = app(ImpersonationBanner::class)->handle(Request::create('/export'), fn () => $download);

    expect($response)->toBe($download)
        ->and($response->getContent())->toBeFalse()
        ->and($response->headers->get('Content-Disposition'))->toBe('attachment; filename=report.csv')
        ->and(file_get_contents($response->getFile()->getPathname()))->toBe($body);

    @unlink($path);
});

it('does not throw from canImpersonate when the impersonate permission is not seeded', function () {
    $user = User::factory()->create();

    expect($user->canImpersonate())->toBeFalse();
});
