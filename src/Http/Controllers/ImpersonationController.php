<?php

namespace Laravilt\Users\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravilt\Users\Exceptions\CannotImpersonateSelfException;
use Laravilt\Users\Exceptions\CannotImpersonateSuperAdminException;
use Laravilt\Users\Exceptions\UnauthorizedImpersonationException;
use Laravilt\Users\Services\ImpersonationService;

class ImpersonationController extends Controller
{
    public function __construct(
        protected ImpersonationService $impersonationService
    ) {}

    /**
     * Impersonate a user.
     */
    public function impersonate(Request $request, int $userId): RedirectResponse
    {
        $currentUser = $request->user();
        $userModel = config('laravilt-users.model', config('auth.providers.users.model'));
        $targetUser = $userModel::findOrFail($userId);

        // Check authorization
        if (! $this->impersonationService->canImpersonate($currentUser, $targetUser)) {
            throw new UnauthorizedImpersonationException;
        }

        try {
            $this->impersonationService->impersonate($currentUser, $targetUser);

            return redirect(config('laravilt-users.impersonation.redirect_to', '/admin'))
                ->with('success', "You are now impersonating {$targetUser->name}");
        } catch (CannotImpersonateSelfException $e) {
            return back()->with('error', $e->getMessage());
        } catch (CannotImpersonateSuperAdminException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Stop impersonation.
     */
    public function stopImpersonation(): RedirectResponse
    {
        $this->impersonationService->stopImpersonation();

        return redirect(config('laravilt-users.impersonation.back_to', '/admin'))
            ->with('success', 'You have stopped impersonating');
    }
}
