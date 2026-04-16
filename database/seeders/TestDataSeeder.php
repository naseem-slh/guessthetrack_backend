<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Room;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users
        $user1 = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User 1',
                'password' => Hash::make('password'),
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'test2@example.com'],
            [
                'name' => 'Test User 2',
                'password' => Hash::make('password'),
            ]
        );

        $user3 = User::updateOrCreate(
            ['email' => 'test3@example.com'],
            [
                'name' => 'Test User 3',
                'password' => Hash::make('password'),
            ]
        );

        // Create test room
        $room = Room::updateOrCreate(
            ['name' => 'Test Room'],
            ['creator_id' => $user1->id]
        );

        // Sync users to room with proper pivot data (this will update existing relationships)
        $room->users()->sync([
            $user1->id => [
                'role' => 'owner',
                'status' => 'accepted',
                'invited_at' => now(),
                'invited_by' => $user1->id
            ],
            $user2->id => [
                'role' => 'member',
                'status' => 'accepted',
                'invited_at' => now(),
                'invited_by' => $user1->id
            ]
        ]);    }
}