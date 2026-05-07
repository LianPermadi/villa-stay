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
            "name" => "Admin VilaStay",
            "email" => "admin@vila-stay.com",
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
                "status" => "confirmed",
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
                "status" => "completed",
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

        $revenues = DB::table("revenues")
            ->select(DB::raw("SUM(amount) as total, period"))
            ->groupBy("period")
            ->orderBy("period")
            ->get();

        $revenueData = [];
        foreach ($revenues as $revenue) {
            $revenueData[] = [
                "period" => $revenue->period,
                "amount" => $revenue->total,
            ];
        }

        for ($i = 2; $i < count($revenueData); $i++) {
            $sum = $revenueData[$i]["amount"] + $revenueData[$i-1]["amount"] + $revenueData[$i-2]["amount"];
            $average = $sum / 3;

            DB::table("moving_average_results")->insert([
                "period" => $revenueData[$i]["period"],
                "actual_revenue" => $revenueData[$i]["amount"],
                "predicted_revenue" => $average,
                "months_used" => 3,
                "calculation_data" => json_encode([
                    "month_1" => $revenueData[$i-2]["period"],
                    "amount_1" => $revenueData[$i-2]["amount"],
                    "month_2" => $revenueData[$i-1]["period"],
                    "amount_2" => $revenueData[$i-1]["amount"],
                    "month_3" => $revenueData[$i]["period"],
                    "amount_3" => $revenueData[$i]["amount"],
                ]),
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
    }
}
