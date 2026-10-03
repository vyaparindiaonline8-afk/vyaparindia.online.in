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
        if (Category::count() >= 5) {
            return;
        }

        $sellerRole = Role::firstOrCreate(['name' => 'seller']);
        $buyerRole = Role::firstOrCreate(['name' => 'buyer']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // 1. Admin Account
        User::firstOrCreate(['email' => 'admin@vyaparindia.com'], [
            'name' => 'Admin VyaparIndia',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // 2. Buyer Account
        User::firstOrCreate(['email' => 'buyer@example.com'], [
            'name' => 'Vikram Trading Co.',
            'password' => Hash::make('password'),
            'role_id' => $buyerRole->id,
        ]);

        // 3. Categories
        $categories = [
            'Building & Construction' => 'building-construction',
            'Hardware & Industrial Tools' => 'hardware-tools',
            'Electricals & Solar Panels' => 'electricals-solar',
            'Textiles, Fabrics & Sarees' => 'textiles-fabrics',
            'Ceramics, Tiles & Sanitary' => 'ceramics-sanitary',
            'Agriculture, Seeds & Spices' => 'agriculture-spices',
            'Packaging & Corrugated Boxes' => 'packaging-boxes',
            'Chemicals & Industrial Oils' => 'chemicals-oils',
        ];

        $catModels = [];
        foreach ($categories as $catName => $catSlug) {
            $catModels[$catName] = Category::firstOrCreate(['slug' => $catSlug], ['name' => $catName]);
        }

        // 4. Sample B2B Verified Sellers
        $sellersData = [
            [
                'email' => 'rajesh.handlooms@example.com',
                'name' => 'Rajesh Handlooms & Silk Mill',
                'company' => 'Rajesh Handlooms Ltd.',
                'city' => 'Jaipur',
                'state' => 'Rajasthan',
                'gst' => '08AAAAA1234A1Z5',
                'phone' => '9829012345',
                'slug' => 'rajesh-handlooms',
                'tagline' => 'Direct Manufacturers of Authentic Handcrafted Sarees & Kurtas',
                'welcome' => 'Welcome to Jaipur’s premier wholesale textile hub. Factory direct rates for retailers across India.',
                'products' => [
                    [
                        'name' => 'Pure Banarasi Zari Silk Saree - Royal Crimson',
                        'slug' => 'pure-banarasi-zari-silk-saree-royal-crimson',
                        'desc' => "Exquisite handcrafted pure Banarasi silk saree with authentic antique gold zari weaving. Includes unstitched matching blouse.\nMin Order: 10 Pieces\nFabric: 100% Pure Katan Silk",
                        'price' => 2499.00,
                        'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=600&auto=format&fit=crop&q=80',
                        'category' => 'Textiles, Fabrics & Sarees',
                    ],
                    [
                        'name' => 'Jaipuri Hand-Block Print Pure Cotton Fabric (100m Roll)',
                        'slug' => 'jaipuri-hand-block-print-pure-cotton-fabric-100m-roll',
                        'desc' => "Traditional natural vegetable dye hand-block print fabric on 60x60 cambric cotton. Ideal for boutiques and dress manufacturers.\nRoll Length: 100 Meters\nWidth: 44 inches",
                        'price' => 14500.00,
                        'image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=600&auto=format&fit=crop&q=80',
                        'category' => 'Textiles, Fabrics & Sarees',
                    ],
                ]
            ],
            [
                'email' => 'shivshakti.hardware@example.com',
                'name' => 'Shiv Shakti Industrial Tools & Valves',
                'company' => 'Shiv Shakti Brass & Hardware Works',
                'city' => 'Rajkot',
                'state' => 'Gujarat',
                'gst' => '24BBBBB5678B1Z2',
                'phone' => '9879554321',
                'slug' => 'shiv-shakti-hardware',
                'tagline' => 'Precision Brass Components, Valves & Heavy Industrial Power Tools',
                'welcome' => 'Rajkot GIDC certified brass components and industrial hardware manufacturer. Supplying over 500 retail hardware stores pan-India.',
                'products' => [
                    [
                        'name' => 'Heavy Duty Brass Ball Valve 1-Inch (Pack of 50)',
                        'slug' => 'heavy-duty-brass-ball-valve-1-inch-pack-of-50',
                        'desc' => "Forged brass body, chrome plated ball valve with Teflon seal. Rated up to 40 Bar pressure.\nApplication: Plumbing, Agriculture, Oil pipelines\nMOQ: 1 Box (50 pcs)",
                        'price' => 8750.00,
                        'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600&auto=format&fit=crop&q=80',
                        'category' => 'Hardware & Industrial Tools',
                    ],
                    [
                        'name' => 'Industrial Angle Grinder 850W Heavy Motor',
                        'slug' => 'industrial-angle-grinder-850w-heavy-motor',
                        'desc' => "Copper armature angle grinder for metal fabrication and stone cutting. 11,000 RPM with anti-vibration auxiliary handle.\nWarranty: 1 Year Manufacturer Warranty",
                        'price' => 1850.00,
                        'image' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?w=600&auto=format&fit=crop&q=80',
                        'category' => 'Hardware & Industrial Tools',
                    ],
                ]
            ],
            [
                'email' => 'apex.electricals@example.com',
                'name' => 'Apex Solar & Industrial Switchgear',
                'company' => 'Apex Energy Innovations Pvt Ltd',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'gst' => '07CCCCC9012C1Z9',
                'phone' => '9811223344',
                'slug' => 'apex-solar-delhi',
                'tagline' => 'Mono-Perc Solar Panels, Inverters & Industrial LED Solutions',
                'welcome' => 'Direct tier-1 solar panel distributor and industrial LED manufacturer with ready inventory for commercial contractors.',
                'products' => [
                    [
                        'name' => '540W Mono-PERC Bifacial High Efficiency Solar Panel',
                        'slug' => '540w-mono-perc-bifacial-high-efficiency-solar-panel',
                        'desc' => "A-grade certified bifacial solar panel with 21.4% module efficiency. 25-Year linear power warranty. BIS approved.\nPallet packing: 31 modules",
                        'price' => 11800.00,
                        'image' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?w=600&auto=format&fit=crop&q=80',
                        'category' => 'Electricals & Solar Panels',
                    ],
                    [
                        'name' => '150W IP66 Waterproof Outdoor Industrial LED Flood Light',
                        'slug' => '150w-ip66-waterproof-outdoor-industrial-led-flood-light',
                        'desc' => "Die-cast aluminum housing, surge protection up to 6kV, 150 Lumens/Watt. Perfect for warehouses, factories, and sports yards.",
                        'price' => 2200.00,
                        'image' => 'https://images.unsplash.com/photo-1517524008697-84bbe3c3fd98?w=600&auto=format&fit=crop&q=80',
                        'category' => 'Electricals & Solar Panels',
                    ],
                ]
            ],
            [
                'email' => 'mahalaxmi.ceramics@example.com',
                'name' => 'Mahalaxmi Ceramics & Sanitaryware',
                'company' => 'Mahalaxmi Tiles & Faucets LLP',
                'city' => 'Morbi',
                'state' => 'Gujarat',
                'gst' => '24DDDDD3456D1Z1',
                'phone' => '9825123456',
                'slug' => 'mahalaxmi-ceramics',
                'tagline' => 'Vitrified Floor Tiles, Wall Panels & Designer Sanitaryware from Morbi',
                'welcome' => 'Morbi’s trusted tile manufacturer supplying full truckloads (FTL) and container shipments across India at factory prices.',
                'products' => [
                    [
                        'name' => '600x1200mm Glossy Finish Glazed Vitrified Floor Tiles (Box of 2 Pcs)',
                        'slug' => '600x1200mm-glossy-finish-glazed-vitrified-floor-tiles',
                        'desc' => "Italian marble design vitrified tiles with nano-polish scratch resistant surface. Water absorption < 0.05%.\nCoverage: 15.5 Sq. Ft. per box\nMin Order: 50 Boxes",
                        'price' => 840.00,
                        'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&auto=format&fit=crop&q=80',
                        'category' => 'Ceramics, Tiles & Sanitary',
                    ],
                    [
                        'name' => 'One-Piece Ceramic Western Toilet Commode (Soft Close)',
                        'slug' => 'one-piece-ceramic-western-toilet-commode-soft-close',
                        'desc' => "Dual flush siphon jet one-piece commode with anti-bacterial glaze and soft closing hydraulic seat cover.\nS-Trap: 300mm",
                        'price' => 4500.00,
                        'image' => 'https://images.unsplash.com/photo-1584622781564-1d987f7333c1?w=600&auto=format&fit=crop&q=80',
                        'category' => 'Ceramics, Tiles & Sanitary',
                    ],
                ]
            ],
            [
                'email' => 'kisan.agro@example.com',
                'name' => 'Kisan Agro Seeds & Natural Spices',
                'company' => 'Kisan Agro Exports Pvt Ltd',
                'city' => 'Indore',
                'state' => 'Madhya Pradesh',
                'gst' => '23EEEEE7890E1Z4',
                'phone' => '9893011223',
                'slug' => 'kisan-agro-indore',
                'tagline' => 'Wholesale Spices, Certified Hybrid Seeds & Organic Pulses',
                'welcome' => 'Direct mandi aggregation and processing plant in Indore. Bulk supply for grocery wholesalers and supermarkets.',
                'products' => [
                    [
                        'name' => 'Premium Malwa Sharbati Wheat - 50 Kg Jute Bag',
                        'slug' => 'premium-malwa-sharbati-wheat-50-kg-jute-bag',
                        'desc' => "Golden luster grain Sharbati gehun sourced directly from Sehore farmers. High protein content, machine cleaned and sortex graded.\nNet Weight: 50 Kg",
                        'price' => 2450.00,
                        'image' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=600&auto=format&fit=crop&q=80',
                        'category' => 'Agriculture, Seeds & Spices',
                    ],
                    [
                        'name' => 'Whole Salem Turmeric Fingers (Haldi) - 25 Kg Bag',
                        'slug' => 'whole-salem-turmeric-fingers-haldi-25-kg-bag',
                        'desc' => "Natural polished turmeric fingers with curcumin level > 3.5%. Sun-dried, unadulterated spice ready for grinding and export.",
                        'price' => 3800.00,
                        'image' => 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=600&auto=format&fit=crop&q=80',
                        'category' => 'Agriculture, Seeds & Spices',
                    ],
                ]
            ]
        ];

        foreach ($sellersData as $sData) {
            $user = User::firstOrCreate(['email' => $sData['email']], [
                'name' => $sData['name'],
                'password' => Hash::make('password'),
                'role_id' => $sellerRole->id,
            ]);

            SellerProfile::updateOrCreate(['user_id' => $user->id], [
                'company_name' => $sData['company'],
                'phone_number' => $sData['phone'],
                'address' => 'GIDC Industrial Area',
                'city' => $sData['city'],
                'state' => $sData['state'],
                'country' => 'India',
                'gst_number' => $sData['gst'],
                'dispatch_radius' => 1000,
            ]);

            SellerPage::updateOrCreate(['user_id' => $user->id], [
                'page_title' => $sData['name'],
                'slug' => $sData['slug'],
                'tagline' => $sData['tagline'],
                'welcome_message' => $sData['welcome'],
                'about_text' => $sData['welcome'],
                'whatsapp_number' => '91' . $sData['phone'],
                'support_phone' => $sData['phone'],
                'support_email' => $sData['email'],
                'city' => $sData['city'],
                'theme_color' => '#1e40af',
                'theme_style' => 'modern',
                'currency' => 'INR',
                'enable_cod' => true,
                'enable_whatsapp_order' => true,
                'policies' => "1. Direct Factory Dispatch via Safe Express Logistics.\n2. GST Tax Invoice issued with every order.\n3. Verified Supplier Guarantee on VyaparIndia.",
            ]);

            foreach ($sData['products'] as $p) {
                $cat = $catModels[$p['category']] ?? Category::first();
                Product::updateOrCreate(['slug' => $p['slug']], [
                    'name' => $p['name'],
                    'slug' => $p['slug'],
                    'description' => $p['desc'],
                    'price' => $p['price'],
                    'image' => $p['image'],
                    'user_id' => $user->id,
                    'category_id' => $cat->id,
                ]);
            }
        }
    }
}