<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\CreatePlayerRequest;
use App\Services\PlayerService;
use App\Models\Player;

class PlayerController extends Controller
{
    protected PlayerService $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    /**
     * Handles the creation of a player.
     *
     * @param CreatePlayerRequest $request
     * @return JsonResponse
     */
    public function store(CreatePlayerRequest $request): JsonResponse
    {
        $player = $this->playerService->createPlayer($request->validated());

        return response()->json($player->load('skills'), 201);
    }

    /**
     * Update the specified player in storage.
     *
     * @param CreatePlayerRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(CreatePlayerRequest $request, int $id): JsonResponse
    {
        $player = Player::findOrFail($id);
        $updatedPlayer = $this->playerService->updatePlayer($player, $request->validated());

        return response()->json($updatedPlayer, 200);
    }

    /**
     * Display a listing of the players.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $players = $this->playerService->getAllPlayers();

        return response()->json($players);
    }

    /**
     * Display the specified player.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $player = Player::find($id);

        if (!$player) {
            return response()->json(['message' => 'Player not found.'], 404);
        }

        return response()->json($player->load('skills'));
    }

    /**
     * Delete the specified player from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->playerService->deletePlayer($id);

        if ($result === null) {
            return response()->json(['message' => 'Player not found.'], 404);
        }

        return response()->json(['message' => 'Player has been deleted.'], 200);
    }
}
