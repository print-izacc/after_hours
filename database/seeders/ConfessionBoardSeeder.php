<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class ConfessionBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Demo confessions may only be seeded outside production.');
        }

        $demoPasswordHash = Hash::make('demo-password-123');
        $confessions = [
            [
                'poster_name' => 'Maya',
                'is_censored' => true,
                'title' => 'A note I never sent',
                'content' => 'I still think about the kindness you showed me on the day everything felt difficult. I never found the right words to say thank you, so I am leaving them here instead. I hope life has been gentle with you.',
            ],
            [
                'poster_name' => 'Anonymous',
                'is_censored' => false,
                'title' => 'Taking things one day at a time',
                'content' => 'This week has been a lot, and I am trying to remember that I do not have to solve everything tonight. A little rest counts as progress too.',
            ],
            [
                'poster_name' => 'Jordan Lee',
                'is_censored' => true,
                'title' => 'To my quiet crush',
                'content' => 'Your laugh makes ordinary afternoons feel brighter. I am not ready to say this out loud yet, but I hope you know someone appreciates the warmth you bring into a room.',
            ],
            [
                'poster_name' => 'Sam',
                'is_censored' => false,
                'title' => 'Small wins still count',
                'content' => 'I finally asked for help with something I had been carrying alone. It was scary, and it also made the problem feel smaller. If you have been putting off that conversation, this is your reminder that you deserve support.',
            ],
            [
                'poster_name' => 'R.',
                'is_censored' => true,
                'title' => 'A little appreciation',
                'content' => 'To the friend who checks in even when I say I am fine: thank you for noticing and for staying patient with me.',
            ],
            [
                'poster_name' => 'Taylor',
                'is_censored' => false,
                'title' => 'Letting go of perfect',
                'content' => 'I spent too long waiting until I felt completely ready. Today I started anyway, made mistakes, and learned more than I would have by worrying. That is enough for today.',
            ],
            [
                'poster_name' => 'Anonymous',
                'is_censored' => false,
                'title' => 'A vent about being overwhelmed',
                'content' => 'Sometimes every little task arrives at once and my brain treats all of them like emergencies. I am writing this down to remind myself to pause, pick one thing, and breathe before the next.',
            ],
            [
                'poster_name' => 'Alex Morgan',
                'is_censored' => true,
                'title' => 'Maybe someday I will tell you',
                'content' => 'I like how you listen, how you remember small details, and how you make people feel included. For now, this can be a tiny secret I leave with the night.',
            ],
            [
                'poster_name' => 'Casey',
                'is_censored' => false,
                'title' => 'Proud of making it through',
                'content' => 'Nothing dramatic happened today. I just got up, did what I could, and made it to the end of a hard stretch. I am trying to let that be something worth celebrating.',
            ],
            [
                'poster_name' => 'Jamie Park',
                'is_censored' => true,
                'title' => 'A reminder for whoever needs one',
                'content' => 'You are allowed to change your mind, take a break, and start again. Your pace does not have to look like anyone else’s. Keep going gently.',
            ],
        ];

        foreach ($confessions as $confession) {
            Note::query()->firstOrCreate(
                ['title' => '[Demo] '.$confession['title']],
                [
                    ...$confession,
                    'title' => '[Demo] '.$confession['title'],
                    'password' => $demoPasswordHash,
                ],
            );
        }
    }
}
