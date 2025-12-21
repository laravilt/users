<?php

namespace Laravilt\Users\Resources\Roles\Pages;

use Laravilt\Panel\Pages\EditRecord;
use Laravilt\Users\Resources\Roles\RoleResource;

class EditRole extends EditRecord
{
    protected static ?string $resource = RoleResource::class;
}
