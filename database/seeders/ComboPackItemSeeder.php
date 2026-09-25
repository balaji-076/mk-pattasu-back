<?php

namespace Database\Seeders;

use App\Models\ComboPackItems;
use Illuminate\Database\Seeder;

class ComboPackItemSeeder extends Seeder
{
    // public function run(): void
    // {
    //     $comboPackId = 3; // existing Combo Offer id

    //     $items = [
    //         '2 ¾" Kuruvi', '4" Kuruvi', '4" Gold Lakshmi', '5" Kuruvi', '5" Bahubali',
    //         '5" Jallikattu', '6" Lion King', 'Ground Chakkar Spl', 'Ground Chakkar Deluxe',
    //         'Flower Pot Ashoka', 'Colour Kotti', 'Gold Bijili (100 pcs)', 'Paper Bomb ½ kg',
    //         'Paper Bomb ¼ kg', 'Rocket Bomb', 'Silver Rocket', 'Classic Bomb',
    //         'Digital Bomb Ringrolling', '1 ½" Twinkling Star', '4" Twinkling Star',
    //         'Nayara Pencil', '100 Wala', '5K Wala', 'Kit Kat', 'Tom and Jerry White Gold',
    //         'Tom and Jerry Green Red', 'Chakkar Big', 'Chakkar Deluxe', 'Photo Flash',
    //         'Colour Rain', 'Butter Fly', 'Golden Globe', 'Peacock Feather', 'Helicopter',
    //         'Lotus Natiya', 'Chotta Fancy', 'Bambaram', 'Smoke - 3 pcs', 'Peacock (3 in 1)',
    //         'Twix', 'X-Mass', 'Siren Mega', 'Dancing Peacock', 'Money Bank', 'Sky Shot',
    //         'Maya Jal', 'X-Mass Blue Ice', 'Flower Pots', 'Umbrella Sparklers',
    //         'Tri Colour Mega Crackling', 'Bada Peacock', 'Peacock 3 in One',
    //         '1" Chotta Fancy', 'Four Star - 3 pcs', '3 ½" Double Ball', '3 ½" Fancy Pipe',
    //         '4" Pipe', '3 ½" Nayara Pipe', '30 Shot Multi Colour', '60 Shot Multi Colour',
    //         '30 cm Electric Sparklers', '30 cm Colour Sparklers', '30 cm Green Sparklers',
    //         '30 cm Red Sparklers', '15 cm Red Sparklers', '50 cm Electric Sparklers',
    //         'Crackling Fountain', '7 cm Electric Sparklers', '10 cm Electric Sparklers',
    //         '10 cm Colour Sparklers',
    //     ];

    //     foreach ($items as $index => $name) {
    //         ComboPackItems::updateOrCreate(
    //             [
    //                 'combo_pack_id' => $comboPackId,
    //                 'name'          => $name,
    //             ],
    //             [
    //                 'sort_order' => $index,
    //             ]
    //         );
    //     }
    // }

    //      public function run(): void
    // {
    //     $comboPackId = 4;

    //     $items = [
    //         '2 ¾" Kuruvi',
    //         '4" Lakshmi',
    //         '4 ½" Kuruvi',
    //         'Ground Chakkar Deluxe',
    //         'Flower Pot Big',
    //         'Colour Kotti',
    //         'Red Bijili (100 pcs)',
    //         'Gold Bijili (100 pcs)',
    //         'King of King',
    //         'Classic Bomb',
    //         '4" Twinkling Star',
    //         '100 Wala',
    //         '1000 Wala',
    //         'Money Bank Mini',
    //         'Kit Kat',
    //         'Tom and Jerry Red Green',
    //         'Tom and Jerry White Gold',
    //         'Tom and Jerry Red White',
    //         'Special Crackling (12)',
    //         'Special Crackling (12)',
    //         'Photo Flash',
    //         'Cocktail Spinner',
    //         'Colour Rain',
    //         'Butterfly',
    //         'Golden Globe',
    //         'Peacock Feather',
    //         'Helicopter',
    //         'Natiyaday Shower (Lotus)',
    //         'Bambaram',
    //         'Smoke (3 pcs)',
    //         'Twix',
    //         'Peacock (3 in 1)',
    //         'X-Mass',
    //         'Siren Mega',
    //         'Sky Shot',
    //         'Maya Jal',
    //         'Umbrella Sparklers',
    //         'Mega Tri Colour',
    //         '3 ½" Nayara',
    //         '3" Pipe Rock (3 pcs)',
    //         '30 Shot Multicolour',
    //         '30 cm Electric Sparklers',
    //         '30 cm Colour Sparklers',
    //         '15 cm Electric Sparklers',
    //         '30 cm Colour Sparklers',
    //         '30 cm Green Sparklers',
    //         '15 cm Red Sparklers',
    //         'Pokemon',
    //         '12 cm Electric Sparklers',
    //         '12 cm Colour Sparklers',
    //     ];

