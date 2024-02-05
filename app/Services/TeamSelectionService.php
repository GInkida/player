<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Models\Player;

class TeamSelectionService
{
    public function selectTeam(array $selectionCriteria): Collection
    {
        $selectedPlayers = collect();

        foreach ($selectionCriteria as $criteria) {
            $players = $this->getPlayersForCriteria($criteria);
            $selectedPlayers = $selectedPlayers->concat($players);
        }

        return $selectedPlayers;
    }

    private function getPlayersForCriteria(array $criteria): Collection
    {
        return Player::with(['skills' => function ($query) use ($criteria) {
            $query->where('skill', $criteria['mainSkill'])->orderBy('value', 'desc');
        }])->where('position', $criteria['position'])
            ->get()
            ->sortByDesc(function ($player) use ($criteria) {
                return $player->skills->firstWhere('skill', $criteria['mainSkill'])->value ?? 0;
            })
            ->take($criteria['numberOfPlayers'])
            ->map(function ($player) {
                return [
                    'name' => $player->name,
                    'position' => $player->position,
                    'playerSkills' => $player->skills->map(function ($skill) {
                        return ['skill' => $skill->skill, 'value' => $skill->value];
                    })->toArray(),
                ];
            });
    }
}
