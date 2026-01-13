<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * RulesController displays tournament rules and format information.
 */
class RulesController extends Controller
{
    /**
     * Show the tournament rules page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('rules.index');
    }
}
