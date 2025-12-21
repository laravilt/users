<?php

namespace Laravilt\Users\Resources\Users\Pages;

use Laravilt\Panel\Pages\ListRecords;
use Laravilt\Users\Resources\Users\UserResource;

class ListUsers extends ListRecords
{
    protected static ?string $resource = UserResource::class;
}
