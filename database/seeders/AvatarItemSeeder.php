<?php

namespace Database\Seeders;

use App\Models\AvatarItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AvatarItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Base bodies
            ['type' => 'base', 'name' => 'Default Body',    'rarity' => 'common',   'is_default' => true],
            ['type' => 'base', 'name' => 'Athletic Body',   'rarity' => 'uncommon', 'is_default' => false],

            // Skin tones
            ['type' => 'skin', 'name' => 'Light Skin',      'rarity' => 'common',   'is_default' => true],
            ['type' => 'skin', 'name' => 'Medium Skin',     'rarity' => 'common',   'is_default' => false],
            ['type' => 'skin', 'name' => 'Dark Skin',       'rarity' => 'common',   'is_default' => false],

            // Eyes
            ['type' => 'eyes', 'name' => 'Default Eyes',    'rarity' => 'common',   'is_default' => true],
            ['type' => 'eyes', 'name' => 'Blue Eyes',       'rarity' => 'uncommon', 'is_default' => false],
            ['type' => 'eyes', 'name' => 'Glowing Eyes',    'rarity' => 'rare',     'is_default' => false],

            // Mouth
            ['type' => 'mouth', 'name' => 'Smile',          'rarity' => 'common',   'is_default' => true],
            ['type' => 'mouth', 'name' => 'Smirk',          'rarity' => 'uncommon', 'is_default' => false],

            // Hair
            ['type' => 'hair', 'name' => 'Short Brown',     'rarity' => 'common',   'is_default' => true],
            ['type' => 'hair', 'name' => 'Long Black',      'rarity' => 'common',   'is_default' => false],
            ['type' => 'hair', 'name' => 'Neon Green',      'rarity' => 'rare',     'is_default' => false],
            ['type' => 'hair', 'name' => 'Cyber White',     'rarity' => 'epic',     'is_default' => false],

            // Shirts
            ['type' => 'shirt', 'name' => 'White Tee',      'rarity' => 'common',   'is_default' => true],
            ['type' => 'shirt', 'name' => 'Blue Hoodie',    'rarity' => 'common',   'is_default' => false],
            ['type' => 'shirt', 'name' => 'Leather Jacket', 'rarity' => 'rare',     'is_default' => false],
            ['type' => 'shirt', 'name' => 'Neon Hoodie',    'rarity' => 'epic',     'is_default' => false, 'price' => 500],

            // Pants
            ['type' => 'pants', 'name' => 'Jeans',          'rarity' => 'common',   'is_default' => true],
            ['type' => 'pants', 'name' => 'Black Joggers',  'rarity' => 'common',   'is_default' => false],
            ['type' => 'pants', 'name' => 'Camo Pants',     'rarity' => 'uncommon', 'is_default' => false],

            // Shoes
            ['type' => 'shoes', 'name' => 'White Sneakers', 'rarity' => 'common',   'is_default' => true],
            ['type' => 'shoes', 'name' => 'Black Boots',    'rarity' => 'uncommon', 'is_default' => false],
            ['type' => 'shoes', 'name' => 'Neon Kicks',     'rarity' => 'rare',     'is_default' => false, 'price' => 200],

            // Hats
            ['type' => 'hat', 'name' => 'Red Cap',          'rarity' => 'common',   'is_default' => false],
            ['type' => 'hat', 'name' => 'Beanie',           'rarity' => 'common',   'is_default' => false],
            ['type' => 'hat', 'name' => 'Crown',            'rarity' => 'legendary','is_default' => false, 'price' => 5000],

            // Glasses
            ['type' => 'glasses', 'name' => 'Sunglasses',   'rarity' => 'uncommon', 'is_default' => false],
            ['type' => 'glasses', 'name' => 'Nerd Glasses', 'rarity' => 'common',   'is_default' => false],
            ['type' => 'glasses', 'name' => 'Cyber Visor',  'rarity' => 'epic',     'is_default' => false, 'price' => 1000],

            // Accessories
            ['type' => 'accessory', 'name' => 'Headphones', 'rarity' => 'uncommon', 'is_default' => false],
            ['type' => 'accessory', 'name' => 'Scarf',      'rarity' => 'common',   'is_default' => false],

            // Back items
            ['type' => 'back', 'name' => 'Backpack',        'rarity' => 'common',   'is_default' => false],
            ['type' => 'back', 'name' => 'Wings',           'rarity' => 'legendary','is_default' => false, 'price' => 10000],

            // Effects
            ['type' => 'effect', 'name' => 'Blue Glow',     'rarity' => 'rare',     'is_default' => false],
            ['type' => 'effect', 'name' => 'Fire Aura',     'rarity' => 'epic',     'is_default' => false, 'price' => 2000],
            ['type' => 'effect', 'name' => 'Rainbow Aura',  'rarity' => 'legendary','is_default' => false, 'price' => 8000],

            // Pets
            ['type' => 'pet', 'name' => 'Mini Cat',         'rarity' => 'rare',     'is_default' => false, 'price' => 1500],
            ['type' => 'pet', 'name' => 'Robot Dog',        'rarity' => 'epic',     'is_default' => false, 'price' => 3000],

            // Badges
            ['type' => 'badge', 'name' => 'Sub Badge',      'rarity' => 'subscriber','is_default' => false, 'is_subscriber_only' => true],
            ['type' => 'badge', 'name' => 'VIP Badge',      'rarity' => 'vip',       'is_default' => false, 'is_vip_only' => true],
        ];

        foreach ($items as $data) {
            $slug = Str::slug($data['name']);
            AvatarItem::firstOrCreate(['slug' => $slug], array_merge($data, [
                'slug'       => $slug,
                'image_path' => "/items/{$data['type']}/{$slug}.svg",
            'animated_image_path' => null,
                'sort_order' => 0,
            ]));
        }
    }
}
