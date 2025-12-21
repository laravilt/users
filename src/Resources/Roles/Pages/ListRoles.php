<?php

namespace Laravilt\Users\Resources\Roles\Pages;

use Laravilt\Panel\Pages\ListRecords;
use Laravilt\Users\Resources\Roles\RoleResource;

class ListRoles extends ListRecords
{
    protected static ?string $resource = RoleResource::class;
}
