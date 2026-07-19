<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $favorites = Favorite::with('drama.category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->pluck('drama');

        return response()->json([
            'favorites' => $favorites,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'drama_id' => 'required|exists:dramas,id',
        ]);

        $favorite = Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'drama_id' => $validated['drama_id'],
        ]);

        return response()->json([
            'favorite' => $favorite->load('drama.category'),
        ], 201);
    }

    public function destroy($dramaId, Request $request): JsonResponse
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('drama_id', $dramaId)
            ->delete();

        return response()->json(['message' => 'Removed from favorites']);
    }

    public function check($dramaId, Request $request): JsonResponse
    {
        $exists = Favorite::where('user_id', $request->user()->id)
            ->where('drama_id', $dramaId)
            ->exists();

        return response()->json(['favorited' => $exists]);
    }
}