<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("users")->insert([
            "name" => "Admin Villa-Sina",
            "email" => "admin@villa-sina.com",
            "password" => Hash::make("password"),
            "phone" => "081234567890",
            "address" => "Komplek Vila Paradise",
            "role" => "admin",
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        DB::table("users")->insert([
            "name" => "John Doe",
            "email" => "john@example.com",
            "password" => Hash::make("password"),
            "phone" => "081234567891",
            "address" => "Jl. Merdeka No. 1",
            "role" => "user",
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        $villas = [
            [
                "name" => "Villa Ocean View",
                "description" => "Villa mewah dengan pemandangan laut yang menakjubkan. Dilengkapi dengan kolam renang pribadi dan taman yang luas. Cocok untuk liburan keluarga atau rombongan teman.",
                "price_per_night" => 2500000,
                "capacity" => 8,
                "bedrooms" => 4,
                "bathrooms" => 3,
                "area" => 350.50,
                "status" => "available",
                "is_featured" => true,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Villa Mountain Retreat",
                "description" => "Villa nyaman di kaki gunung dengan udara segar. Dilengkapi dengan perapian dan ruang tamu yang hangat. Tempat ideal untuk bersantai dan melepas penat.",
                "price_per_night" => 1800000,
                "capacity" => 6,
                "bedrooms" => 3,
                "bathrooms" => 2,
                "area" => 280.00,
                "status" => "available",
                "is_featured" => true,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Villa Garden Paradise",
                "description" => "Villa elegan dengan taman bunga yang indah. Arsitektur modern dengan sentuhan tradisional. Cocok untuk pasangan yang ingin menikmati suasana romantis.",
                "price_per_night" => 1500000,
                "capacity" => 4,
                "bedrooms" => 2,
                "bathrooms" => 2,
                "area" => 200.00,
                "status" => "available",
                "is_featured" => false,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Villa Sunset Beach",
                "description" => "Villa eksklusif di pinggir pantai dengan pemandangan matahari terbenam yang spektakuler. Fasilitas lengkap dengan akses langsung ke pantai.",
                "price_per_night" => 3200000,
                "capacity" => 10,
                "bedrooms" => 5,
                "bathrooms" => 4,
                "area" => 450.00,
                "status" => "available",
                "is_featured" => true,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Villa Green Valley",
                "description" => "Villa tenang di lembah hijau yang asri. Dikelilingi oleh pepohonan dan sungai kecil. Tempat sempurna untuk yang mencari ketenangan jauh dari hiruk pikuk kota.",
                "price_per_night" => 1200000,
                "capacity" => 4,
                "bedrooms" => 2,
                "bathrooms" => 1,
                "area" => 180.00,
                "status" => "unavailable",
                "is_featured" => false,
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ];

        foreach ($villas as $villa) {
            $villaId = DB::table("villas")->insertGetId($villa);
            
            $images = [
                [
                    "villa_id" => $villaId,
                    "image_path" => "villas/villa-" . $villaId . "-1.jpg",
                    "is_primary" => true,
                    "sort_order" => 1,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "villa_id" => $villaId,
                    "image_path" => "villas/villa-" . $villaId . "-2.jpg",
                    "is_primary" => false,
                    "sort_order" => 2,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "villa_id" => $villaId,
                    "image_path" => "villas/villa-" . $villaId . "-3.jpg",
                    "is_primary" => false,
                    "sort_order" => 3,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
            ];
            DB::table("villa_images")->insert($images);
        }

        $bookings = [
            [
                "user_id" => 2,
                "villa_id" => 1,
                "check_in" => "2026-06-15",
                "check_out" => "2026-06-18",
                "num_nights" => 3,
                "num_guests" => 6,
                "total_price" => 7500000,
                "guest_name" => "John Doe",
                "guest_email" => "john@example.com",
                "guest_phone" => "081234567891",
                "special_requests" => "Mohon disediakan extra bed",
                "status" => "pending",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "user_id" => 2,
                "villa_id" => 2,
                "check_in" => "2026-07-10",
                "check_out" => "2026-07-13",
                "num_nights" => 3,
                "num_guests" => 4,
                "total_price" => 5400000,
                "guest_name" => "John Doe",
                "guest_email" => "john@example.com",
                "guest_phone" => "081234567891",
                "special_requests" => null,
                "status" => "pending",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "user_id" => 2,
                "villa_id" => 3,
                "check_in" => "2026-05-20",
                "check_out" => "2026-05-22",
                "num_nights" => 2,
                "num_guests" => 2,
                "total_price" => 3000000,
                "guest_name" => "John Doe",
                "guest_email" => "john@example.com",
                "guest_phone" => "081234567891",
                "special_requests" => "Kamar dengan pemandangan taman",
                "status" => "pending",
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ];

        foreach ($bookings as $booking) {
            $bookingId = DB::table("bookings")->insertGetId($booking);
            
            DB::table("revenues")->insert([
                "booking_id" => $bookingId,
                "amount" => $booking["total_price"],
                "revenue_date" => $booking["check_in"],
                "period" => date("Y-m", strtotime($booking["check_in"])),
                "created_at" => now(),
                "updated_at" => now(),
            ]);

            DB::table("payments")->insert([
                "booking_id" => $bookingId,
                "amount" => $booking["total_price"],
                "payment_method" => "bank_transfer",
                "transaction_id" => "TRX" . str_pad($bookingId, 6, "0", STR_PAD_LEFT),
                "proof_image" => null,
                "status" => $booking["status"] === "completed" ? "verified" : "pending",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        $faker = \Faker\Factory::create('id_ID');

        // Generate 15 extra random villas
        for ($i = 6; $i <= 20; $i++) {
            $price = $faker->numberBetween(5, 50) * 100000;
            $villaId = DB::table('villas')->insertGetId([
                'name' => 'Villa ' . ucfirst($faker->words(2, true)),
                'description' => $faker->paragraph(),
                'price_per_night' => $price,
                'capacity' => $faker->numberBetween(2, 12),
                'bedrooms' => $faker->numberBetween(1, 6),
                'bathrooms' => $faker->numberBetween(1, 4),
                'area' => $faker->randomFloat(2, 50, 600),
                'status' => $faker->boolean(80) ? 'available' : 'unavailable',
                'is_featured' => $faker->boolean(20),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Copy images from dummy
            for ($j = 1; $j <= 3; $j++) {
                DB::table('villa_images')->insert([
                    'villa_id' => $villaId,
                    'image_path' => 'villas/villa-' . rand(1, 5) . '-' . $j . '.jpg',
                    'is_primary' => $j === 1,
                    'sort_order' => $j,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Generate 150 random bookings over the last 12 months
        for ($i = 0; $i < 150; $i++) {
            $villaId = $faker->numberBetween(1, 20);
            $villaPrice = DB::table('villas')->where('id', $villaId)->value('price_per_night');
            $checkIn = $faker->dateTimeBetween('-12 months', '+1 months')->format('Y-m-d');
            $nights = $faker->numberBetween(1, 5);
            $checkOut = date('Y-m-d', strtotime($checkIn . ' + ' . $nights . ' days'));
            
            $status = 'pending';

            $bookingId = DB::table('bookings')->insertGetId([
                'user_id' => 2,
                'villa_id' => $villaId,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'num_nights' => $nights,
                'num_guests' => $faker->numberBetween(2, 6),
                'total_price' => $villaPrice * $nights,
                'guest_name' => $faker->name,
                'guest_email' => $faker->email,
                'guest_phone' => $faker->phoneNumber,
                'special_requests' => $faker->boolean(30) ? $faker->sentence : null,
                'status' => $status,
                'created_at' => $checkIn,
                'updated_at' => $checkIn,
            ]);

            if ($status !== 'cancelled') {
                DB::table('revenues')->insert([
                    'booking_id' => $bookingId,
                    'amount' => $villaPrice * $nights,
                    'revenue_date' => $checkIn,
                    'period' => date('Y-m', strtotime($checkIn)),
                    'created_at' => $checkIn,
                    'updated_at' => $checkIn,
                ]);

                DB::table('payments')->insert([
                    'booking_id' => $bookingId,
                    'amount' => $villaPrice * $nights,
                    'payment_method' => 'bank_transfer',
                    'transaction_id' => 'TRX' . str_pad($bookingId, 6, '0', STR_PAD_LEFT),
                    'proof_image' => null,
                    'status' => in_array($status, ['completed', 'confirmed']) ? 'verified' : 'pending',
                    'created_at' => $checkIn,
                    'updated_at' => $checkIn,
                ]);
            }
        }
    }
}
