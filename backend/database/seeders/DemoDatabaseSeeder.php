<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $storeId = (int) (DB::table('stores')->orderBy('id')->value('id') ?: 1);

        $this->ensureDemoImages();

        $passwordHash = Hash::make('12345678');

        // 1. Cities
        $citiesData = [
            ['slug' => 'Cairo', 'en' => 'Cairo', 'ar' => 'القاهرة', 'min' => 250, 'charge' => 25],
            ['slug' => 'Giza', 'en' => 'Giza', 'ar' => 'الجيزة', 'min' => 250, 'charge' => 30],
            ['slug' => 'Alexandria', 'en' => 'Alexandria', 'ar' => 'الإسكندرية', 'min' => 300, 'charge' => 35],
            ['slug' => 'Riyadh', 'en' => 'Riyadh', 'ar' => 'الرياض', 'min' => 350, 'charge' => 40],
        ];
        $cityIds = [];
        foreach ($citiesData as $c) {
            $nameJson = json_encode(['en' => $c['en'], 'ar' => $c['ar']], JSON_UNESCAPED_UNICODE);
            $existing = DB::table('cities')->where('name', 'like', '%' . $c['en'] . '%')->first();
            if ($existing) {
                $cityIds[] = $existing->id;
            } else {
                $cityIds[] = DB::table('cities')->insertGetId([
                    'name' => $nameJson,
                    'minimum_free_delivery_order_amount' => $c['min'],
                    'delivery_charges' => $c['charge'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 2. Zipcodes
        $zipcodesData = [
            ['code' => '11511', 'city_idx' => 0, 'min' => 200, 'charge' => 20],
            ['code' => '12511', 'city_idx' => 1, 'min' => 220, 'charge' => 25],
            ['code' => '21500', 'city_idx' => 2, 'min' => 250, 'charge' => 30],
            ['code' => '11461', 'city_idx' => 3, 'min' => 300, 'charge' => 35],
        ];
        $zipcodeIds = [];
        foreach ($zipcodesData as $z) {
            $existing = DB::table('zipcodes')->where('zipcode', $z['code'])->first();
            if ($existing) {
                $zipcodeIds[] = $existing->id;
            } else {
                $zipcodeIds[] = DB::table('zipcodes')->insertGetId([
                    'zipcode' => $z['code'],
                    'city_id' => $cityIds[$z['city_idx']],
                    'minimum_free_delivery_order_amount' => $z['min'],
                    'delivery_charges' => $z['charge'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 3. Zones
        $zonesData = [
            ['en' => 'Greater Cairo Zone', 'ar' => 'نطاق القاهرة الكبرى', 'cities' => [$cityIds[0], $cityIds[1]], 'zips' => [$zipcodeIds[0], $zipcodeIds[1]]],
            ['en' => 'Alexandria Coast Zone', 'ar' => 'نطاق الإسكندرية', 'cities' => [$cityIds[2]], 'zips' => [$zipcodeIds[2]]],
            ['en' => 'Riyadh Central Zone', 'ar' => 'نطاق الرياض المركزي', 'cities' => [$cityIds[3]], 'zips' => [$zipcodeIds[3]]],
        ];
        $zoneIds = [];
        foreach ($zonesData as $zn) {
            $nameJson = json_encode(['en' => $zn['en'], 'ar' => $zn['ar']], JSON_UNESCAPED_UNICODE);
            $existing = DB::table('zones')->where('name', 'like', '%' . $zn['en'] . '%')->first();
            if ($existing) {
                $zoneIds[] = $existing->id;
            } else {
                $zoneIds[] = DB::table('zones')->insertGetId([
                    'name' => $nameJson,
                    'serviceable_city_ids' => implode(',', $zn['cities']),
                    'serviceable_zipcode_ids' => implode(',', $zn['zips']),
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 4. Categories
        $categoriesData = [
            ['slug' => 'electronics-smart-devices', 'en' => 'Electronics & Smart Devices', 'ar' => 'الإلكترونيات والأجهزة الذكية'],
            ['slug' => 'fashion-apparel', 'en' => 'Fashion & Apparel', 'ar' => 'الأزياء والملابس'],
            ['slug' => 'home-kitchen', 'en' => 'Home & Kitchen', 'ar' => 'المنزل والمطبخ'],
            ['slug' => 'groceries-organic', 'en' => 'Groceries & Fresh Food', 'ar' => 'البقالة والأغذية الطازجة'],
            ['slug' => 'beauty-personal-care', 'en' => 'Beauty & Personal Care', 'ar' => 'الجمال والعناية الشخصية'],
            ['slug' => 'sports-fitness', 'en' => 'Sports & Fitness', 'ar' => 'الرياضة واللياقة'],
        ];
        $categoryIds = [];
        foreach ($categoriesData as $idx => $cat) {
            $existing = DB::table('categories')->where('slug', $cat['slug'])->first();
            if ($existing) {
                $categoryIds[] = $existing->id;
            } else {
                $categoryIds[] = DB::table('categories')->insertGetId([
                    'store_id' => $storeId,
                    'name' => json_encode(['en' => $cat['en'], 'ar' => $cat['ar']], JSON_UNESCAPED_UNICODE),
                    'parent_id' => 0,
                    'slug' => $cat['slug'],
                    'image' => '/demo-thumb.jpg',
                    'banner' => '/demo-banner.jpg',
                    'style' => 'style_1',
                    'row_order' => $idx + 1,
                    'status' => 1,
                    'affiliate_commission' => 5.00,
                    'is_in_affiliate' => 1,
                    'clicks' => 25 + ($idx * 12),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 5. Brands
        $brandsData = [
            ['slug' => 'muqtarin-tech', 'en' => 'MUQTARIN Tech', 'ar' => 'مقترن تك'],
            ['slug' => 'al-noor-home', 'en' => 'Al-Noor Home', 'ar' => 'النور هوم'],
            ['slug' => 'oasis-style', 'en' => 'Oasis Style', 'ar' => 'واحة ستايل'],
            ['slug' => 'pure-nature', 'en' => 'Pure Nature', 'ar' => 'نقاء الطبيعة'],
        ];
        $brandIds = [];
        foreach ($brandsData as $b) {
            $existing = DB::table('brands')->where('slug', $b['slug'])->first();
            if ($existing) {
                $brandIds[] = $existing->id;
            } else {
                $brandIds[] = DB::table('brands')->insertGetId([
                    'store_id' => $storeId,
                    'name' => json_encode(['en' => $b['en'], 'ar' => $b['ar']], JSON_UNESCAPED_UNICODE),
                    'slug' => $b['slug'],
                    'image' => '/demo-thumb.jpg',
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 6. Customers (role_id = 2)
        $customersData = [
            ['username' => 'mariam_adel', 'email' => 'mariam.adel@demo.muqtarin.test', 'mobile' => '01010000001', 'active' => 1, 'days_ago' => 1],
            ['username' => 'mohamed_hassan', 'email' => 'mohamed.hassan@demo.muqtarin.test', 'mobile' => '01010000002', 'active' => 1, 'days_ago' => 2],
            ['username' => 'nourhan_samir', 'email' => 'nourhan.samir@demo.muqtarin.test', 'mobile' => '01010000003', 'active' => 1, 'days_ago' => 3],
            ['username' => 'khaled_mansour', 'email' => 'khaled.mansour@demo.muqtarin.test', 'mobile' => '01010000004', 'active' => 1, 'days_ago' => 5],
            ['username' => 'salma_ibrahim', 'email' => 'salma.ibrahim@demo.muqtarin.test', 'mobile' => '01010000005', 'active' => 1, 'days_ago' => 6],
            ['username' => 'mostafa_ali', 'email' => 'mostafa.ali@demo.muqtarin.test', 'mobile' => '01010000006', 'active' => 1, 'days_ago' => 8],
            ['username' => 'hana_youssef', 'email' => 'hana.youssef@demo.muqtarin.test', 'mobile' => '01010000007', 'active' => 1, 'days_ago' => 10],
            ['username' => 'amr_fathy', 'email' => 'amr.fathy@demo.muqtarin.test', 'mobile' => '01010000008', 'active' => 0, 'days_ago' => 12],
            ['username' => 'dina_kamal', 'email' => 'dina.kamal@demo.muqtarin.test', 'mobile' => '01010000009', 'active' => 1, 'days_ago' => 36],
            ['username' => 'hassan_ragab', 'email' => 'hassan.ragab@demo.muqtarin.test', 'mobile' => '01010000010', 'active' => 1, 'days_ago' => 42],
            ['username' => 'reem_tarek', 'email' => 'reem.tarek@demo.muqtarin.test', 'mobile' => '01010000011', 'active' => 1, 'days_ago' => 48],
            ['username' => 'ziad_nabil', 'email' => 'ziad.nabil@demo.muqtarin.test', 'mobile' => '01010000012', 'active' => 0, 'days_ago' => 55],
        ];
        $customerIds = [];
        foreach ($customersData as $cu) {
            $existing = DB::table('users')->where('email', $cu['email'])->first();
            $createdAt = $now->copy()->subDays($cu['days_ago']);
            if ($existing) {
                $customerIds[] = $existing->id;
            } else {
                $customerIds[] = DB::table('users')->insertGetId([
                    'role_id' => 2,
                    'username' => $cu['username'],
                    'password' => $passwordHash,
                    'email' => $cu['email'],
                    'mobile' => $cu['mobile'],
                    'image' => 'demo-avatar.jpg',
                    'disk' => 'public',
                    'balance' => 150,
                    'active' => $cu['active'],
                    'country_code' => 20,
                    'serviceable_cities' => implode(',', $cityIds),
                    'status' => 1,
                    'is_notification_on' => 1,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'active_status' => $cu['active'],
                    'avatar' => 'avatar.png',
                ]);
            }
        }

        // 7. Delivery Boys (role_id = 3)
        $driversData = [
            ['username' => 'karim_delivery', 'email' => 'karim.driver@demo.muqtarin.test', 'mobile' => '01020000001'],
            ['username' => 'omar_express', 'email' => 'omar.driver@demo.muqtarin.test', 'mobile' => '01020000002'],
            ['username' => 'youssef_rider', 'email' => 'youssef.driver@demo.muqtarin.test', 'mobile' => '01020000003'],
            ['username' => 'tarek_courier', 'email' => 'tarek.driver@demo.muqtarin.test', 'mobile' => '01020000004'],
        ];
        $driverIds = [];
        foreach ($driversData as $dr) {
            $existing = DB::table('users')->where('email', $dr['email'])->first();
            if ($existing) {
                $driverIds[] = $existing->id;
            } else {
                $driverIds[] = DB::table('users')->insertGetId([
                    'role_id' => 3,
                    'username' => $dr['username'],
                    'password' => $passwordHash,
                    'email' => $dr['email'],
                    'mobile' => $dr['mobile'],
                    'image' => 'demo-avatar.jpg',
                    'disk' => 'public',
                    'balance' => 240,
                    'active' => 1,
                    'bonus_type' => 'percentage_per_order_item',
                    'bonus' => 10,
                    'country_code' => 20,
                    'serviceable_cities' => implode(',', $cityIds),
                    'serviceable_zipcodes' => implode(',', $zipcodeIds),
                    'serviceable_zones' => implode(',', $zoneIds),
                    'status' => 1,
                    'is_notification_on' => 1,
                    'created_at' => $now->copy()->subDays(20),
                    'updated_at' => $now,
                    'active_status' => 1,
                    'avatar' => 'avatar.png',
                ]);
            }
        }

        // 8. Sellers (role_id = 4 + seller_data + seller_store)
        $sellersData = [
            [
                'username' => 'ahmed_tech',
                'email' => 'ahmed.tech@demo.muqtarin.test',
                'mobile' => '01030000001',
                'slug' => 'muqtarin-tech-hub',
                'store_name' => 'مقترن تك | MUQTARIN Tech Hub',
                'desc' => 'Smartphones, audio, and flagship accessories',
                'commission' => 10,
            ],
            [
                'username' => 'sara_fashion',
                'email' => 'sara.fashion@demo.muqtarin.test',
                'mobile' => '01030000002',
                'slug' => 'elite-wardrobe',
                'store_name' => 'أزياء النخبة | Elite Wardrobe',
                'desc' => 'Modern everyday apparel and footwear',
                'commission' => 12,
            ],
            [
                'username' => 'khaled_home',
                'email' => 'khaled.home@demo.muqtarin.test',
                'mobile' => '01030000003',
                'slug' => 'modern-living-hub',
                'store_name' => 'بيت العصرية | Modern Living',
                'desc' => 'Kitchen tools, lighting, and home essentials',
                'commission' => 8,
            ],
            [
                'username' => 'nora_organic',
                'email' => 'nora.organic@demo.muqtarin.test',
                'mobile' => '01030000004',
                'slug' => 'oasis-organic-market',
                'store_name' => 'خيرات الواحة | Oasis Organic',
                'desc' => 'Specialty coffee, organic honey, and natural care',
                'commission' => 10,
            ],
        ];
        $sellerUserIds = [];
        $sellerIds = [];
        foreach ($sellersData as $idx => $se) {
            $userRow = DB::table('users')->where('email', $se['email'])->first();
            if ($userRow) {
                $sUserId = $userRow->id;
            } else {
                $sUserId = DB::table('users')->insertGetId([
                    'role_id' => 4,
                    'username' => $se['username'],
                    'password' => $passwordHash,
                    'email' => $se['email'],
                    'mobile' => $se['mobile'],
                    'image' => 'demo-avatar.jpg',
                    'disk' => 'public',
                    'balance' => 1200,
                    'active' => 1,
                    'country_code' => 20,
                    'serviceable_cities' => implode(',', $cityIds),
                    'status' => 1,
                    'is_notification_on' => 1,
                    'created_at' => $now->copy()->subDays(25),
                    'updated_at' => $now,
                    'active_status' => 1,
                    'avatar' => 'avatar.png',
                ]);
            }
            $sellerUserIds[] = $sUserId;

            $sDataRow = DB::table('seller_data')->where('user_id', $sUserId)->first();
            if ($sDataRow) {
                $sId = $sDataRow->id;
            } else {
                $sId = DB::table('seller_data')->insertGetId([
                    'user_id' => $sUserId,
                    'national_identity_card' => '/demo-seller.jpg',
                    'authorized_signature' => '/demo-seller.jpg',
                    'disk' => 'public',
                    'pan_number' => 'PAN-MUQ-00' . ($idx + 1),
                    'status' => 1,
                    'created_at' => $now->copy()->subDays(25),
                    'updated_at' => $now,
                ]);
            }
            $sellerIds[] = $sId;

            $sStoreExists = DB::table('seller_store')
                ->where('seller_id', $sId)
                ->where('store_id', $storeId)
                ->exists();
            if (!$sStoreExists) {
                DB::table('seller_store')->insert([
                    'seller_id' => $sId,
                    'user_id' => $sUserId,
                    'store_id' => $storeId,
                    'slug' => $se['slug'],
                    'category_ids' => implode(',', $categoryIds),
                    'store_name' => $se['store_name'],
                    'store_description' => $se['desc'],
                    'logo' => '/demo-seller.jpg',
                    'store_thumbnail' => '/demo-banner.jpg',
                    'other_documents' => json_encode([]),
                    'disk' => 'public',
                    'store_url' => 'https://muqtarin.demo/' . $se['slug'],
                    'no_of_ratings' => 18 + ($idx * 7),
                    'rating' => 4.8,
                    'bank_name' => 'MUQTARIN Bank',
                    'bank_code' => 'MUQ001',
                    'account_name' => $se['store_name'],
                    'account_number' => '10002000300' . ($idx + 1),
                    'address_proof' => '/demo-seller.jpg',
                    'tax_name' => 'VAT',
                    'tax_number' => 'TAX-100' . ($idx + 1),
                    'permissions' => json_encode(['require_products_approval' => 0, 'customer_privacy' => 1]),
                    'commission' => $se['commission'],
                    'latitude' => '30.0444',
                    'longitude' => '31.2357',
                    'city' => $cityIds[0],
                    'zipcode' => $zipcodeIds[0],
                    'deliverable_type' => 1,
                    'deliverable_zones' => implode(',', $zoneIds),
                    'status' => 1,
                    'created_at' => $now->copy()->subDays(25),
                    'updated_at' => $now,
                ]);
            }
        }

        // 9. Products & Variants
        $productsData = [
            ['slug' => 'pro-wireless-earbuds-x1', 'en' => 'Pro Wireless Earbuds X1', 'ar' => 'سماعات لاسلكية برو X1', 'cat' => 0, 'seller' => 0, 'brand' => 0, 'price' => 149, 'special' => 119],
            ['slug' => 'smart-watch-emerald-s4', 'en' => 'Smart Watch Emerald S4', 'ar' => 'ساعة ذكية إيميرالد S4', 'cat' => 0, 'seller' => 0, 'brand' => 0, 'price' => 240, 'special' => 199],
            ['slug' => 'fast-gan-charger-65w', 'en' => 'GaN Fast Charger 65W', 'ar' => 'شاحن سريع 65 واط GaN', 'cat' => 0, 'seller' => 0, 'brand' => 0, 'price' => 55, 'special' => 45],
            ['slug' => 'linen-summer-jacket', 'en' => 'Linen Tailored Jacket', 'ar' => 'جاكيت كتان عصري', 'cat' => 1, 'seller' => 1, 'brand' => 2, 'price' => 135, 'special' => 110],
            ['slug' => 'cotton-polo-classic', 'en' => 'Premium Cotton Polo', 'ar' => 'قميص بولو قطن فاخر', 'cat' => 1, 'seller' => 1, 'brand' => 2, 'price' => 65, 'special' => 49],
            ['slug' => 'urban-running-sneakers', 'en' => 'Urban Comfort Sneakers', 'ar' => 'حذاء رياضي مريح للمشي', 'cat' => 5, 'seller' => 1, 'brand' => 2, 'price' => 120, 'special' => 95],
            ['slug' => 'espresso-barista-maker', 'en' => 'Compact Espresso Maker', 'ar' => 'ماكينة إسبريسو منزلية', 'cat' => 2, 'seller' => 2, 'brand' => 1, 'price' => 310, 'special' => 265],
            ['slug' => 'ceramic-dinnerware-set', 'en' => 'Ceramic Dinnerware 16-Pc Set', 'ar' => 'طقم سفرة سيراميك 16 قطعة', 'cat' => 2, 'seller' => 2, 'brand' => 1, 'price' => 160, 'special' => 135],
            ['slug' => 'smart-desk-lamp-led', 'en' => 'Minimalist LED Desk Lamp', 'ar' => 'مصباح مكتب ذكي LED', 'cat' => 2, 'seller' => 2, 'brand' => 1, 'price' => 75, 'special' => 59],
            ['slug' => 'sidr-organic-honey-500g', 'en' => 'Pure Sidr Honey 500g', 'ar' => 'عسل سدر عضوي 500 جم', 'cat' => 3, 'seller' => 3, 'brand' => 3, 'price' => 85, 'special' => 72],
            ['slug' => 'specialty-arabica-beans', 'en' => 'Specialty Arabica Coffee 1kg', 'ar' => 'حبوب قهوة أرابيكا مختصة 1 كجم', 'cat' => 3, 'seller' => 3, 'brand' => 3, 'price' => 58, 'special' => 48],
            ['slug' => 'argan-botanical-serum', 'en' => 'Botanical Argan Care Serum', 'ar' => 'سيروم الأرغان الطبيعي للعناية', 'cat' => 4, 'seller' => 3, 'brand' => 3, 'price' => 68, 'special' => 54],
        ];
        $variants = [];
        foreach ($productsData as $idx => $p) {
            $existingProd = DB::table('products')->where('slug', $p['slug'])->first();
            $sId = $sellerIds[$p['seller']];
            $catId = $categoryIds[$p['cat']];
            $brandId = $brandIds[$p['brand']];
            if ($existingProd) {
                $pId = $existingProd->id;
            } else {
                $pId = DB::table('products')->insertGetId([
                    'store_id' => $storeId,
                    'product_identity' => 'MUQ-' . str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT),
                    'category_id' => $catId,
                    'seller_id' => $sId,
                    'row_order' => $idx + 1,
                    'type' => 'simple_product',
                    'stock_type' => '0',
                    'name' => json_encode(['en' => $p['en'], 'ar' => $p['ar']], JSON_UNESCAPED_UNICODE),
                    'short_description' => json_encode(['en' => $p['en'] . ' - MUQTARIN Verified Quality', 'ar' => $p['ar'] . ' - جودة معتمدة من مقترن ستور'], JSON_UNESCAPED_UNICODE),
                    'slug' => $p['slug'],
                    'cod_allowed' => 1,
                    'minimum_order_quantity' => 1,
                    'quantity_step_size' => 1,
                    'total_allowed_quantity' => 50,
                    'is_prices_inclusive_tax' => 1,
                    'is_returnable' => 1,
                    'is_cancelable' => 1,
                    'cancelable_till' => 'shipped',
                    'image' => '/demo-thumb.jpg',
                    'other_images' => json_encode(['/demo-banner.jpg']),
                    'brand' => (string) $brandId,
                    'sku' => 'SKU-MUQ-' . ($idx + 101),
                    'stock' => 120,
                    'availability' => 1,
                    'rating' => 4.7,
                    'no_of_ratings' => 12 + $idx,
                    'description' => '<p>' . e($p['en']) . '</p>',
                    'deliverable_type' => 1,
                    'city_deliverable_type' => 1,
                    'deliverable_cities' => implode(',', $cityIds),
                    'deliverable_zones' => implode(',', $zoneIds),
                    'status' => 1,
                    'created_at' => $now->copy()->subDays(22),
                    'updated_at' => $now,
                ]);
            }

            $existingVar = DB::table('product_variants')->where('product_id', $pId)->first();
            if ($existingVar) {
                $vId = $existingVar->id;
            } else {
                $vId = DB::table('product_variants')->insertGetId([
                    'product_id' => $pId,
                    'attribute_value_ids' => '',
                    'attribute_set' => 'Standard',
                    'price' => $p['price'],
                    'special_price' => $p['special'],
                    'sku' => 'VAR-MUQ-' . ($idx + 101),
                    'stock' => 120,
                    'weight' => 0.5,
                    'height' => 10,
                    'breadth' => 10,
                    'length' => 10,
                    'images' => json_encode(['/demo-thumb.jpg']),
                    'availability' => 1,
                    'status' => 1,
                    'created_at' => $now->copy()->subDays(22),
                    'updated_at' => $now,
                ]);
            }

            $variants[] = [
                'product_id' => $pId,
                'variant_id' => $vId,
                'seller_id' => $sId,
                'name' => $p['en'],
                'price' => $p['price'],
                'special_price' => $p['special'],
            ];
        }

        // 10. Addresses + Orders + Order Items (only if orders table is empty for Store 1)
        if (DB::table('orders')->where('store_id', $storeId)->count() === 0) {
            $addressIds = [];
            foreach ($customerIds as $idx => $custId) {
                $addressIds[$custId] = DB::table('addresses')->insertGetId([
                    'user_id' => $custId,
                    'name' => $customersData[$idx]['username'],
                    'type' => 'Home',
                    'mobile' => $customersData[$idx]['mobile'],
                    'address' => '15 Tahrir Square, Downtown',
                    'landmark' => 'Near Metro Station',
                    'city_id' => $cityIds[$idx % count($cityIds)],
                    'city' => $citiesData[$idx % count($citiesData)]['en'],
                    'area' => 'Downtown District',
                    'pincode' => $zipcodesData[$idx % count($zipcodesData)]['code'],
                    'system_pincode' => 1,
                    'country_code' => 20,
                    'state' => 'Cairo',
                    'country' => 'Egypt',
                    'is_default' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Carefully spread across today, this week, last 30 days, and prior months
            $orderPlans = [
                ['days_ago' => 0, 'hours_ago' => 1, 'status' => 'delivered', 'pay' => 'stripe', 'qty' => 2, 'v' => 0],
                ['days_ago' => 0, 'hours_ago' => 3, 'status' => 'received', 'pay' => 'cod', 'qty' => 1, 'v' => 1],
                ['days_ago' => 0, 'hours_ago' => 5, 'status' => 'processed', 'pay' => 'paypal', 'qty' => 3, 'v' => 6],
                ['days_ago' => 1, 'hours_ago' => 2, 'status' => 'delivered', 'pay' => 'stripe', 'qty' => 2, 'v' => 3],
                ['days_ago' => 1, 'hours_ago' => 4, 'status' => 'shipped', 'pay' => 'cod', 'qty' => 1, 'v' => 7],
                ['days_ago' => 2, 'hours_ago' => 1, 'status' => 'delivered', 'pay' => 'wallet', 'qty' => 2, 'v' => 9],
                ['days_ago' => 2, 'hours_ago' => 6, 'status' => 'received', 'pay' => 'cod', 'qty' => 2, 'v' => 4],
                ['days_ago' => 3, 'hours_ago' => 2, 'status' => 'delivered', 'pay' => 'stripe', 'qty' => 1, 'v' => 6],
                ['days_ago' => 4, 'hours_ago' => 3, 'status' => 'shipped', 'pay' => 'paypal', 'qty' => 2, 'v' => 5],
                ['days_ago' => 5, 'hours_ago' => 2, 'status' => 'delivered', 'pay' => 'cod', 'qty' => 3, 'v' => 10],
                ['days_ago' => 6, 'hours_ago' => 4, 'status' => 'processed', 'pay' => 'stripe', 'qty' => 1, 'v' => 1],
                ['days_ago' => 8, 'hours_ago' => 2, 'status' => 'delivered', 'pay' => 'stripe', 'qty' => 2, 'v' => 2],
                ['days_ago' => 10, 'hours_ago' => 3, 'status' => 'delivered', 'pay' => 'wallet', 'qty' => 2, 'v' => 8],
                ['days_ago' => 12, 'hours_ago' => 1, 'status' => 'shipped', 'pay' => 'cod', 'qty' => 1, 'v' => 3],
                ['days_ago' => 14, 'hours_ago' => 5, 'status' => 'delivered', 'pay' => 'stripe', 'qty' => 3, 'v' => 0],
                ['days_ago' => 16, 'hours_ago' => 2, 'status' => 'cancelled', 'pay' => 'cod', 'qty' => 1, 'v' => 11],
                ['days_ago' => 19, 'hours_ago' => 3, 'status' => 'delivered', 'pay' => 'paypal', 'qty' => 2, 'v' => 7],
                ['days_ago' => 22, 'hours_ago' => 2, 'status' => 'returned', 'pay' => 'cod', 'qty' => 1, 'v' => 4],
                ['days_ago' => 25, 'hours_ago' => 4, 'status' => 'delivered', 'pay' => 'stripe', 'qty' => 2, 'v' => 6],
                ['days_ago' => 28, 'hours_ago' => 2, 'status' => 'delivered', 'pay' => 'stripe', 'qty' => 2, 'v' => 9],
                ['days_ago' => 38, 'hours_ago' => 2, 'status' => 'delivered', 'pay' => 'paypal', 'qty' => 3, 'v' => 1],
                ['days_ago' => 52, 'hours_ago' => 2, 'status' => 'delivered', 'pay' => 'stripe', 'qty' => 2, 'v' => 6],
                ['days_ago' => 74, 'hours_ago' => 2, 'status' => 'delivered', 'pay' => 'cod', 'qty' => 4, 'v' => 0],
                ['days_ago' => 96, 'hours_ago' => 2, 'status' => 'received', 'pay' => 'wallet', 'qty' => 2, 'v' => 5],
            ];

            foreach ($orderPlans as $idx => $plan) {
                $custIdx = $idx % count($customerIds);
                $custId = $customerIds[$custIdx];
                $driverId = $driverIds[$idx % count($driverIds)];
                $variant = $variants[$plan['v']];
                $ts = $now->copy()->subDays($plan['days_ago'])->subHours($plan['hours_ago']);

                $unitPrice = (float) $variant['special_price'];
                $qty = (int) $plan['qty'];
                $subTotal = round($unitPrice * $qty, 2);
                $deliveryCharge = 25.0;
                $finalTotal = round($subTotal + $deliveryCharge, 2);
                $adminComm = round($subTotal * 0.10, 2);
                $sellerComm = round($subTotal - $adminComm, 2);

                $orderId = DB::table('orders')->insertGetId([
                    'user_id' => $custId,
                    'store_id' => $storeId,
                    'address_id' => $addressIds[$custId],
                    'mobile' => $customersData[$custIdx]['mobile'],
                    'total' => $subTotal,
                    'delivery_charge' => $deliveryCharge,
                    'is_delivery_charge_returnable' => 0,
                    'wallet_balance' => 0,
                    'promo_discount' => 0,
                    'discount' => 0,
                    'total_payable' => $finalTotal,
                    'final_total' => $finalTotal,
                    'payment_method' => $plan['pay'],
                    'latitude' => '30.0444',
                    'longitude' => '31.2357',
                    'address' => '15 Tahrir Square, Cairo, Egypt',
                    'delivery_time' => '10:00 AM - 02:00 PM',
                    'delivery_date' => $ts->toDateString(),
                    'otp' => 1234 + $idx,
                    'email' => $customersData[$custIdx]['email'],
                    'notes' => 'Demo order #' . ($idx + 1),
                    'is_pos_order' => 0,
                    'is_shiprocket_order' => 0,
                    'is_cod_collected' => $plan['status'] === 'delivered' ? 1 : 0,
                    'type' => 'simple_product',
                    'order_payment_currency_id' => 1,
                    'order_payment_currency_code' => 'USD',
                    'base_currency_code' => 'USD',
                    'order_payment_currency_conversion_rate' => 1,
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);

                $statusHistory = json_encode([['received', $ts->format('d-m-Y h:i:sa')], [$plan['status'], $ts->format('d-m-Y h:i:sa')]]);

                DB::table('order_items')->insert([
                    'user_id' => $custId,
                    'store_id' => $storeId,
                    'order_id' => $orderId,
                    'delivery_boy_id' => $driverId,
                    'seller_id' => $variant['seller_id'],
                    'is_credited' => $plan['status'] === 'delivered' ? 1 : 0,
                    'otp' => 1234 + $idx,
                    'product_name' => $variant['name'],
                    'variant_name' => 'Standard',
                    'product_variant_id' => $variant['variant_id'],
                    'quantity' => $qty,
                    'delivered_quantity' => $plan['status'] === 'delivered' ? $qty : 0,
                    'price' => $variant['price'],
                    'discounted_price' => $unitPrice,
                    'tax_ids' => '',
                    'tax_percent' => 0,
                    'tax_amount' => 0,
                    'discount' => max(0, $variant['price'] - $unitPrice),
                    'sub_total' => $subTotal,
                    'deliver_by' => $driversData[$idx % count($driversData)]['username'],
                    'updated_by' => 1,
                    'status' => $statusHistory,
                    'admin_commission_amount' => $adminComm,
                    'seller_commission_amount' => $sellerComm,
                    'active_status' => $plan['status'],
                    'is_sent' => 0,
                    'order_type' => 'simple_product',
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);
            }
        }

        // 10B. Transactions for orders
        if (DB::table('transactions')->count() === 0) {
            $orderRows = DB::table('orders')->where('store_id', $storeId)->get();
            foreach ($orderRows as $o) {
                $itemRow = DB::table('order_items')->where('order_id', $o->id)->first();
                $txStatus = $o->payment_method === 'cod'
                    ? ($o->is_cod_collected ? 'success' : 'pending')
                    : 'success';
                DB::table('transactions')->insert([
                    'transaction_type' => 'transaction',
                    'user_id' => $o->user_id,
                    'order_id' => (string) $o->id,
                    'order_item_id' => $itemRow ? $itemRow->id : null,
                    'type' => $o->payment_method,
                    'txn_id' => 'TXN-MUQ-' . str_pad((string) $o->id, 5, '0', STR_PAD_LEFT),
                    'amount' => $o->final_total,
                    'status' => $txStatus,
                    'currency_code' => $o->order_payment_currency_code ?? 'USD',
                    'payer_email' => $o->email,
                    'message' => 'Demo payment (' . $o->payment_method . ')',
                    'transaction_date' => $o->created_at,
                    'is_refund' => 0,
                    'created_at' => $o->created_at,
                    'updated_at' => $o->updated_at,
                ]);
            }
        }

        // 11. Support Ticket Types & Tickets
        if (DB::table('ticket_types')->count() === 0) {
            $types = [
                'Order Delivery Delay',
                'Payment & Refund Request',
                'Seller Verification',
                'Product Quality Inquiry',
            ];
            $typeIds = [];
            foreach ($types as $tTitle) {
                $typeIds[] = DB::table('ticket_types')->insertGetId([
                    'title' => $tTitle,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $ticketsData = [
                ['type' => 0, 'cust' => 0, 'subject' => 'تأخر شحنة الطلب #2 عن الموعد المحدد', 'desc' => 'أرجو متابعة حالة التوصيل مع المندوب.', 'status' => 1, 'days' => 0],
                ['type' => 1, 'cust' => 1, 'subject' => 'استرجاع مبلغ منتج مُلغى عبر البطاقة', 'desc' => 'تم إلغاء الطلب وأنتظر تأكيد الاسترداد.', 'status' => 2, 'days' => 1],
                ['type' => 3, 'cust' => 2, 'subject' => 'استفسار عن ضمان السماعات اللاسلكية X1', 'desc' => 'هل الضمان شامل الاستبدال خلال السنة الأولى؟', 'status' => 5, 'days' => 2],
                ['type' => 2, 'cust' => 3, 'subject' => 'تحديث بيانات السجل التجاري للبائع', 'desc' => 'تم رفع المستندات الجديدة للمراجعة.', 'status' => 1, 'days' => 3],
                ['type' => 0, 'cust' => 4, 'subject' => 'تعديل عنوان التوصيل في القاهرة الجديدة', 'desc' => 'أرجو تحديث العنوان قبل خروج الشحنة.', 'status' => 3, 'days' => 4],
                ['type' => 1, 'cust' => 5, 'subject' => 'تفعيل رصيد المحفظة للطلب الأخير', 'desc' => 'تم استخدام الرصيد بنجاح وتأكيد الفاتورة.', 'status' => 4, 'days' => 6],
            ];
            foreach ($ticketsData as $tk) {
                $cId = $customerIds[$tk['cust']];
                $ts = $now->copy()->subDays($tk['days']);
                DB::table('tickets')->insert([
                    'ticket_type_id' => $typeIds[$tk['type']],
                    'user_id' => $cId,
                    'subject' => $tk['subject'],
                    'email' => $customersData[$tk['cust']]['email'],
                    'description' => $tk['desc'],
                    'status' => $tk['status'],
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);
            }
        }

        // 12. Chatify Demo Messages (between sellers/customers and Admin ID=1)
        if (DB::table('ch_messages')->count() === 0) {
            $adminId = 1;
            $messages = [
                ['from' => $sellerUserIds[0], 'to' => $adminId, 'body' => 'مرحباً، تم إضافة دفعة جديدة من منتجات الإلكترونيات للمراجعة.', 'seen' => 0, 'mins' => 15],
                ['from' => $sellerUserIds[1], 'to' => $adminId, 'body' => 'أهلاً بالإدارة، يرجى اعتماد تحديث أسعار تشكيلة الموسم الجديد.', 'seen' => 0, 'mins' => 45],
                ['from' => $customerIds[0], 'to' => $adminId, 'body' => 'السلام عليكم، أود التأكد من موعد وصول طلبي اليوم.', 'seen' => 1, 'mins' => 120],
                ['from' => $sellerUserIds[2], 'to' => $adminId, 'body' => 'تم تجهيز جميع شحنات بيت العصرية لشركة الشحن.', 'seen' => 1, 'mins' => 240],
                ['from' => $sellerUserIds[3], 'to' => $adminId, 'body' => 'شكراً لتفعيل المتجر على منصة مقترن ستور!', 'seen' => 1, 'mins' => 480],
            ];
            foreach ($messages as $m) {
                $ts = $now->copy()->subMinutes($m['mins']);
                DB::table('ch_messages')->insert([
                    'id' => (string) Str::uuid(),
                    'from_id' => $m['from'],
                    'to_id' => $m['to'],
                    'body' => $m['body'],
                    'attachment' => null,
                    'seen' => $m['seen'],
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);
            }
        }
    }

    private function ensureDemoImages(): void
    {
        $iconSrc = public_path('assets/brand/icon.jpg');
        $logoSrc = public_path('assets/brand/logo.jpg');
        if (!File::exists($iconSrc)) {
            return;
        }
        $bannerSrc = File::exists($logoSrc) ? $logoSrc : $iconSrc;

        $targets = [
            public_path('storage/media/demo-thumb.jpg') => $iconSrc,
            public_path('storage/media/demo-banner.jpg') => $bannerSrc,
            public_path('storage/sellers/demo-seller.jpg') => $iconSrc,
            public_path('storage/user_image/demo-avatar.jpg') => $iconSrc,
        ];

        foreach ($targets as $dest => $src) {
            $dir = dirname($dest);
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0775, true);
            }
            if (!File::exists($dest)) {
                File::copy($src, $dest);
            }
        }
    }
}
