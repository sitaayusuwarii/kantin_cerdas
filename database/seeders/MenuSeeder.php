<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Seed 4 kategori dan 12 menu dummy.
     * Slug di-generate otomatis oleh event 'creating' pada Model Menu.
     */
    public function run(): void
    {
        // --- Kategori ---
        $categories = [
            ['name' => 'Makanan Berat', 'slug' => 'makanan-berat'],
            ['name' => 'Minuman',       'slug' => 'minuman'],
            ['name' => 'Snack',         'slug' => 'snack'],
            ['name' => 'Dessert',       'slug' => 'dessert'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $ids = Category::whereIn('slug', ['makanan-berat', 'minuman', 'snack', 'dessert'])
            ->pluck('id', 'slug');

        // --- 12 Menu Dummy ---
        $menus = [
            // Makanan Berat
            ['category_id' => $ids['makanan-berat'], 'name' => 'Nasi Goreng Spesial',  'price' => 15000, 'description' => 'Nasi goreng dengan telur, ayam suwir, dan kerupuk.'],
            ['category_id' => $ids['makanan-berat'], 'name' => 'Mie Ayam Bakso',        'price' => 13000, 'description' => 'Mie ayam dengan bakso sapi dan pangsit goreng.'],
            ['category_id' => $ids['makanan-berat'], 'name' => 'Nasi Uduk Komplit',     'price' => 14000, 'description' => 'Nasi uduk dengan ayam goreng, tempe orek, sambal kacang.'],
            ['category_id' => $ids['makanan-berat'], 'name' => 'Lontong Sayur',         'price' => 10000, 'description' => 'Lontong dengan sayur labu siam dan kuah santan gurih.'],
            // Minuman
            ['category_id' => $ids['minuman'],       'name' => 'Es Teh Manis',          'price' => 4000,  'description' => 'Teh manis dingin dengan es batu segar.'],
            ['category_id' => $ids['minuman'],       'name' => 'Jus Alpukat',           'price' => 8000,  'description' => 'Jus alpukat segar dengan susu kental manis.'],
            ['category_id' => $ids['minuman'],       'name' => 'Air Mineral Botol',     'price' => 4000,  'description' => 'Air mineral kemasan 600ml.'],
            // Snack
            ['category_id' => $ids['snack'],         'name' => 'Risoles Mayo',          'price' => 5000,  'description' => 'Risoles sayuran dan mayo, digoreng crispy.'],
            ['category_id' => $ids['snack'],         'name' => 'Pisang Goreng Keju',    'price' => 6000,  'description' => 'Pisang kepok goreng dengan keju parut leleh.'],
            ['category_id' => $ids['snack'],         'name' => 'Cireng Bumbu Rujak',    'price' => 5000,  'description' => 'Aci goreng dengan bumbu rujak pedas manis.'],
            // Dessert
            ['category_id' => $ids['dessert'],       'name' => 'Es Campur',             'price' => 8000,  'description' => 'Cincau, kolang-kaling, nata de coco, sirup merah.'],
            ['category_id' => $ids['dessert'],       'name' => 'Puding Cokelat',        'price' => 6000,  'description' => 'Puding cokelat lembut dengan saus vla susu.'],
        ];

        foreach ($menus as $menuData) {
            Menu::firstOrCreate(
                ['name' => $menuData['name']],
                array_merge($menuData, ['is_available' => true])
                // Slug tidak di-set manual, auto-generated dari booted() event
            );
        }

        $this->command->info('✓ MenuSeeder: 4 kategori dan 12 menu berhasil dibuat.');
    }
}