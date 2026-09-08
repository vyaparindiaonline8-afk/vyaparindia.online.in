<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerPage;
use App\Models\SellerProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $sellerRole = Role::where('name', 'seller')->first();
        $buyerRole = Role::where('name', 'buyer')->first();
        $adminRole = Role::where('name', 'admin')->first();

        // Create Admin
        User::firstOrCreate(['email' => 'admin@vyaparindia.com'], [
            'name' => 'Admin VyaparIndia',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // Create Sample Seller
        $seller = User::firstOrCreate(['email' => 'seller@example.com'], [
            'name' => 'Rajesh Textiles & Handlooms',
            'password' => Hash::make('password'),
            'role_id' => $sellerRole->id,
        ]);

        // Create Seller Profile
        SellerProfile::updateOrCreate(['user_id' => $seller->id], [
            'company_name' => 'Rajesh Handlooms Ltd.',
            'phone_number' => '9876543210',
            'address' => 'Shop 42, Johari Bazaar',
            'city' => 'Jaipur',
            'state' => 'Rajasthan',
            'country' => 'India',
            'gst_number' => '08AAAAA0000A1Z5',
            'dispatch_radius' => 500,
        ]);

        // Create Sample Mini-Site
        SellerPage::updateOrCreate(['user_id' => $seller->id], [
            'page_title' => 'Rajesh Handlooms & Silk',
            'slug' => 'rajesh-handlooms',
            'tagline' => 'Direct Manufacturers of Authentic Handcrafted Sarees & Fabrics',
            'welcome_message' => 'Explore 100% pure silk and cotton handcrafted fabrics directly from Jaipur weavers at factory wholesale rates.',
            'about_text' => 'Rajesh Handlooms has been weaving heritage Rajasthani textiles for over 25 years. We supply direct to consumers and retail shops pan-India with assured quality check and fast dispatch.',
            'whatsapp_number' => '919876543210',
            'support_phone' => '0141-2567890',
            'support_email' => 'contact@rajeshhandlooms.com',
            'address' => 'Plot 45, Textile Industrial Park, Sitapura',
            'city' => 'Jaipur',
            'pincode' => '302022',
            'theme_color' => '#0f766e',
            'theme_style' => 'modern',
            'currency' => 'INR',
            'enable_cod' => true,
            'enable_whatsapp_order' => true,
            'policies' => "1. All orders dispatched within 24 hours via Express Courier.\n2. 7-Day replacement for damaged/defective items with unboxing video.\n3. Cash on Delivery available across all serviceable pincodes in India.",
        ]);

        // Create Categories
        $cat1 = Category::firstOrCreate(['name' => 'Silk Sarees', 'slug' => 'silk-sarees']);
        $cat2 = Category::firstOrCreate(['name' => 'Cotton Kurtas', 'slug' => 'cotton-kurtas']);
        $cat3 = Category::firstOrCreate(['name' => 'Handcrafted Dupattas', 'slug' => 'handcrafted-dupattas']);

        // Create Sample Products
        $products = [
            [
                'name' => 'Pure Banarasi Zari Silk Saree - Crimson Red',
                'slug' => 'pure-banarasi-zari-silk-saree-crimson-red',
                'description' => "Exquisite handcrafted pure Banarasi silk saree with authentic antique gold zari weaving.\nIncludes unstitched matching blouse piece.\nFabric: 100% Pure Katan Silk.\nOccasion: Wedding, Festive.",
                'price' => 2499.00,
                'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=600&auto=format&fit=crop&q=80',
                'category_id' => $cat1->id,
            ],
            [
                'name' => 'Jaipuri Sanganeri Print Pure Cotton Kurta',
                'slug' => 'jaipuri-sanganeri-print-pure-cotton-kurta',
                'description' => "Breathable premium 60x60 cotton kurta crafted with traditional wooden block print techniques.\nComfortable all-day wear.",
                'price' => 799.00,
                'image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=600&auto=format&fit=crop&q=80',
                'category_id' => $cat2->id,
            ],
            [
                'name' => 'Chanderi Handloom Floral Dupatta with Zari Border',
                'slug' => 'chanderi-handloom-floral-dupatta-with-zari-border',
                'description' => "Lightweight royal Chanderi silk cotton dupatta featuring intricate hand-block floral motifs and delicate woven zari borders.",
                'price' => 649.00,
                'image' => 'https://images.unsplash.com/photo-1609357605129-26f69add5d6e?w=600&auto=format&fit=crop&q=80',
                'category_id' => $cat3->id,
            ],
            [
                'name' => 'Royal Tussar Silk Saree - Mustard Gold',
                'slug' => 'royal-tussar-silk-saree-mustard-gold',
                'description' => "Handwoven textured Tussar silk saree with rich temple border and contrast pallu.\nSilk Mark certified authentic weave.",
                'price' => 1999.00,
                'image' => 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?w=600&auto=format&fit=crop&q=80',
                'category_id' => $cat1->id,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['slug' => $p['slug']], array_merge($p, ['user_id' => $seller->id]));
        }
    }
}