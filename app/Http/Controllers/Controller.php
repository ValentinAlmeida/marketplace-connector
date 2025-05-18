<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as Eloquent;

class Controller extends Eloquent
{
    use AuthorizesRequests, ValidatesRequests;
}