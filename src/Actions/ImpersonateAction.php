<?php

namespace Laravilt\Users\Actions;

use Illuminate\Database\Eloquent\Model;
use Laravilt\Actions\Action;
use Laravilt\Users\Services\ImpersonationService;

class ImpersonateAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('impersonate');

        $this->label(__('laravilt-users::users.actions.impersonate'));

        $this->tooltip(__('laravilt-users::users.actions.impersonate_tooltip'));

        $this->icon('user-cog');

        $this->color('warning');

        $this->requiresConfirmation();

        $this->modalHeading(__('laravilt-users::users.actions.impersonate_heading'));

        $this->modalDescription(__('laravilt-users::users.actions.impersonate_description'));

        $this->modalSubmitActionLabel(__('laravilt-users::users.actions.impersonate_confirm'));

        // Check if impersonation is enabled and user can impersonate
        $this->visible(function (Model $record): bool {
            // Check if impersonation feature is enabled (disabled by default)
            if (! config('laravilt-users.features.impersonation', false)) {
                return false;
            }

            $user = auth()->user();

            // Not logged in
            if (! $user) {
                return false;
            }

            // Can't impersonate yourself
            if ($record->getKey() === $user->getKey()) {
                return false;
            }

            // Check if user can impersonate (if method exists)
            if (method_exists($user, 'canImpersonate') && ! $user->canImpersonate()) {
                return false;
            }

            // Check if target can be impersonated (if method exists)
            if (method_exists($record, 'canBeImpersonated') && ! $record->canBeImpersonated()) {
                return false;
            }

            return true;
        });

        // Capture config values outside closure to avoid serializing $this
        $redirectTo = config('laravilt-users.impersonation.redirect_to', '/admin');

        $this->action(function (Model $record) use ($redirectTo) {
            $impersonator = auth()->user();
            $service = app(ImpersonationService::class);
            $service->impersonate($impersonator, $record);

            // Flash success message for the impersonation
            session()->flash('notification', [
                'type' => 'success',
                'title' => __('laravilt-users::users.notifications.impersonating'),
            ]);

            return redirect($redirectTo);
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'impersonate';
    }
}
