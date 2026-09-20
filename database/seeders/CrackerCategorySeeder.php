<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\product\CrackerCategory;

class CrackerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['label' => 'Single Sound Crackers', 'value' => 'single_sound_crackers'],
            ['label' => 'Deluxe Crackers',       'value' => 'deluxe_crackers'],
            ['label' => 'Giant Crackers',         'value' => 'giant_crackers'],
            ['label' => 'Garland',                'value' => 'garland'],
            ['label' => 'Bijili',                 'value' => 'bijili'],
            ['label' => 'Bomb Item',              'value' => 'bomb_item'],
            ['label' => 'Adiyal',                 'value' => 'adiyal'],
            ['label' => 'Ground Chakkar',         'value' => 'ground_chakkar'],
            ['label' => 'Special Chakkar',        'value' => 'special_chakkar'],
            ['label' => 'Flower Pots',            'value' => 'flower_pots'],
            ['label' => 'Peacock Series',         'value' => 'peacock_series'],
            ['label' => 'Twinkling Star',         'value' => 'twinkling_star'],
            ['label' => 'Lovely Sparkler',        'value' => 'lovely_sparkler'],
            ['label' => 'Rocket',                 'value' => 'rocket'],
            ['label' => 'Children Special',       'value' => 'children_special'],
            ['label' => 'New Arrival',            'value' => 'new_arrival'],
            ['label' => 'Multicolor Fountain',    'value' => 'multicolor_fountain'],
            ['label' => 'Special Edition',        'value' => 'special_edition'],
            ['label' => 'Shot Items',             'value' => 'shot_items'],
            ['label' => 'Fancy Out Items',        'value' => 'fancy_out_items'],
            ['label' => 'Sparkler',               'value' => 'sparkler'],
        ];

        foreach ($categories as $index => $category) {
            CrackerCategory::create([
                ...$category,
                'seq_order'     => ($index + 1) * 1,
                'active_status' => true,
            ]);
        }
    }
}