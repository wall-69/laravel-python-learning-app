<?php

namespace Database\Seeders;

use App\Enums\LectureStatus;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Lecture;
use App\Models\User;
use App\Models\UserProgress;
use DB;
use Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin John Doe user
        $adminUser = User::factory()->create([
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john@doe.com",
            "password" => Hash::make("doedoe")
        ]);

        UserProgress::create([
            "user_id" => $adminUser->id
        ]);

        Admin::create([
            "user_id" => $adminUser->id
        ]);

        // Categories
        $categories = collect([
            [
                "title" => "Python základy",
                "description" => "Úvod do programovacieho jazyka Python, dátové typy, premenné, podmienky, cykly a základné princípy programovania.",
            ],
            [
                "title" => "Python tkinter",
                "description" => "Základy tvorby grafických desktopových aplikácií v Pythone pomocou knižnice Tkinter - okná, tlačidlá, vstupy a jednoduché interaktívne prvky."
            ]
        ]);

        $categories->each(fn($data) => Category::factory()->create($data));

        // Lectures
        $lectures = collect([
            [
                "category_id" => Category::where("title", "Python základy")->value("id"),
                "category_order" => 1,
                "title" => "Čo je to Python a prečo sa ho všetci učia?",
                "description" => "V tejto lekcií si priblížime, čo ten Python vôbec je (okrem toho, že je to je programovací jazyk) a pre aké dôvody sa ho všetci učia a používajú ho.",
                "status" => LectureStatus::PUBLIC->value,
                "blocks" => '{"time":1756563367397,"blocks":[{"id":"KegvIaJRcc","type":"paragraph","data":{"text":"TODO"}}],"version":"2.31.0-rc.7"}',
            ],
            [
                "category_id" => Category::where("title", "Python základy")->value("id"),
                "category_order" => 2,
                "title" => "Základy Pythonu",
                "description" => "Dátové typy, premenné a všetko o nich!",
                "status" => LectureStatus::PUBLIC->value,
                "blocks" => '{"time":1756563554045,"blocks":[{"id":"cg8_iQiBHg","type":"paragraph","data":{"text":"TODO"}}],"version":"2.31.0-rc.7"}',
            ],
            [
                "category_id" => Category::where("title", "Python tkinter")->value("id"),
                "category_order" => 1,
                "title" => "Základy tkinteru",
                "description" => "Zoznámime sa s Python knižnicou tkinter, ktorá je využívaná na tvorbu desktopových rozhraní.",
                "status" => LectureStatus::PUBLIC->value,
                "blocks" => '{"time":1756566789394,"blocks":[{"id":"XsXsW9TjJm","type":"paragraph","data":{"text":"TODO"}}],"version":"2.31.0-rc.7"}',
            ],
        ]);

        $lectures->each(fn($data) => Lecture::factory()->create($data));
    }
}
