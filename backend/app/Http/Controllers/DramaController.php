<?php

namespace App\Http\Controllers;

use App\Models\Drama;
use App\Models\Category;
use App\Models\Episode;
use App\Models\WatchHistory;
use App\Models\Favorite;
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

    /**
     * Highly rated dramas (sorted by rating descending)
     */
    public function highlyRated(): JsonResponse
    {
        $dramas = Drama::with('category')
            ->orderBy('rating', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'dramas' => $dramas,
        ]);
    }

    /**
     * Most watched dramas (sorted by episode count as popularity proxy)
     */
    public function mostWatched(): JsonResponse
    {
        $dramas = Drama::with('category')
            ->orderBy('episode_count', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'dramas' => $dramas,
        ]);
    }

    /**
     * Personalized recommendations based on user's watch history and favorites.
     * Returns dramas from categories the user has engaged with, excluding already-watched ones.
     * Falls back to highly-rated if no history exists.
     */
    public function recommendations(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            // Guest: return top-rated dramas
            $dramas = Drama::with('category')
                ->orderBy('rating', 'desc')
                ->take(10)
                ->get();

            return response()->json([
                'dramas' => $dramas,
                'source' => 'top_rated',
            ]);
        }

        // Get category IDs from watch history
        $watchedCategoryIds = WatchHistory::where('user_id', $user->id)
            ->with('drama')
            ->get()
            ->pluck('drama.category_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Get category IDs from favorites
        $favCategoryIds = Favorite::where('user_id', $user->id)
            ->with('drama')
            ->get()
            ->pluck('drama.category_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Merge all engaged category IDs
        $categoryIds = array_unique(array_merge($watchedCategoryIds, $favCategoryIds));

        // Get IDs of dramas already watched
        $watchedDramaIds = WatchHistory::where('user_id', $user->id)
            ->pluck('drama_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($categoryIds)) {
            // No history: return top-rated
            $dramas = Drama::with('category')
                ->orderBy('rating', 'desc')
                ->take(10)
                ->get();

            return response()->json([
                'dramas' => $dramas,
                'source' => 'top_rated',
            ]);
        }

        // Find dramas in those categories, excluding already watched ones
        $dramas = Drama::with('category')
            ->whereIn('category_id', $categoryIds)
            ->whereNotIn('id', $watchedDramaIds)
            ->orderBy('rating', 'desc')
            ->take(10)
            ->get();

        // If not enough recommendations, fill with top-rated
        if ($dramas->count() < 5) {
            $existingIds = $dramas->pluck('id')->toArray();
            $fillers = Drama::with('category')
                ->whereNotIn('id', array_merge($existingIds, $watchedDramaIds))
                ->orderBy('rating', 'desc')
                ->take(10 - $dramas->count())
                ->get();
            $dramas = $dramas->concat($fillers);
        }

        return response()->json([
            'dramas' => $dramas,
            'source' => 'personalized',
        ]);
    }
}