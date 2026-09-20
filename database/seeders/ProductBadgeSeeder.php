<?php

namespace Database\Seeders;

use App\Models\product\ProductBadge;
use Illuminate\Database\Seeder;

class ProductBadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'label'          => 'Hot deal',
                'value'          => 'hot_deal',
                'color'          => 'red',
                'icon'           => 'fire',
                'animation_type' => 'flicker', // Blinkit fire effect
                'seq_order'      => 1,
                'is_active'      => true,
            ],
            [
                'label'          => 'Best seller',
                'value'          => 'best_seller',
                'color'          => 'orange',
                'icon'           => 'star',
                'animation_type' => 'spin-slow', // Zepto star effect
                'seq_order'      => 2,
                'is_active'      => true,
            ],
            [
                'label'          => 'Limited',
                'value'          => 'limited',
                'color'          => 'amber',
                'icon'           => 'clock',
                'animation_type' => 'pulse',
                'seq_order'      => 3,
                'is_active'      => true,
            ],
            [
                'label'          => 'New arrival',
                'value'          => 'new_arrival',
                'color'          => 'green',
                'icon'           => 'zap',
                'animation_type' => 'bounce',
                'seq_order'      => 4,
                'is_active'      => true,
            ],
            [
                'label'          => 'Trending',
                'value'          => 'trending',
                'color'          => 'blue',
                'icon'           => 'trending',
                'animation_type' => 'bounce',
                'seq_order'      => 5,
                'is_active'      => true,
            ],
            [
                'label'          => 'Featured',
                'value'          => 'featured',
                'color'          => 'purple',
                'icon'           => 'award',
                'animation_type' => 'pulse',
                'seq_order'      => 6,
                'is_active'      => true,
            ],
            [
                'label'          => 'Exclusive',
                'value'          => 'exclusive',
                'color'          => 'pink',
                'icon'           => 'award',
                'animation_type' => 'spin-slow',
                'seq_order'      => 7,
                'is_active'      => true,
            ],
            [
                'label'          => 'Flash sale',
                'value'          => 'flash_sale',
                'color'          => 'red',
                'icon'           => 'zap',
                'animation_type' => 'shake',
                'seq_order'      => 8,
                'is_active'      => true,
            ],
            [
                'label'          => 'Combo offer',
                'value'          => 'combo_offer',
                'color'          => 'teal',
                'icon'           => 'gift',
                'animation_type' => 'bounce',
                'seq_order'      => 9,
                'is_active'      => true,
            ],
            [
                'label'          => 'Clearance',
                'value'          => 'clearance',
                'color'          => 'gray',
                'icon'           => 'tag',
                'animation_type' => 'pulse',
                'seq_order'      => 10,
                'is_active'      => true,
            ],
        ];

        foreach ($badges as $badge) {
            ProductBadge::updateOrCreate(
                ['value' => $badge['value']],
                $badge
            );
        }

        $this->command->info('Product badges seeded: ' . count($badges) . ' records');
    }
}