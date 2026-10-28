<?php

namespace App\Http\Controllers;

use App\Models\Publication;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('home');
        }

        $categories = [
            ['name' => 'Publikasi Umum', 'icon' => 'book-open', 'classes' => 'bg-blue-50 text-blue-700'],
            ['name' => 'Statistik Sosial', 'icon' => 'users', 'classes' => 'bg-amber-50 text-amber-700'],
            ['name' => 'Statistik Ekonomi', 'icon' => 'trending-up', 'classes' => 'bg-emerald-50 text-emerald-700'],
            ['name' => 'Statistik Pertanian', 'icon' => 'sprout', 'classes' => 'bg-lime-50 text-lime-700'],
            ['name' => 'Statistik Industri', 'icon' => 'factory', 'classes' => 'bg-purple-50 text-purple-700'],
            ['name' => 'Statistik Distribusi', 'icon' => 'shopping-cart', 'classes' => 'bg-rose-50 text-rose-700'],
            ['name' => 'Statistik Lingkungan', 'icon' => 'trees', 'classes' => 'bg-teal-50 text-teal-700'],
            ['name' => 'Sensus & Survei', 'icon' => 'clipboard-list', 'classes' => 'bg-indigo-50 text-indigo-700'],
        ];

        $counts = Publication::query()
            ->summarized()
            ->selectRaw('category, COUNT(*) as aggregate')
            ->groupBy('category')
            ->pluck('aggregate', 'category');

        foreach ($categories as &$category) {
            $category['count'] = (int) ($counts[$category['name']] ?? 0);
        }
        unset($category);

        return view('welcome', compact('categories'));
    }
}
