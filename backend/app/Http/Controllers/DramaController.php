<?php

namespace App\Http\Controllers;

use App\Models\Drama;
use App\Models\Category;
use App\Models\Episode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DramaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Drama::with('category');

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $dramas = $query->latest()->paginate(20);

        return response()->json([
            'dramas' => $dramas->items(),
            'meta' => [
                'current_page' => $dramas->currentPage(),
                'last_page' => $dramas->lastPage(),
                'total' => $dramas->total(),
            ],
        ]);
    }

    public function show($id): JsonResponse
    {
        $drama = Drama::with('category')->findOrFail($id);

        return response()->json([
            'drama' => $drama,
        ]);
    }

    public function episodes($id): JsonResponse
    {
        $episodes = Episode::where('drama_id', $id)
            ->orderBy('episode_number')
            ->get();

        return response()->json([
            'episodes' => $episodes,
        ]);
    }

    public function episode($id, $episodeId): JsonResponse
    {
        $episode = Episode::where('drama_id', $id)
            ->where('id', $episodeId)
            ->firstOrFail();

        return response()->json([
            'episode' => $episode,
        ]);
    }

    public function trending(): JsonResponse
    {
        $dramas = Drama::with('category')
            ->where('is_trending', true)
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'dramas' => $dramas,
        ]);
    }

    public function featured(): JsonResponse
    {
        $dramas = Drama::with('category')
            ->where('is_featured', true)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'dramas' => $dramas,
        ]);
    }

    public function recent(): JsonResponse
    {
        $dramas = Drama::with('category')
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'dramas' => $dramas,
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::all();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $dramas = Drama::with('category')
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'dramas' => $dramas,
        ]);
    }
}