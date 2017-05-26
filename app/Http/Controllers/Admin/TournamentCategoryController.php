<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TournamentCategory;

class TournamentCategoryController extends Controller
{
    public function index($domain, TournamentCategory $category)
    {
        $vars = [
            'category' => $category,
        ];

        return view('admin.tournamentcategory.index', $vars);
    }
}
