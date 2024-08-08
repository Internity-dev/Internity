<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'kepala program');
        })->get();

        foreach ($teachers as $teacher) {
            News::create([
                'user_id' => $teacher->id,
                'newsable_type' => 'App\Models\Department',
                'newsable_id' => $teacher->departments->first()->id,
                'title' => 'Pengumuman',
                'content' => "
                    <h2>What is Lorem Ipsum?</h2>
                    <p><strong>Lorem Ipsum</strong><span>&nbsp;</span>is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                    <ol>
                        <li>sasjfidsjf ivsdifos dfsdaf</li>
                        <li>askldjsai ofjdsa idfs ao das</li>
                        <li>dasda jasdiasid adjasiojdoias</li>
                    </ol>
                    <p>
                    sdas iajsdisaj disajdiosajd iasjdiasojdiaso diasj disjadio saidjasid asio jdaiso
                    </p>
                ",
                'status' => 1,
                'image' => fake()->imageUrl(),
            ]);

            News::create([
                'user_id' => $teacher->id,
                'newsable_type' => 'App\Models\Department',
                'newsable_id' => $teacher->departments->first()->id,
                'title' => 'Pengumuman 2',
                'content' => "
                    <p>Assalamualaikum</p>
                    <p>Ini adalah pengumuman kedua</p>
                    <ul>
                        <li>asdasd</li>
                        <li>asdasd</li>
                        <li>asdasd</li>
                    </ul>
                    <p>asdasd</p>
                ",
                'status' => 1,
                'image' => fake()->imageUrl(),
            ]);

            News::factory()->count(2)->create([
                'user_id' => $teacher->id,
                'newsable_type' => 'App\Models\Department',
                'newsable_id' => $teacher->departments->first()->id,
            ]);
        }

        $manager = User::whereHas('roles', function ($query) {
            $query->where('name', 'manager');
        })->first();

        News::create([
            'user_id' => $manager->id,
            'newsable_type' => 'App\Models\School',
            'newsable_id' => $manager->schools->first()->id,
            'title' => 'Pengumuman',
            'content' => "
                <h2>What is Lorem Ipsum?</h2>
                <p><strong>Lorem Ipsum</strong><span>&nbsp;</span>is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                <ol>
                    <li>sasjfidsjf ivsdifos dfsdaf</li>
                    <li>askldjsai ofjdsa idfs ao das</li>
                    <li>dasda jasdiasid adjasiojdoias</li>
                </ol>
                <p>
                sdas iajsdisaj disajdiosajd iasjdiasojdiaso diasj disjadio saidjasid asio jdaiso
                </p>
            ",
            'status' => 1,
            'image' => fake()->imageUrl(),
        ]);

        News::create([
            'user_id' => $manager->id,
            'newsable_type' => 'App\Models\School',
            'newsable_id' => $manager->schools->first()->id,
            'title' => 'Pengumuman 2',
            'content' => "
                <p>Assalamualaikum</p>
                <p>Ini adalah pengumuman kedua</p>
                <ul>
                    <li>asdasd</li>
                    <li>asdasd</li>
                    <li>asdasd</li>
                </ul>
                <p>asdasd</p>
            ",
            'status' => 1,
            'image' => fake()->imageUrl(),
        ]);

        News::factory()->count(3)->create([
            'user_id' => $manager->id,
            'newsable_type' => 'App\Models\School',
            'newsable_id' => $manager->schools->first()->id,
        ]);
    }
}
