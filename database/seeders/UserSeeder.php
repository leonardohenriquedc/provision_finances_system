<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            "name" => "Maria Silva",
            "email" => "maria@example.com",
            "password" => "password",
        ]);

        User::factory()->create([
            "name" => "João Santos",
            "email" => "joao@example.com",
            "password" => "password",
        ]);

        User::factory()->create([
            "name" => "Ana Oliveira",
            "email" => "ana@example.com",
            "password" => "password",
        ]);

        User::factory()->create([
            "name" => "Carlos Pereira",
            "email" => "carlos@example.com",
            "password" => "password",
        ]);
    }
}
