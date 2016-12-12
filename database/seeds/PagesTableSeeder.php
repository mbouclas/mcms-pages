<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Mcms\Pages\Models\Page;
use Mcms\Pages\Models\PageCategory;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $categoryIds = [3, 4, 5];

        foreach (range(1, 100) as $index) {
            $title = $faker->sentence(6);
            $descriptionLong = '';

            foreach ($faker->paragraphs(rand(1,3)) as $paragraph){
                $descriptionLong .= "<p>{$paragraph}</p>";
            }

            $page = Page::create([
                'title' => ['en'=> $title],
                'slug' => str_slug($title),
                'description' => ['en'=> "<p>{$faker->paragraph}</p>"],
                'description_long' => ['en'=> $descriptionLong],
                'user_id' => 2,
                'active' => true
            ]);

            $ids = [];
            foreach (array_rand($categoryIds, 2) as $index) {
                $ids[] = $categoryIds[$index];
            }

            $page->categories()->attach($ids);
        }
    }
}
