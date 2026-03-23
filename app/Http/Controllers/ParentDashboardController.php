<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class ParentDashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $parent */
        $parent = auth()->user();

        $children = $parent->children()
            ->with(['account', 'transactionsAsChild' => fn ($query) => $query->latest()->limit(5)])
            ->orderBy('name')
            ->get();

        return view('parent.dashboard', [
            'parent' => $parent,
            'children' => $children,
        ]);
    }
}
