<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\status;

class statusController extends Controller
{
  public function All()
  {
    return response()->json(status::All(),200);
  }
}
