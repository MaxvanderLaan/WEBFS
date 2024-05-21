<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MealAddition;

class MealAdditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mealAdditions = ['witte rijst', 'nasi', 'bami goreng', 'mihoen goreng', 'chinese bami'];

        foreach ($mealAdditions as $addition) {
            MealAddition::create([
                'name' => $addition,
            ]);
        }
    }
}