    //     foreach ($items as $index => $name) {
    //         ComboPackItems::updateOrCreate(
    //             [
    //                 'combo_pack_id' => $comboPackId,
    //                 'name'          => $name,
    //             ],
    //             [
    //                 'sort_order' => $index,
    //             ]
    //         );
    //     }
    // }

    // public function run(): void
    // {
    //     $comboPackId = 1;

    //     $items = [
    //         'Kuruvi',
    //         '3 ½" Lakshmi',
    //         'Gold Lakshmi',
    //         'Ground Chakkar Deluxe',
    //         'Flower Pot Big',
    //         'Colour Kotti',
    //         'Red Bijili (100 pcs)',
    //         'Gold Bijili (100 pcs)',
    //         'King of King',
    //         '4" Twinkling Star',
    //         '100 Wala',
    //         'Money Bank Mini',
    //         'Kit Kat',
    //         'Tom and Jerry Red & Green',
    //         'Tom and Jerry White & Gold',
    //         'Chakkar Big',
    //         'Special Sparklers (12 cm)',
    //         'Photo Flash',
    //         'Cocktail Spinner',
    //         'Colour Rain',
    //         'Butterfly',
    //         'Golden Globe',
    //         'Peacock Feather',
    //         'Helicopter',
    //         'Lotus Shower',
    //         'Bambaram',
    //         'Smoke 3 pcs',
    //         'Peacock 3 in 1',
    //         'X-Mas',
    //         'Siren - Mega',
    //         'Dancing Peacock',
    //         'Red Bloom Gold',
    //         'Gold Fear',
    //         'Maya Jal',
    //         'Tri Colour',
    //         'Crackling Fountain',
    //         'Chotta Fancy',
    //         '15 Shot Multicolour',
    //         '15 cm Red Sparklers',
    //         '15 cm Green Sparklers',
    //         '30 cm Colour Sparklers',
    //         '30 cm Electric Sparklers',
    //         'Flash Matches',
    //         '12 cm Electric Sparklers',
    //         '12 cm Colour Sparklers',
    //     ];

    //     foreach ($items as $index => $name) {
    //         ComboPackItems::updateOrCreate(
    //             [
    //                 'combo_pack_id' => $comboPackId,
    //                 'name'          => $name,
    //             ],
    //             [
    //                 'sort_order' => $index,
    //             ]
    //         );
    //     }
    // }

    // public function run(): void
    // {
    //     $comboPackId = 2;

    //     $items = [
    //         '2 ¾" Kuruvi',
    //         '5" Kuruvi',
    //         '5" Bahubali',
    //         '6" Lion King',
    //         'Ground Chakkar Spl',
    //         'Flower Pot Ashoka',
    //         'Paper Bomb ½ kg Jallikattu',
    //         'Paper Bomb ½ kg',
    //         'Classic Bomb',
    //         'Mega Digital Bomb',
    //         '4" Twinkling Star',
    //         'Rocket Bomb',
    //         '5K Wala',
    //         'Helicopter',
    //         'Kinder Joy',
    //         'Peacock 3 in 1',
    //         'Smoke - 3 pcs',
    //         'Siren Mega',
    //         'Red Sun',
    //         '2" Pipe Single',
    //         'Tom and Jerry',
    //         'Natchathiram (Lotus)',
    //         'Penta',
    //         'Kit Kat',
    //         'Crackling Fountain',
    //         'Chotta Fancy',
    //         'Melody - 3 pcs',
    //         '3 ½" Nayagra',
    //         '3 ½" Fancy Pipe',
    //         '3 ½" Double Ball',
    //         '60 Shot Multi Colour',
    //         '12 cm Electric Sparklers',
    //         '12 cm Colour Sparklers',
    //         '15 cm Green Sparklers',
    //         '30 cm Red Sparklers',
    //     ];

    //     foreach ($items as $index => $name) {
    //         ComboPackItems::updateOrCreate(
    //             [
    //                 'combo_pack_id' => $comboPackId,
    //                 'name'          => $name,
    //             ],
    //             [
    //                 'sort_order' => $index,
    //             ]
    //         );
    //     }
    // }

}