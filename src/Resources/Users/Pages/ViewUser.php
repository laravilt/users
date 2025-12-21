<?php

namespace Laravilt\Users\Resources\Users\Pages;

use Laravilt\Panel\Pages\ViewRecord;
use Laravilt\Users\Resources\Users\UserResource;

class ViewUser extends ViewRecord
{
    protected static ?string $resource = UserResource::class;
}
