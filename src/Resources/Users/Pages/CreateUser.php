<?php

namespace Laravilt\Users\Resources\Users\Pages;

use Laravilt\Panel\Pages\CreateRecord;
use Laravilt\Users\Resources\Users\UserResource;

class CreateUser extends CreateRecord
{
    protected static ?string $resource = UserResource::class;
}
