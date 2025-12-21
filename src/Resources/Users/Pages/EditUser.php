<?php

namespace Laravilt\Users\Resources\Users\Pages;

use Laravilt\Panel\Pages\EditRecord;
use Laravilt\Users\Resources\Users\UserResource;

class EditUser extends EditRecord
{
    protected static ?string $resource = UserResource::class;
}
