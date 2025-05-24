<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class marker_reviews_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 50) as $index) {

            DB::table('marker_reviews')->insert([
                'review_stars' => mt_rand(0,10),
                'review_content' => "Review Content Test ($index)",
                'user_id' => mt_rand(1,50),
                'marker_id' => mt_rand(1,50),
            ]);
        }
    }
}
