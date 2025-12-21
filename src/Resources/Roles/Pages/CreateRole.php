<?php

namespace Laravilt\Users\Resources\Roles\Pages;

use Laravilt\Panel\Pages\CreateRecord;
use Laravilt\Users\Resources\Roles\RoleResource;

class CreateRole extends CreateRecord
{
    protected static ?string $resource = RoleResource::class;
}
