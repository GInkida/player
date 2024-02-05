<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Database\Eloquent\Collection;

class PlayerService
{
    /**
     * Gets a list of all players with their skills.
     *
     * @return Collection
     */
    public function getAllPlayers(): Collection
    {
        return Player::with('skills')->get();
    }

    /**
     * Creates a player with skills.
     *
     * @param array $validatedData
     * @return Player
     */
    public function createPlayer(array $validatedData): Player
    {
        $player = Player::create([
            'name' => $validatedData['name'],
            'position' => $validatedData['position'],
        ]);

        foreach ($validatedData['playerSkills'] as $skillData) {
            $player->skills()->create([
                'skill' => $skillData['skill'],
                'value' => $skillData['value'],
            ]);
        }

        return $player;
    }

    /**
     * Updates a player with given data.
     *
     * @param Player $player
     * @param array $data
     * @return Player
     */
    public function updatePlayer(Player $player, array $data): Player
    {
        // Update player information
        $player->update([
            'name' => $data['name'],
            'position' => $data['position'],
        ]);

        // Remove old skills
        $player->skills()->delete();

        // Add new/updated skills
        foreach ($data['playerSkills'] as $skillData) {
            $player->skills()->create([
                'skill' => $skillData['skill'],
                'value' => $skillData['value'],
            ]);
        }

        return $player->fresh('skills'); // Return the player instance with refreshed skills
    }

    /**
     * Deletes a player by ID.
     *
     * @param int $id
     * @return bool|null
     */
    public function deletePlayer(int $id): ?bool
    {
        $player = Player::find($id);

        return $player?->delete();
    }
}
