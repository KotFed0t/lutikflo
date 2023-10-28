<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Traits\Models\HasMoonShineChangeLog;

class Package extends Model
{
    use HasFactory;
    use HasMoonShineChangeLog;
}
