<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class SearchPageController extends Controller
{
    private const PALETTES = ['red', 'amber', 'violet', 'green', 'pink', 'teal', 'rose', 'lime', 'orange', 'indigo', 'fuchsia', 'blue'];
    private const PATTERNS = ['bands', 'rings', 'horizon', 'orb'];

    public function index(): View
    {
        $genres = Category::orderBy('id')->get()->values()->map(fn (Category $category, int $i) => [
            'name' => $category->name,
            'color' => $category->color,
            'palette' => self::PALETTES[$i % count(self::PALETTES)],
            'pattern' => self::PATTERNS[$i % count(self::PATTERNS)],
        ])->all();

        return view('search', ['genres' => $genres]);
    }
}
