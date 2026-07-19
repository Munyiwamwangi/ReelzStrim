<?php

namespace App\Http\Controllers;

use App\Models\WatchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $history = WatchHistory::with(['drama:id,title', 'episode:id,episode_number'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'history' => $history,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'drama_id' => 'required|exists:dramas,id',
            'episode_id' => 'required|exists:episodes,id',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $history = WatchHistory::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'episode_id' => $validated['episode_id'],
            ],
            [
                'drama_id' => $validated['drama_id'],
                'progress' => $validated['progress'],
                'duration' => $validated['progress'],
            ]
        );

        return response()->json([
            'history' => $history->load(['drama:id,title', 'episode:id,episode_number']),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        WatchHistory::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'History cleared']);
    }
}