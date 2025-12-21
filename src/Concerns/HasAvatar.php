<?php

namespace Laravilt\Users\Concerns;

use Spatie\MediaLibrary\InteractsWithMedia;

trait HasAvatar
{
    use InteractsWithMedia;

    /**
     * Initialize the HasAvatar trait.
     */
    public function initializeHasAvatar(): void
    {
        $this->append('avatar_url');
    }

    /**
     * Register media collections for the avatar.
     */
    public function registerMediaCollections(): void
    {
        $collection = config('laravilt-users.avatar.collection', 'avatar');
        $fallback = config('laravilt-users.avatar.fallback', 'https://ui-avatars.com/api/');

        $this->addMediaCollection($collection)
            ->singleFile()
            ->useFallbackUrl($fallback.'?name='.urlencode($this->name ?? 'User').'&color=7F9CF5&background=EBF4FF');
    }

    /**
     * Get the avatar URL from media library.
     * Used as Laravel accessor - accessible as $user->avatar_url
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getAvatarUrl();
    }

    /**
     * Get the avatar URL.
     * Can be called directly as $user->getAvatarUrl()
     */
    public function getAvatarUrl(): string
    {
        $collection = config('laravilt-users.avatar.collection', 'avatar');
        $media = $this->getFirstMediaUrl($collection);

        if ($media) {
            return $media;
        }

        $fallback = config('laravilt-users.avatar.fallback', 'https://ui-avatars.com/api/');

        return $fallback.'?name='.urlencode($this->name ?? 'User').'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the avatar media.
     */
    public function getAvatar(): ?\Spatie\MediaLibrary\MediaCollections\Models\Media
    {
        $collection = config('laravilt-users.avatar.collection', 'avatar');

        return $this->getFirstMedia($collection);
    }

    /**
     * Check if the user has an avatar.
     */
    public function hasAvatar(): bool
    {
        return $this->getAvatar() !== null;
    }

    /**
     * Delete the avatar.
     */
    public function deleteAvatar(): void
    {
        $collection = config('laravilt-users.avatar.collection', 'avatar');
        $this->clearMediaCollection($collection);
    }
}
