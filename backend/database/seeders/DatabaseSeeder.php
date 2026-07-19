<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Drama;
use App\Models\Episode;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@vidstreamer.com',
            'password' => bcrypt('password'),
        ]);

        // Create categories
        $categories = [
            ['name' => 'Romance', 'slug' => 'romance', 'color' => 'bg-pink-500/30 text-pink-300'],
            ['name' => 'Action', 'slug' => 'action', 'color' => 'bg-red-500/30 text-red-300'],
            ['name' => 'Thriller', 'slug' => 'thriller', 'color' => 'bg-purple-500/30 text-purple-300'],
            ['name' => 'Comedy', 'slug' => 'comedy', 'color' => 'bg-yellow-500/30 text-yellow-300'],
            ['name' => 'Historical', 'slug' => 'historical', 'color' => 'bg-amber-500/30 text-amber-300'],
            ['name' => 'Fantasy', 'slug' => 'fantasy', 'color' => 'bg-indigo-500/30 text-indigo-300'],
            ['name' => 'Mystery', 'slug' => 'mystery', 'color' => 'bg-cyan-500/30 text-cyan-300'],
            ['name' => 'School', 'slug' => 'school', 'color' => 'bg-green-500/30 text-green-300'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Drama data - using public domain/test video URLs
        $videoUrls = [
            'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-720p.mp4',
            'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4',
            'https://media.w3.org/2010/05/sintel/trailer.mp4',
            'https://test-videos.co.uk/vids/bigbuckbunny/mp4/h264/720/Big_Buck_Bunny_720_10s_1MB.mp4',
            'https://test-videos.co.uk/vids/bigbuckbunny/mp4/h264/720/Big_Buck_Bunny_720_10s_2MB.mp4',
            'https://test-videos.co.uk/vids/bigbuckbunny/mp4/h264/720/Big_Buck_Bunny_720_10s_3MB.mp4',
            'https://test-videos.co.uk/vids/bigbuckbunny/mp4/h264/720/Big_Buck_Bunny_720_10s_4MB.mp4',
            'https://test-videos.co.uk/vids/bigbuckbunny/mp4/h264/720/Big_Buck_Bunny_720_10s_5MB.mp4',
        ];
        $trailerUrls = [
            'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4',
            'https://media.w3.org/2010/05/sintel/trailer.mp4',
        ];

        // Vibrant poster images from picsum.photos (each seed gives a consistent unique image)
        $posterUrls = [
            'https://picsum.photos/seed/drama1/400/600',
            'https://picsum.photos/seed/drama2/400/600',
            'https://picsum.photos/seed/drama3/400/600',
            'https://picsum.photos/seed/drama4/400/600',
            'https://picsum.photos/seed/drama5/400/600',
            'https://picsum.photos/seed/drama6/400/600',
            'https://picsum.photos/seed/drama7/400/600',
            'https://picsum.photos/seed/drama8/400/600',
        ];
        $bannerUrls = [
            'https://picsum.photos/seed/banner1/800/400',
            'https://picsum.photos/seed/banner2/800/400',
            'https://picsum.photos/seed/banner3/800/400',
            'https://picsum.photos/seed/banner4/800/400',
            'https://picsum.photos/seed/banner5/800/400',
            'https://picsum.photos/seed/banner6/800/400',
            'https://picsum.photos/seed/banner7/800/400',
            'https://picsum.photos/seed/banner8/800/400',
        ];
        $thumbnailUrls = [
            'https://picsum.photos/seed/thumb1/320/180',
            'https://picsum.photos/seed/thumb2/320/180',
            'https://picsum.photos/seed/thumb3/320/180',
            'https://picsum.photos/seed/thumb4/320/180',
            'https://picsum.photos/seed/thumb5/320/180',
            'https://picsum.photos/seed/thumb6/320/180',
            'https://picsum.photos/seed/thumb7/320/180',
            'https://picsum.photos/seed/thumb8/320/180',
        ];

        $dramas = [
            [
                'title' => 'Eternal Hearts',
                'description' => 'A heartwarming tale of two souls destined to meet across time. When a modern-day architect discovers an ancient diary, she embarks on a journey through past lives to find her eternal love.',
                'rating' => 9.2,
                'year' => 2024,
                'duration' => '45 min',
                'episode_count' => 16,
                'is_featured' => true,
                'is_trending' => true,
                'category_id' => 1,
                'episodes' => [
                    ['title' => 'The Diary', 'episode_number' => 1],
                    ['title' => 'First Glimpse', 'episode_number' => 2],
                    ['title' => 'Echoes of the Past', 'episode_number' => 3],
                    ['title' => 'Crossing Paths', 'episode_number' => 4],
                    ['title' => 'Unraveling Secrets', 'episode_number' => 5],
                ],
            ],
            [
                'title' => 'Shadow Protocol',
                'description' => 'A elite secret agent must stop a rogue organization from unleashing a devastating weapon. Packed with intense action sequences and unexpected plot twists.',
                'rating' => 8.8,
                'year' => 2024,
                'duration' => '50 min',
                'episode_count' => 12,
                'is_featured' => true,
                'is_trending' => true,
                'category_id' => 2,
                'episodes' => [
                    ['title' => 'Operation Dark Dawn', 'episode_number' => 1],
                    ['title' => 'The Mole', 'episode_number' => 2],
                    ['title' => 'Nightfall', 'episode_number' => 3],
                    ['title' => 'Zero Hour', 'episode_number' => 4],
                    ['title' => 'Counterstrike', 'episode_number' => 5],
                ],
            ],
            [
                'title' => 'The Last Empress',
                'description' => 'Set in the final days of a great dynasty, a young peasant woman rises to become the most powerful figure in the empire. A story of courage, sacrifice, and destiny.',
                'rating' => 9.5,
                'year' => 2023,
                'duration' => '60 min',
                'episode_count' => 24,
                'is_featured' => true,
                'is_trending' => false,
                'category_id' => 5,
                'episodes' => [
                    ['title' => 'The Prophecy', 'episode_number' => 1],
                    ['title' => 'Into the Palace', 'episode_number' => 2],
                    ['title' => 'Silk and Shadows', 'episode_number' => 3],
                    ['title' => 'The Coronation', 'episode_number' => 4],
                    ['title' => 'Storm Clouds', 'episode_number' => 5],
                ],
            ],
            [
                'title' => 'Laugh Factory',
                'description' => 'Follow the hilarious misadventures of five friends running a struggling comedy club. Their dreams of stardom lead to side-splitting situations and touching moments.',
                'rating' => 8.5,
                'year' => 2024,
                'duration' => '30 min',
                'episode_count' => 20,
                'is_featured' => false,
                'is_trending' => true,
                'category_id' => 4,
                'episodes' => [
                    ['title' => 'Open Mic Night', 'episode_number' => 1],
                    ['title' => 'The Heckler', 'episode_number' => 2],
                    ['title' => 'Famous Last Words', 'episode_number' => 3],
                    ['title' => 'Road Trip', 'episode_number' => 4],
                    ['title' => 'The Big Break', 'episode_number' => 5],
                ],
            ],
            [
                'title' => 'Whispers in the Dark',
                'description' => 'A detective investigates a series of mysterious disappearances in a small town where everyone has secrets. Each clue leads deeper into a web of lies.',
                'rating' => 9.0,
                'year' => 2024,
                'duration' => '45 min',
                'episode_count' => 10,
                'is_featured' => false,
                'is_trending' => true,
                'category_id' => 7,
                'episodes' => [
                    ['title' => 'The Missing', 'episode_number' => 1],
                    ['title' => 'Footprints', 'episode_number' => 2],
                    ['title' => 'The Letter', 'episode_number' => 3],
                    ['title' => 'Suspects', 'episode_number' => 4],
                    ['title' => 'Revelations', 'episode_number' => 5],
                ],
            ],
            [
                'title' => 'Dragon Realms',
                'description' => 'In a world where dragons and humans coexist, a young orphan discovers she has the power to communicate with the ancient creatures. An epic fantasy adventure.',
                'rating' => 9.3,
                'year' => 2024,
                'duration' => '55 min',
                'episode_count' => 18,
                'is_featured' => true,
                'is_trending' => false,
                'category_id' => 6,
                'episodes' => [
                    ['title' => 'The Egg', 'episode_number' => 1],
                    ['title' => 'First Flight', 'episode_number' => 2],
                    ['title' => 'The Elders', 'episode_number' => 3],
                    ['title' => 'Forbidden Lands', 'episode_number' => 4],
                    ['title' => 'The Bond', 'episode_number' => 5],
                ],
            ],
            [
                'title' => 'Campus Confidential',
                'description' => 'Life at Maplewood High School gets complicated when a anonymous blog starts revealing everyone\'s secrets. A coming-of-age story about friendship and identity.',
                'rating' => 8.7,
                'year' => 2024,
                'duration' => '35 min',
                'episode_count' => 15,
                'is_featured' => false,
                'is_trending' => true,
                'category_id' => 8,
                'episodes' => [
                    ['title' => 'First Day', 'episode_number' => 1],
                    ['title' => 'The Blog', 'episode_number' => 2],
                    ['title' => 'Secrets Revealed', 'episode_number' => 3],
                    ['title' => 'Fallout', 'episode_number' => 4],
                    ['title' => 'Standing Tall', 'episode_number' => 5],
                ],
            ],
            [
                'title' => 'Midnight Chase',
                'description' => 'A cat-and-mouse thriller following a master thief and the relentless detective on her trail. Every episode is a pulse-pounding race against time.',
                'rating' => 8.9,
                'year' => 2023,
                'duration' => '45 min',
                'episode_count' => 8,
                'is_featured' => false,
                'is_trending' => false,
                'category_id' => 3,
                'episodes' => [
                    ['title' => 'The Heist', 'episode_number' => 1],
                    ['title' => 'Getaway', 'episode_number' => 2],
                    ['title' => 'Safe House', 'episode_number' => 3],
                    ['title' => 'The Double Cross', 'episode_number' => 4],
                    ['title' => 'Final Showdown', 'episode_number' => 5],
                ],
            ],
        ];

        foreach ($dramas as $index => $dramaData) {
            $episodes = $dramaData['episodes'];
            unset($dramaData['episodes']);

            $drama = Drama::create(array_merge($dramaData, [
                'poster_url' => $posterUrls[$index],
                'banner_url' => $bannerUrls[$index],
                'trailer_url' => $trailerUrls[$index % count($trailerUrls)],
                'latest_episode' => count($episodes),
            ]));

            foreach ($episodes as $epIdx => $ep) {
                Episode::create([
                    'drama_id' => $drama->id,
                    'title' => $ep['title'],
                    'episode_number' => $ep['episode_number'],
                    'video_url' => $videoUrls[($index + $epIdx) % count($videoUrls)],
                    'thumbnail_url' => $thumbnailUrls[$index],
                    'duration' => $dramaData['duration'],
                    'description' => "Episode {$ep['episode_number']}: {$ep['title']}",
                ]);
            }
        }
    }
}