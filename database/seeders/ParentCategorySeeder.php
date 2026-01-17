<?php

namespace Database\Seeders;

use App\Models\ParentCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParentCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentCategories = [
            [
                'name' => 'Tin Tức',
                'slug' => 'tin-tuc',
                'ordering' => 1,
            ],
            [
                'name' => 'Sở Thích',
                'slug' => 'so-thich',
                'ordering' => 2,
            ],
            [
                'name' => 'Hướng Dẫn',
                'slug' => 'huong-dan',
                'ordering' => 3,
            ],
            [
                'name' => 'Khác',
                'slug' => 'khac',
                'ordering' => 4,
            ],
        ];

        foreach ($parentCategories as $parentCategory) {
            ParentCategory::updateOrCreate(
                ['slug' => $parentCategory['slug']],
                $parentCategory
            );
        }
    }
}
