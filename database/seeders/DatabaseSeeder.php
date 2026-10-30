<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@demo.test'],
            [
                'name' => 'Sarah Jenkins',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $marcus = User::firstOrCreate(
            ['email' => 'marcus.v@cloudcorp.io'],
            ['name' => 'Marcus Vance', 'password' => Hash::make('password')]
        );

        $elena = User::firstOrCreate(
            ['email' => 'elena.r@devguild.org'],
            ['name' => 'Elena Rostova', 'password' => Hash::make('password')]
        );

        $kevin = User::firstOrCreate(
            ['email' => 'kevin.a@innovate.id'],
            ['name' => 'Kevin Alamsyah', 'password' => Hash::make('password')]
        );

        $nadia = User::firstOrCreate(
            ['email' => 'nadia.w@robotics.tech'],
            ['name' => 'Nadia Wijaya', 'password' => Hash::make('password')]
        );

        $david = User::firstOrCreate(
            ['email' => 'david.chen@fintech.co'],
            ['name' => 'David Chen', 'password' => Hash::make('password')]
        );

        $rachel = User::firstOrCreate(
            ['email' => 'rachel.k@ai-labs.org'],
            ['name' => 'Rachel Kim', 'password' => Hash::make('password')]
        );

        // 1. Flagship Event organized by Sarah Jenkins (Admin)
        $eventSummit = Event::firstOrCreate(
            ['title' => 'Global Cloud Native & DevOps Summit 2026'],
            [
                'organizer_id' => $admin->id,
                'description' => "Annual flagship summit gathering cloud architects, platform engineers, and DevOps practitioners. Featuring deep-dive keynote sessions on Kubernetes multi-tenancy, eBPF telemetry, service mesh observability, and automated GitOps resilience patterns.\n\nIncludes hands-on breakout rooms, architecture review clinics, and an executive networking lounge.",
                'venue' => 'Grand Ballroom, Indonesia Convention Exhibition (ICE BSD)',
                'starts_at' => Carbon::now()->addDays(5)->setTime(9, 0),
                'ends_at' => Carbon::now()->addDays(5)->setTime(17, 30),
                'capacity' => 150,
                'status' => 'published',
            ]
        );

        foreach ([$marcus, $elena, $kevin, $nadia, $david, $rachel] as $attendee) {
            $eventSummit->registrations()->firstOrCreate(
                ['user_id' => $attendee->id],
                ['registered_at' => Carbon::now()->subDays(rand(1, 4))]
            );
        }

        // 2. Meetup organized by Elena Rostova
        $eventMeetup = Event::firstOrCreate(
            ['title' => 'Jakarta Laravel & Modern PHP Meetup #42'],
            [
                'organizer_id' => $elena->id,
                'description' => "Monthly hands-on community meetup exploring modern PHP 8.3+ features, asynchronous worker architectures, SQLite in high-throughput production, and elegant Tailwind CSS component design.\n\nInteractive Q&A followed by live coding demonstrations and lightning talks from local tech leads.",
                'venue' => 'Cohive 101, Mega Kuningan, Jakarta Selatan',
                'starts_at' => Carbon::now()->addDays(2)->setTime(18, 30),
                'ends_at' => Carbon::now()->addDays(2)->setTime(21, 0),
                'capacity' => 60,
                'status' => 'published',
            ]
        );

        foreach ([$admin, $kevin, $david] as $attendee) {
            $eventMeetup->registrations()->firstOrCreate(
                ['user_id' => $attendee->id],
                ['registered_at' => Carbon::now()->subDays(2)]
            );
        }

        // 3. Workshop organized by Marcus Vance
        $eventWorkshop = Event::firstOrCreate(
            ['title' => 'AI & Large Language Model Architecture Workshop'],
            [
                'organizer_id' => $marcus->id,
                'description' => "Intensive technical deep-dive into LLM inference optimization, retrieval-augmented generation (RAG) pipelines, semantic vector search, and agentic tool use frameworks for production enterprise scale.\n\nParticipants will build and benchmark an autonomous reasoning pipeline using open APIs.",
                'venue' => 'Auditorium Lt. 4, Menara Multimedia, Jakarta Pusat',
                'starts_at' => Carbon::now()->addDays(7)->setTime(10, 0),
                'ends_at' => Carbon::now()->addDays(7)->setTime(16, 0),
                'capacity' => 30,
                'status' => 'published',
            ]
        );

        foreach ([$admin, $elena, $nadia] as $attendee) {
            $eventWorkshop->registrations()->firstOrCreate(
                ['user_id' => $attendee->id],
                ['registered_at' => Carbon::now()->subDay()]
            );
        }

        // 4. Cybersecurity Symposium organized by Kevin Alamsyah
        $eventSecurity = Event::firstOrCreate(
            ['title' => 'NextGen Cyber Security & Zero-Trust Symposium'],
            [
                'organizer_id' => $kevin->id,
                'description' => "Exploring defense-in-depth strategies, identity access management, secure software supply chain audits, container isolation, and automated vulnerability posture management across hybrid clouds.",
                'venue' => 'The Ritz-Carlton Pacific Place, SCBD Jakarta',
                'starts_at' => Carbon::now()->addDays(12)->setTime(8, 30),
                'ends_at' => Carbon::now()->addDays(12)->setTime(16, 30),
                'capacity' => 120,
                'status' => 'published',
            ]
        );

        foreach ([$marcus, $nadia, $david, $rachel] as $attendee) {
            $eventSecurity->registrations()->firstOrCreate(
                ['user_id' => $attendee->id],
                ['registered_at' => Carbon::now()->subHours(12)]
            );
        }
    }
}
