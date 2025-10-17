<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fighter;

class FighterController extends Controller
{
    public function index()
    {
        $fighters = Fighter::all();
        return view('fighters.index', compact('fighters'));
    }

    public function threads(Fighter $fighter)
    {
        return view('fighters.show', compact('fighter'));
    }
}
