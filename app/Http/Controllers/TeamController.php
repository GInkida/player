<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Enums\PlayerPosition;
use App\Enums\PlayerSkill;
use App\Services\TeamSelectionService;

class TeamController extends Controller
{
    private TeamSelectionService $teamSelectionService;

    public function __construct(TeamSelectionService $teamSelectionService)
    {
        $this->teamSelectionService = $teamSelectionService;
    }

    public function processTeamSelection(Request $request): JsonResponse
    {
        $validator = Validator::make($request->except('_url'), [
            '*.position' => ['required', Rule::in(PlayerPosition::cases())],
            '*.mainSkill' => ['required', Rule::in(PlayerSkill::cases())],
            '*.numberOfPlayers' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $selectedPlayers = $this->teamSelectionService->selectTeam($request->except('_url'));

        if ($this->hasInsufficientPlayers($selectedPlayers, $request->all())) {
            return response()->json(['message' => 'Insufficient number of players for requested positions'], 422);
        }

        return response()->json($selectedPlayers);
    }

    private function hasInsufficientPlayers($selectedPlayers, $selectionCriteria): bool
    {
        $totalRequiredPlayers = array_sum(array_column($selectionCriteria, 'numberOfPlayers'));
        return count($selectedPlayers) < $totalRequiredPlayers;
    }
}
