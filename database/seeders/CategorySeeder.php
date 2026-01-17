<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Tin Tức (parent_id = 1)
            [
                'name' => 'Công Nghệ',
                'slug' => 'cong-nghe',
                'parent' => 1,
                'ordering' => 1,
            ],
            [
                'name' => 'Kinh Tế',
                'slug' => 'kinh-te',
                'parent' => 1,
                'ordering' => 2,
            ],
            [
                'name' => 'Chính Trị',
                'slug' => 'chinh-tri',
                'parent' => 1,
                'ordering' => 3,
            ],
            [
                'name' => 'Thế Giới',
                'slug' => 'the-gioi',
                'parent' => 1,
                'ordering' => 4,
            ],
            // Sở Thích (parent_id = 2)
            [
                'name' => 'Thể Thao',
                'slug' => 'the-thao',
                'parent' => 2,
                'ordering' => 5,
            ],
            [
                'name' => 'Giải Trí',
                'slug' => 'giai-tri',
                'parent' => 2,
                'ordering' => 6,
            ],
            [
                'name' => 'Ẩm Thực',
                'slug' => 'am-thuc',
                'parent' => 2,
                'ordering' => 7,
            ],
            [
                'name' => 'Du Lịch',
                'slug' => 'du-lich',
                'parent' => 2,
                'ordering' => 8,
            ],
            // Hướng Dẫn (parent_id = 3)
            [
                'name' => 'Giáo Dục',
                'slug' => 'giao-duc',
                'parent' => 3,
                'ordering' => 9,
            ],
            [
                'name' => 'Sức Khỏe',
                'slug' => 'suc-khoe',
                'parent' => 3,
                'ordering' => 10,
            ],
            [
                'name' => 'Kỹ Năng',
                'slug' => 'ky-nang',
                'parent' => 3,
                'ordering' => 11,
            ],
            // Khác (parent_id = 4)
            [
                'name' => 'Phong Cách Sống',
                'slug' => 'phong-cach-song',
                'parent' => 4,
                'ordering' => 12,
            ],
            [
                'name' => 'Mẹo Vặt',
                'slug' => 'meo-vat',
                'parent' => 4,
                'ordering' => 13,
            ],
            [
                'name' => 'Tạp Chí',
                'slug' => 'tap-chi',
                'parent' => 4,
                'ordering' => 14,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
