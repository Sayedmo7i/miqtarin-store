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

        // 1A. Brand Store #1 cleanly (remove Demo Store / legacy upload filenames)
        DB::table('stores')->where('id', $storeId)->update([
            'name' => json_encode(['en' => 'MUQTARIN Store', 'ar' => 'مقترن ستور'], JSON_UNESCAPED_UNICODE),
            'slug' => 'muqtarin-store',
            'description' => json_encode([
                'en' => 'Official MUQTARIN multi-vendor marketplace connecting verified local sellers with curated everyday products.',
                'ar' => 'سوق مقترن ستور الرسمي متعدد البائعين — منتجات مختارة وتوصيل موثوق.',
            ], JSON_UNESCAPED_UNICODE),
            'image' => 'media/muqtarin-icon.svg',
            'banner_image' => 'media/hero-editorial-1.jpg',
            'banner_image_for_most_selling_product' => 'media/hero-editorial-2.jpg',
            'stack_image' => 'media/muqtarin-icon.svg',
            'login_image' => 'media/hero-editorial-1.jpg',
            'half_store_logo' => 'media/muqtarin-logo.svg',
            'updated_at' => $now,
        ]);

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
                DB::table('cities')->where('id', $existing->id)->update([
                    'name' => $nameJson,
                    'minimum_free_delivery_order_amount' => $c['min'],
                    'delivery_charges' => $c['charge'],
                    'updated_at' => $now,
                ]);
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
            ['code' => '11511', 'city_idx' => 0, 'min' => 250, 'charge' => 25],
            ['code' => '12511', 'city_idx' => 1, 'min' => 250, 'charge' => 30],
            ['code' => '21500', 'city_idx' => 2, 'min' => 300, 'charge' => 35],
            ['code' => '11461', 'city_idx' => 3, 'min' => 350, 'charge' => 40],
        ];
        $zipcodeIds = [];
        foreach ($zipcodesData as $z) {
            $existing = DB::table('zipcodes')->where('zipcode', $z['code'])->first();
            if ($existing) {
                DB::table('zipcodes')->where('id', $existing->id)->update([
                    'city_id' => $cityIds[$z['city_idx']],
                    'minimum_free_delivery_order_amount' => $z['min'],
                    'delivery_charges' => $z['charge'],
                    'updated_at' => $now,
                ]);
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
            ['slug' => 'electronics-smart-devices', 'en' => 'Electronics & Smart Devices', 'ar' => 'الإلكترونيات والأجهزة الذكية', 'image' => 'media/prod-headphones.jpg', 'banner' => 'media/hero-editorial-1.jpg'],
            ['slug' => 'fashion-apparel', 'en' => 'Fashion & Apparel', 'ar' => 'الأزياء والملابس', 'image' => 'media/prod-jacket.png', 'banner' => 'media/hero-editorial-2.jpg'],
            ['slug' => 'home-kitchen', 'en' => 'Home & Kitchen', 'ar' => 'المنزل والمطبخ', 'image' => 'media/prod-espresso.jpg', 'banner' => 'media/hero-editorial-2.jpg'],
            ['slug' => 'groceries-organic', 'en' => 'Groceries & Fresh Food', 'ar' => 'البقالة والأغذية الطازجة', 'image' => 'media/prod-coffee.jpg', 'banner' => 'media/hero-editorial-1.jpg'],
            ['slug' => 'beauty-personal-care', 'en' => 'Beauty & Personal Care', 'ar' => 'الجمال والعناية الشخصية', 'image' => 'media/prod-serum.png', 'banner' => 'media/hero-editorial-2.jpg'],
            ['slug' => 'sports-fitness', 'en' => 'Sports & Fitness', 'ar' => 'الرياضة واللياقة', 'image' => 'media/prod-sneakers.png', 'banner' => 'media/hero-editorial-1.jpg'],
        ];
        $categoryIds = [];
        foreach ($categoriesData as $idx => $cat) {
            $existing = DB::table('categories')->where('slug', $cat['slug'])->first();
            if ($existing) {
                DB::table('categories')->where('id', $existing->id)->update([
                    'image' => $cat['image'],
                    'banner' => $cat['banner'],
                    'updated_at' => $now,
                ]);
                $categoryIds[] = $existing->id;
            } else {
                $categoryIds[] = DB::table('categories')->insertGetId([
                    'store_id' => $storeId,
                    'name' => json_encode(['en' => $cat['en'], 'ar' => $cat['ar']], JSON_UNESCAPED_UNICODE),
                    'parent_id' => 0,
                    'slug' => $cat['slug'],
                    'image' => $cat['image'],
                    'banner' => $cat['banner'],
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
            ['slug' => 'muqtarin-tech', 'en' => 'MUQTARIN Tech', 'ar' => 'مقترن تك', 'image' => 'media/prod-smartwatch.jpg'],
            ['slug' => 'al-noor-home', 'en' => 'Al-Noor Home', 'ar' => 'النور هوم', 'image' => 'media/prod-lamp.png'],
            ['slug' => 'oasis-style', 'en' => 'Oasis Style', 'ar' => 'واحة ستايل', 'image' => 'media/prod-polo.png'],
            ['slug' => 'pure-nature', 'en' => 'Pure Nature', 'ar' => 'نقاء الطبيعة', 'image' => 'media/prod-honey.png'],
        ];
        $brandIds = [];
        foreach ($brandsData as $b) {
            $existing = DB::table('brands')->where('slug', $b['slug'])->first();
            if ($existing) {
                DB::table('brands')->where('id', $existing->id)->update([
                    'image' => $b['image'],
                    'updated_at' => $now,
                ]);
                $brandIds[] = $existing->id;
            } else {
                $brandIds[] = DB::table('brands')->insertGetId([
                    'store_id' => $storeId,
                    'name' => json_encode(['en' => $b['en'], 'ar' => $b['ar']], JSON_UNESCAPED_UNICODE),
                    'slug' => $b['slug'],
                    'image' => $b['image'],
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
                DB::table('users')->where('id', $existing->id)->update([
                    'active' => $cu['active'],
                    'active_status' => $cu['active'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
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

        // 6B. Admin user (id = 1) localized contact (preserve admin@gmail.com & password)
        DB::table('users')->where('id', 1)->update([
            'country_code' => 20,
            'mobile' => '01002458810',
            'updated_at' => $now,
        ]);

        // 7. Delivery Boys (role_id = 3)
        $driversData = [
            ['username' => 'karim_delivery', 'email' => 'karim.driver@demo.muqtarin.test', 'mobile' => '01020000001', 'balance' => 420, 'bonus' => 12, 'zones' => [$zoneIds[0]]],
            ['username' => 'omar_express', 'email' => 'omar.driver@demo.muqtarin.test', 'mobile' => '01020000002', 'balance' => 315, 'bonus' => 10, 'zones' => [$zoneIds[0], $zoneIds[1]]],
            ['username' => 'youssef_rider', 'email' => 'youssef.driver@demo.muqtarin.test', 'mobile' => '01020000003', 'balance' => 190, 'bonus' => 8, 'zones' => [$zoneIds[1]]],
            ['username' => 'tarek_courier', 'email' => 'tarek.driver@demo.muqtarin.test', 'mobile' => '01020000004', 'balance' => 560, 'bonus' => 15, 'zones' => [$zoneIds[2]]],
        ];
        $driverIds = [];
        foreach ($driversData as $dr) {
            $existing = DB::table('users')->where('email', $dr['email'])->first();
            $driverZones = implode(',', $dr['zones']);
            if ($existing) {
                DB::table('users')->where('id', $existing->id)->update([
                    'balance' => $dr['balance'],
                    'bonus' => $dr['bonus'],
                    'serviceable_zones' => $driverZones,
                    'country_code' => 20,
                    'updated_at' => $now,
                ]);
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
                    'balance' => $dr['balance'],
                    'active' => 1,
                    'bonus_type' => 'percentage_per_order_item',
                    'bonus' => $dr['bonus'],
                    'country_code' => 20,
                    'serviceable_cities' => implode(',', $cityIds),
                    'serviceable_zipcodes' => implode(',', $zipcodeIds),
                    'serviceable_zones' => $driverZones,
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
                'balance' => 3420,
                'rating' => 4.9,
                'no_of_ratings' => 48,
                'logo' => 'seller-tech.png',
                'thumb' => 'seller-banner-tech.jpg',
            ],
            [
                'username' => 'sara_fashion',
                'email' => 'sara.fashion@demo.muqtarin.test',
                'mobile' => '01030000002',
                'slug' => 'elite-wardrobe',
                'store_name' => 'أزياء النخبة | Elite Wardrobe',
                'desc' => 'Modern everyday apparel and footwear',
                'commission' => 12,
                'balance' => 2740,
                'rating' => 4.7,
                'no_of_ratings' => 36,
                'logo' => 'seller-fashion.png',
                'thumb' => 'seller-banner-home.jpg',
            ],
            [
                'username' => 'khaled_home',
                'email' => 'khaled.home@demo.muqtarin.test',
                'mobile' => '01030000003',
                'slug' => 'modern-living-hub',
                'store_name' => 'بيت العصرية | Modern Living',
                'desc' => 'Kitchen tools, lighting, and home essentials',
                'commission' => 8,
                'balance' => 1850,
                'rating' => 4.5,
                'no_of_ratings' => 29,
                'logo' => 'seller-home.png',
                'thumb' => 'seller-banner-home.jpg',
            ],
            [
                'username' => 'nora_organic',
                'email' => 'nora.organic@demo.muqtarin.test',
                'mobile' => '01030000004',
                'slug' => 'oasis-organic-market',
                'store_name' => 'خيرات الواحة | Oasis Organic',
                'desc' => 'Specialty coffee, organic honey, and natural care',
                'commission' => 10,
                'balance' => 960,
                'rating' => 4.3,
                'no_of_ratings' => 21,
                'logo' => 'seller-organic.png',
                'thumb' => 'seller-banner-tech.jpg',
            ],
        ];
        $sellerUserIds = [];
        $sellerIds = [];
        foreach ($sellersData as $idx => $se) {
            $userRow = DB::table('users')->where('email', $se['email'])->first();
            if ($userRow) {
                $sUserId = $userRow->id;
                DB::table('users')->where('id', $sUserId)->update([
                    'balance' => $se['balance'],
                    'updated_at' => $now,
                ]);
            } else {
                $sUserId = DB::table('users')->insertGetId([
                    'role_id' => 4,
                    'username' => $se['username'],
                    'password' => $passwordHash,
                    'email' => $se['email'],
                    'mobile' => $se['mobile'],
                    'image' => 'demo-avatar.jpg',
                    'disk' => 'public',
                    'balance' => $se['balance'],
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
                    'national_identity_card' => $se['logo'],
                    'authorized_signature' => $se['logo'],
                    'disk' => 'public',
                    'pan_number' => 'PAN-MUQ-00' . ($idx + 1),
                    'status' => 1,
                    'created_at' => $now->copy()->subDays(25),
                    'updated_at' => $now,
                ]);
            }
            $sellerIds[] = $sId;

            $sStoreRow = DB::table('seller_store')
                ->where('seller_id', $sId)
                ->where('store_id', $storeId)
                ->first();
            if ($sStoreRow) {
                DB::table('seller_store')->where('id', $sStoreRow->id)->update([
                    'logo' => $se['logo'],
                    'store_thumbnail' => $se['thumb'],
                    'rating' => $se['rating'],
                    'no_of_ratings' => $se['no_of_ratings'],
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('seller_store')->insert([
                    'seller_id' => $sId,
                    'user_id' => $sUserId,
                    'store_id' => $storeId,
                    'slug' => $se['slug'],
                    'category_ids' => implode(',', $categoryIds),
                    'store_name' => $se['store_name'],
                    'store_description' => $se['desc'],
                    'logo' => $se['logo'],
                    'store_thumbnail' => $se['thumb'],
                    'other_documents' => json_encode([]),
                    'disk' => 'public',
                    'store_url' => 'https://muqtarin.demo/' . $se['slug'],
                    'no_of_ratings' => $se['no_of_ratings'],
                    'rating' => $se['rating'],
                    'bank_name' => 'MUQTARIN Bank',
                    'bank_code' => 'MUQ001',
                    'account_name' => $se['store_name'],
                    'account_number' => '10002000300' . ($idx + 1),
                    'address_proof' => $se['logo'],
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
            ['slug' => 'pro-wireless-earbuds-x1', 'en' => 'Pro Wireless Earbuds X1', 'ar' => 'سماعات لاسلكية برو X1', 'cat' => 0, 'seller' => 0, 'brand' => 0, 'price' => 149, 'special' => 119, 'rating' => 4.9, 'no_of_ratings' => 38, 'image' => 'media/prod-earbuds.jpg', 'tags' => 'audio,earbuds,wireless,anc,bluetooth', 'warranty' => '12 Months'],
            ['slug' => 'smart-watch-emerald-s4', 'en' => 'Smart Watch Emerald S4', 'ar' => 'ساعة ذكية إيميرالد S4', 'cat' => 0, 'seller' => 0, 'brand' => 0, 'price' => 240, 'special' => 199, 'rating' => 4.8, 'no_of_ratings' => 29, 'image' => 'media/prod-smartwatch.jpg', 'tags' => 'smartwatch,fitness,amoled,wearable', 'warranty' => '24 Months'],
            ['slug' => 'fast-gan-charger-65w', 'en' => 'GaN Fast Charger 65W', 'ar' => 'شاحن سريع 65 واط GaN', 'cat' => 0, 'seller' => 0, 'brand' => 0, 'price' => 55, 'special' => 45, 'rating' => 4.6, 'no_of_ratings' => 22, 'image' => 'media/prod-charger.png', 'tags' => 'charger,usb-c,gan,65w,fast-charge', 'warranty' => '18 Months'],
            ['slug' => 'linen-summer-jacket', 'en' => 'Linen Tailored Jacket', 'ar' => 'جاكيت كتان عصري', 'cat' => 1, 'seller' => 1, 'brand' => 2, 'price' => 135, 'special' => 110, 'rating' => 4.5, 'no_of_ratings' => 19, 'image' => 'media/prod-jacket.png', 'tags' => 'fashion,jacket,linen,menswear', 'warranty' => '14 Days Exchange'],
            ['slug' => 'cotton-polo-classic', 'en' => 'Premium Cotton Polo', 'ar' => 'قميص بولو قطن فاخر', 'cat' => 1, 'seller' => 1, 'brand' => 2, 'price' => 65, 'special' => 49, 'rating' => 4.4, 'no_of_ratings' => 16, 'image' => 'media/prod-polo.png', 'tags' => 'polo,cotton,casual,apparel', 'warranty' => '14 Days Exchange'],
            ['slug' => 'urban-running-sneakers', 'en' => 'Urban Comfort Sneakers', 'ar' => 'حذاء رياضي مريح للمشي', 'cat' => 5, 'seller' => 1, 'brand' => 2, 'price' => 120, 'special' => 95, 'rating' => 4.7, 'no_of_ratings' => 27, 'image' => 'media/prod-sneakers.png', 'tags' => 'sneakers,running,footwear,comfort', 'warranty' => '6 Months'],
            ['slug' => 'espresso-barista-maker', 'en' => 'Compact Espresso Maker', 'ar' => 'ماكينة إسبريسو منزلية', 'cat' => 2, 'seller' => 2, 'brand' => 1, 'price' => 310, 'special' => 265, 'rating' => 4.9, 'no_of_ratings' => 41, 'image' => 'media/prod-espresso.jpg', 'tags' => 'espresso,coffee,kitchen,appliance', 'warranty' => '24 Months'],
            ['slug' => 'ceramic-dinnerware-set', 'en' => 'Ceramic Dinnerware 16-Pc Set', 'ar' => 'طقم سفرة سيراميك 16 قطعة', 'cat' => 2, 'seller' => 2, 'brand' => 1, 'price' => 160, 'special' => 135, 'rating' => 4.3, 'no_of_ratings' => 14, 'image' => 'media/prod-ceramics.jpg', 'tags' => 'ceramic,dinnerware,kitchen,home', 'warranty' => '12 Months'],
            ['slug' => 'smart-desk-lamp-led', 'en' => 'Minimalist LED Desk Lamp', 'ar' => 'مصباح مكتب ذكي LED', 'cat' => 2, 'seller' => 2, 'brand' => 1, 'price' => 75, 'special' => 59, 'rating' => 4.6, 'no_of_ratings' => 24, 'image' => 'media/prod-lamp.png', 'tags' => 'lamp,led,lighting,desk,smart-home', 'warranty' => '12 Months'],
            ['slug' => 'sidr-organic-honey-500g', 'en' => 'Pure Sidr Honey 500g', 'ar' => 'عسل سدر عضوي 500 جم', 'cat' => 3, 'seller' => 3, 'brand' => 3, 'price' => 85, 'special' => 72, 'rating' => 4.8, 'no_of_ratings' => 33, 'image' => 'media/prod-honey.png', 'tags' => 'honey,sidr,organic,natural,grocery', 'warranty' => 'Freshness Guaranteed'],
            ['slug' => 'specialty-arabica-beans', 'en' => 'Specialty Arabica Coffee 1kg', 'ar' => 'حبوب قهوة أرابيكا مختصة 1 كجم', 'cat' => 3, 'seller' => 3, 'brand' => 3, 'price' => 58, 'special' => 48, 'rating' => 4.7, 'no_of_ratings' => 26, 'image' => 'media/prod-coffee.jpg', 'tags' => 'coffee,arabica,specialty,roast', 'warranty' => 'Freshness Guaranteed'],
            ['slug' => 'argan-botanical-serum', 'en' => 'Botanical Argan Care Serum', 'ar' => 'سيروم الأرغان الطبيعي للعناية', 'cat' => 4, 'seller' => 3, 'brand' => 3, 'price' => 68, 'special' => 54, 'rating' => 4.2, 'no_of_ratings' => 15, 'image' => 'media/prod-serum.png', 'tags' => 'argan,serum,skincare,organic,beauty', 'warranty' => '12 Months Shelf Life'],
        ];
        $variants = [];
        foreach ($productsData as $idx => $p) {
            $existingProd = DB::table('products')->where('slug', $p['slug'])->first();
            $sId = $sellerIds[$p['seller']];
            $catId = $categoryIds[$p['cat']];
            $brandId = $brandIds[$p['brand']];
            $specHtml = '<p><strong>' . e($p['en']) . ' (' . e($p['ar']) . ')</strong> — Verified MUQTARIN Store catalog item.</p>'
                . '<ul>'
                . '<li><strong>SKU:</strong> SKU-MUQ-' . ($idx + 101) . '</li>'
                . '<li><strong>Warranty / Guarantee:</strong> ' . e($p['warranty']) . '</li>'
                . '<li><strong>Quality Assurance:</strong> Inspected &amp; fulfilled by verified seller across MUQTARIN zones.</li>'
                . '</ul>';
            $extraHtml = '<p><strong>Care, Packaging &amp; Fulfillment Details:</strong></p>'
                . '<ul>'
                . '<li><strong>Box Contents:</strong> Original sealed packaging for ' . e($p['en']) . ' with seller invoice.</li>'
                . '<li><strong>Delivery Window:</strong> 24–48 hours inside Greater Cairo &amp; Giza; 2–4 days nationwide.</li>'
                . '<li><strong>Return Eligibility:</strong> Eligible for 30-day return or exchange in unused condition.</li>'
                . '</ul>';
            if ($existingProd) {
                $pId = $existingProd->id;
                DB::table('products')->where('id', $pId)->update([
                    'image' => $p['image'],
                    'other_images' => json_encode([$p['image']]),
                    'tags' => $p['tags'],
                    'warranty_period' => $p['warranty'],
                    'description' => $specHtml,
                    'extra_description' => $extraHtml,
                    'updated_at' => $now,
                ]);
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
                    'image' => $p['image'],
                    'other_images' => json_encode([$p['image']]),
                    'tags' => $p['tags'],
                    'warranty_period' => $p['warranty'],
                    'brand' => (string) $brandId,
                    'sku' => 'SKU-MUQ-' . ($idx + 101),
                    'stock' => 120,
                    'availability' => 1,
                    'rating' => $p['rating'],
                    'no_of_ratings' => $p['no_of_ratings'],
                    'description' => $specHtml,
                    'extra_description' => $extraHtml,
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
                DB::table('product_variants')->where('id', $vId)->update([
                    'images' => json_encode([$p['image']]),
                    'updated_at' => $now,
                ]);
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
                    'images' => json_encode([$p['image']]),
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
                'ar' => $p['ar'],
                'slug' => $p['slug'],
                'tags' => $p['tags'],
                'image' => $p['image'],
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
                    'order_type' => 'regular_order',
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

        // 12B. Media Library Records
        $mediaFiles = [
            ['file' => 'hero-editorial-1.jpg', 'name' => 'MUQTARIN Hero Editorial 1'],
            ['file' => 'hero-editorial-2.jpg', 'name' => 'MUQTARIN Hero Editorial 2'],
            ['file' => 'muqtarin-logo.svg', 'name' => 'MUQTARIN Primary Logo'],
            ['file' => 'muqtarin-icon.svg', 'name' => 'MUQTARIN Brand Icon'],
            ['file' => 'prod-earbuds.jpg', 'name' => 'Pro Wireless Earbuds X1'],
            ['file' => 'prod-smartwatch.jpg', 'name' => 'Smart Watch Emerald S4'],
            ['file' => 'prod-charger.png', 'name' => 'GaN Fast Charger 65W'],
            ['file' => 'prod-jacket.png', 'name' => 'Linen Tailored Jacket'],
            ['file' => 'prod-polo.png', 'name' => 'Premium Cotton Polo'],
            ['file' => 'prod-sneakers.png', 'name' => 'Urban Comfort Sneakers'],
            ['file' => 'prod-espresso.jpg', 'name' => 'Compact Espresso Maker'],
            ['file' => 'prod-ceramics.jpg', 'name' => 'Ceramic Dinnerware 16-Pc Set'],
            ['file' => 'prod-lamp.png', 'name' => 'Minimalist LED Desk Lamp'],
            ['file' => 'prod-honey.png', 'name' => 'Pure Sidr Honey 500g'],
            ['file' => 'prod-coffee.jpg', 'name' => 'Specialty Arabica Coffee 1kg'],
            ['file' => 'prod-serum.png', 'name' => 'Botanical Argan Care Serum'],
            ['file' => 'prod-headphones.jpg', 'name' => 'Studio Wireless Headphones'],
        ];
        foreach ($mediaFiles as $idx => $mf) {
            $fullPath = public_path('storage/media/' . $mf['file']);
            if (!File::exists($fullPath)) {
                continue;
            }
            $ext = strtolower(pathinfo($mf['file'], PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'png' => 'image/png',
                'svg' => 'image/svg+xml',
                default => 'image/jpeg',
            };
            $exists = DB::table('media')->where('file_name', $mf['file'])->first();
            if (!$exists) {
                DB::table('media')->insert([
                    'store_id' => $storeId,
                    'seller_id' => 0,
                    'name' => $mf['name'],
                    'extension' => $ext,
                    'type' => 'image',
                    'sub_directory' => 'media',
                    'size' => (string) File::size($fullPath),
                    'order_column' => $idx + 10,
                    'model_type' => 'App\\Models\\Media',
                    'model_id' => 1,
                    'file_name' => $mf['file'],
                    'disk' => 'public',
                    'conversions_disk' => 'public',
                    'collection_name' => 'media',
                    'mime_type' => $mime,
                    'custom_properties' => '[]',
                    'generated_conversions' => '[]',
                    'responsive_images' => '[]',
                    'manipulations' => '[]',
                    'uuid' => (string) Str::uuid(),
                    'object_url' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 12C. Taxes
        if (DB::table('taxes')->count() === 0) {
            $taxesData = [
                ['en' => 'Standard VAT', 'ar' => 'ضريبة القيمة المضافة', 'pct' => 14, 'status' => 1],
                ['en' => 'Reduced Rate', 'ar' => 'ضريبة مخفضة', 'pct' => 5, 'status' => 1],
                ['en' => 'Zero-Rated Tax', 'ar' => 'معفى من الضريبة', 'pct' => 0, 'status' => 1],
            ];
            foreach ($taxesData as $tx) {
                DB::table('taxes')->insert([
                    'title' => json_encode(['en' => $tx['en'], 'ar' => $tx['ar']], JSON_UNESCAPED_UNICODE),
                    'percentage' => $tx['pct'],
                    'status' => $tx['status'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 12D. Return Requests & Order Trackings
        $orderItemsAll = DB::table('order_items')->where('store_id', $storeId)->orderBy('id')->get();
        if (DB::table('return_requests')->count() === 0 && $orderItemsAll->isNotEmpty()) {
            $returnedItems = $orderItemsAll->whereIn('active_status', ['returned', 'delivered'])->take(3);
            $rrPlans = [
                ['status' => 3, 'remarks' => 'Customer requested size exchange; item returned and inspected.'],
                ['status' => 1, 'remarks' => 'Return request approved for pickup by MUQTARIN courier.'],
                ['status' => 0, 'remarks' => 'Customer reported packaging seal issue upon delivery.'],
            ];
            $rrIdx = 0;
            foreach ($returnedItems as $oi) {
                $variantRow = DB::table('product_variants')->where('id', $oi->product_variant_id)->first();
                if (!$variantRow) {
                    continue;
                }
                $plan = $rrPlans[$rrIdx % count($rrPlans)];
                DB::table('return_requests')->insert([
                    'user_id' => $oi->user_id,
                    'product_id' => $variantRow->product_id,
                    'product_variant_id' => $oi->product_variant_id,
                    'order_id' => $oi->order_id,
                    'order_item_id' => $oi->id,
                    'status' => $plan['status'],
                    'remarks' => $plan['remarks'],
                    'created_at' => $now->copy()->subDays(5 - $rrIdx),
                    'updated_at' => $now->copy()->subDays(4 - $rrIdx),
                ]);
                $rrIdx++;
            }
        }

        if (DB::table('order_trackings')->count() === 0 && $orderItemsAll->isNotEmpty()) {
            $shippedOrDelivered = $orderItemsAll->whereIn('active_status', ['shipped', 'delivered'])->take(6);
            foreach ($shippedOrDelivered as $idx => $oi) {
                $trackingId = 'MUQ-TRK-' . str_pad((string) $oi->order_id, 5, '0', STR_PAD_LEFT);
                DB::table('order_trackings')->insert([
                    'order_id' => $oi->order_id,
                    'order_item_id' => $oi->id,
                    'courier_agency' => $idx % 2 === 0 ? 'MUQTARIN Express' : 'Bosta Egypt',
                    'tracking_id' => $trackingId,
                    'url' => 'https://muqtarinstore.com/track/' . $trackingId,
                    'status' => $oi->active_status === 'delivered' ? 1 : 0,
                    'is_canceled' => 0,
                    'date' => $now->copy()->subDays(max(0, 6 - $idx))->toDateTimeString(),
                    'created_at' => $oi->created_at ?? $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 12E. Product Ratings, Product FAQs & Storefront FAQs (populate across all 12 products)
        if (!empty($variants) && !empty($customerIds)) {
            $sampleReviewPool = [
                ['rating' => 5, 'title' => 'جودة ممتازة وتوصيل سريع', 'comment' => 'المنتج مطابق تماماً للوصف والتغليف أنيق ومرتب جداً.'],
                ['rating' => 5, 'title' => 'Authentic & well packaged', 'comment' => 'Arrived on time in Cairo, sealed original packaging and reliable seller.'],
                ['rating' => 4, 'title' => 'قيمة رائعة مقابل السعر', 'comment' => 'تجربة شراء ممتازة وجودة الخامات عالية جداً مقارنة بالسعر.'],
                ['rating' => 5, 'title' => 'خدمة موثوقة من مقترن ستور', 'comment' => 'الطلب وصل خلال يومين مع متابعة دقيقة من المندوب. أنصح به بشدة.'],
                ['rating' => 4, 'title' => 'Solid everyday quality', 'comment' => 'Works smoothly out of the box and matches the photos on the store.'],
            ];
            $ratingPatterns = [
                [5, 5, 5, 5, 4], // 4.8
                [5, 5, 4, 5, 4], // 4.6
                [5, 4, 4, 5, 4], // 4.4
                [5, 4, 4, 3, 5], // 4.2
            ];
            foreach ($variants as $vIdx => $v) {
                $pid = $v['product_id'];
                $existingCount = DB::table('product_ratings')->where('product_id', $pid)->count();
                if ($existingCount < 5) {
                    $pattern = $ratingPatterns[$vIdx % count($ratingPatterns)];
                    for ($rIdx = $existingCount; $rIdx < 5; $rIdx++) {
                        $rev = $sampleReviewPool[$rIdx % count($sampleReviewPool)];
                        $cId = $customerIds[($vIdx + $rIdx) % count($customerIds)];
                        DB::table('product_ratings')->insert([
                            'user_id' => $cId,
                            'product_id' => $pid,
                            'rating' => $pattern[$rIdx],
                            'images' => json_encode([]),
                            'title' => $rev['title'],
                            'comment' => $rev['comment'],
                            'created_at' => $now->copy()->subDays(12 - $rIdx),
                            'updated_at' => $now->copy()->subDays(12 - $rIdx),
                        ]);
                    }
                }
            }
            // Normalize fractional ratings to whole stars (1..5) so star breakdown mean matches product rating
            $nonIntegerRatings = DB::table('product_ratings')->get(['id', 'rating']);
            foreach ($nonIntegerRatings as $r) {
                $rounded = max(1, min(5, (int) round((float) $r->rating)));
                if ((float) $r->rating !== (float) $rounded) {
                    DB::table('product_ratings')->where('id', $r->id)->update([
                        'rating' => $rounded,
                        'updated_at' => $now,
                    ]);
                }
            }
            // Always keep products.rating & products.no_of_ratings 100% consistent with product_ratings across all rated products
            $allStats = DB::table('product_ratings')
                ->selectRaw('product_id, COUNT(*) as cnt, COALESCE(AVG(rating), 0) as avg_rating')
                ->groupBy('product_id')
                ->get();
            foreach ($allStats as $stats) {
                DB::table('products')->where('id', $stats->product_id)->update([
                    'no_of_ratings' => (int) ($stats->cnt ?? 0),
                    'rating' => round((float) ($stats->avg_rating ?? 0), 1),
                    'updated_at' => $now,
                ]);
            }

            $faqTemplates = [
                [
                    'q' => 'هل المنتج أصلي ومشمول بالضمان؟',
                    'a' => 'نعم، جميع منتجات متجر مقترن أصلية ومعتمدة ومشمولة بالضمان وسياسة الاسترجاع خلال 30 يوماً.',
                ],
                [
                    'q' => 'كم تستغرق مدة التوصيل داخل القاهرة والجيزة؟',
                    'a' => 'يتم التوصيل خلال 24 إلى 48 ساعة عمل مع إمكانية الدفع عند الاستلام.',
                ],
                [
                    'q' => 'Is cash on delivery available for this item?',
                    'a' => 'Yes, Cash on Delivery (COD) is supported across all active MUQTARIN delivery zones.',
                ],
            ];
            foreach ($variants as $vIdx => $v) {
                if (DB::table('product_faqs')->where('product_id', $v['product_id'])->count() > 0) {
                    continue;
                }
                foreach ($faqTemplates as $fIdx => $fq) {
                    DB::table('product_faqs')->insert([
                        'user_id' => $customerIds[($vIdx + $fIdx) % count($customerIds)],
                        'seller_id' => $v['seller_id'],
                        'product_id' => $v['product_id'],
                        'votes' => 4 + $fIdx,
                        'question' => $fq['q'],
                        'answer' => $fq['a'],
                        'answered_by' => 1,
                        'created_at' => $now->copy()->subDays(12 - $fIdx),
                        'updated_at' => $now->copy()->subDays(11 - $fIdx),
                    ]);
                }
            }
        }

        // 12G. Storefront FAQs (/faqs)
        if (DB::table('faqs')->count() === 0) {
            $storeFaqs = [
                [
                    'question' => 'كم تستغرق مدة الشحن والتوصيل؟ / How long does delivery take?',
                    'answer' => "يتم توصيل الطلبات داخل القاهرة والجيزة خلال 24–48 ساعة عمل، وباقي المحافظات خلال 2–4 أيام عمل.\nOrders within Cairo & Giza arrive in 24–48 business hours; other governorates take 2–4 business days.",
                ],
                [
                    'question' => 'ما هي سياسة الاسترجاع والاستبدال؟ / What is your return policy?',
                    'answer' => "يمكنك إرجاع أو استبدال المنتجات المؤهلة خلال 30 يوماً من تاريخ الاستلام بحالتها الأصلية مع التغليف الكامل.\nEligible items may be returned or exchanged within 30 days of delivery in original unused condition.",
                ],
                [
                    'question' => 'ما طرق الدفع المتاحة في مقترن ستور؟ / Which payment methods are accepted?',
                    'answer' => "ندعم الدفع عند الاستلام (COD)، والبطاقات البنكية، ورصيد محفظة مقترن.\nWe support Cash on Delivery (COD), major cards, and MUQTARIN wallet balance.",
                ],
                [
                    'question' => 'كيف يمكنني تتبع طلبي؟ / How can I track my order?',
                    'answer' => "يمكنك متابعة حالة الطلب ورقم التتبع مباشرة من حسابك في قسم الطلبات أو عبر رابط التتبع المرسل في رسالة التأكيد.\nTrack your shipment anytime from My Account → Orders using your MUQ-TRK tracking number.",
                ],
                [
                    'question' => 'هل جميع البائعين والمنتجات موثقون؟ / Are all sellers and products verified?',
                    'answer' => "نعم، يخضع كل بائع في مقترن ستور لمراجعة المستندات التجارية وضمان جودة المنتجات قبل النشر.\nYes, every MUQTARIN seller completes verification and quality checks before listing products.",
                ],
                [
                    'question' => 'متى أحصل على توصيل مجاني؟ / When do I qualify for free shipping?',
                    'answer' => "الشحن مجاني للطلبات التي تتجاوز 250 ج.م / \$250 حسب نطاق التوصيل الخاص بك.\nOrders above the free-delivery threshold (\$250 in Greater Cairo) qualify automatically at checkout.",
                ],
            ];
            foreach ($storeFaqs as $idx => $sf) {
                DB::table('faqs')->insert([
                    'question' => $sf['question'],
                    'answer' => $sf['answer'],
                    'status' => 1,
                    'created_at' => $now->copy()->subDays(6 - $idx),
                    'updated_at' => $now,
                ]);
            }
        }

        // 12F. Ticket Messages for Support Tickets
        if (DB::table('ticket_messages')->count() === 0) {
            $allTickets = DB::table('tickets')->orderBy('id')->get();
            foreach ($allTickets as $idx => $tk) {
                $baseTs = Carbon::parse($tk->created_at ?? $now);
                DB::table('ticket_messages')->insert([
                    [
                        'user_type' => 'user',
                        'user_id' => $tk->user_id,
                        'ticket_id' => $tk->id,
                        'message' => $tk->description ?: 'أرجو مراجعة الطلب وتأكيد التحديث.',
                        'attachments' => json_encode([]),
                        'disk' => 'public',
                        'created_at' => $baseTs,
                        'updated_at' => $baseTs,
                    ],
                    [
                        'user_type' => 'admin',
                        'user_id' => 1,
                        'ticket_id' => $tk->id,
                        'message' => 'أهلاً بك في دعم مقترن ستور. تم استلام التذكرة وجارٍ المتابعة مع الفريق المختص.',
                        'attachments' => json_encode([]),
                        'disk' => 'public',
                        'created_at' => $baseTs->copy()->addMinutes(25),
                        'updated_at' => $baseTs->copy()->addMinutes(25),
                    ],
                ]);
            }
        }

        // 13. Hero Sliders & Branded Platform Settings
        $sliderRows = DB::table('sliders')->where('store_id', $storeId)->orderBy('id')->get();
        $heroAssets = ['media/hero-editorial-1.jpg', 'media/hero-editorial-2.jpg'];
        if ($sliderRows->isEmpty()) {
            foreach ($heroAssets as $idx => $heroImg) {
                DB::table('sliders')->insert([
                    'store_id' => $storeId,
                    'type' => 'categories',
                    'type_id' => $categoryIds[$idx % count($categoryIds)] ?? 1,
                    'link' => '',
                    'image' => $heroImg,
                    'web_image' => $heroImg,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        } else {
            foreach ($sliderRows as $idx => $sl) {
                $heroImg = $heroAssets[$idx % count($heroAssets)];
                DB::table('sliders')->where('id', $sl->id)->update([
                    'image' => $heroImg,
                    'web_image' => $heroImg,
                    'updated_at' => $now,
                ]);
            }
        }

        $sysRow = DB::table('settings')->where('variable', 'system_settings')->first();
        $sysVal = $sysRow ? (json_decode($sysRow->value, true) ?: []) : [];
        $sysVal = array_merge($sysVal, [
            'app_name' => 'MUQTARIN Store',
            'support_number' => '+20 100 245 8810',
            'support_email' => 'support@muqtarinstore.com',
            'country_code' => '20',
            'logo' => '/media/muqtarin-logo.svg',
            'favicon' => '/media/muqtarin-icon.svg',
            'system_timezone' => 'Africa/Cairo',
            'max_days_to_return_item' => '30',
            'tax_name' => 'VAT',
            'tax_number' => 'EG-100-482-910',
            'wallet_balance_amount' => '50',
            'current_version_of_android_app' => '1.2.0',
            'current_version_of_ios_app' => '1.2.0',
            'current_version_of_android_app_for_seller' => '1.2.0',
            'current_version_of_ios_app_for_seller' => '1.2.0',
            'current_version_of_android_app_for_delivery_boy' => '1.2.0',
            'current_version_of_ios_app_for_delivery_boy' => '1.2.0',
            'play_store_link_for_customer_app' => 'https://play.google.com/store/apps/details?id=com.muqtarin.store',
            'app_store_link_for_customer_app' => 'https://apps.apple.com/app/muqtarin-store/id1000000001',
            'play_store_link_for_seller_app' => 'https://play.google.com/store/apps/details?id=com.muqtarin.seller',
            'app_store_link_for_seller_app' => 'https://apps.apple.com/app/muqtarin-seller/id1000000002',
            'play_store_link_for_delivery_boy_app' => 'https://play.google.com/store/apps/details?id=com.muqtarin.delivery',
            'app_store_link_for_delivery_boy_app' => 'https://apps.apple.com/app/muqtarin-delivery/id1000000003',
            'deep_link_scheme' => 'muqtarin',
            'deep_link_host' => 'muqtarinstore.com',
            'android_package_name' => 'com.muqtarin.store',
            'message_for_customer_app' => 'We are upgrading MUQTARIN Store to serve you better. Back shortly.',
            'message_for_seller_app' => 'Seller portal maintenance is in progress. Your orders remain safe.',
            'message_for_delivery_boy_app' => 'Courier dispatch maintenance is in progress. Please stay tuned.',
        ]);
        DB::table('settings')->updateOrInsert(
            ['variable' => 'system_settings'],
            ['value' => json_encode($sysVal, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'updated_at' => $now]
        );

        // Clean Payment & Shipping Method settings (valid JSON, no legacy sandbox tokens)
        $paymentConfig = [
            'phonepe_method' => 0,
            'phonepe_mode' => 'sandbox',
            'phonepe_marchant_id' => '',
            'phonepe_salt_index' => '1',
            'phonepe_salt_key' => '',
            'paypal_method' => 0,
            'paypal_mode' => 'sandbox',
            'paypal_business_email' => '',
            'paypal_client_id' => '',
            'currency_code' => 'USD',
            'razorpay_method' => 0,
            'razorpay_mode' => 'sandbox',
            'razorpay_key_id' => '',
            'razorpay_secret_key' => '',
            'razorpay_webhook_secret_key' => '',
            'midtrans_method' => 0,
            'midtrans_payment_mode' => 'sandbox',
            'midtrans_client_key' => '',
            'midtrans_server_key' => '',
            'midtrans_merchant_id' => '',
            'paystack_method' => 0,
            'paystack_key_id' => '',
            'paystack_secret_key' => '',
            'stripe_method' => 0,
            'stripe_payment_mode' => 'test',
            'stripe_publishable_key' => '',
            'stripe_secret_key' => '',
            'stripe_webhook_secret_key' => '',
            'stripe_currency_code' => 'USD',
            'flutterwave_method' => 0,
            'flutterwave_public_key' => '',
            'flutterwave_secret_key' => '',
            'flutterwave_encryption_key' => '',
            'flutterwave_currency_code' => 'USD',
            'flutterwave_webhook_secret_key' => '',
            'fatoorah_method' => 0,
            'myfatoorah_token' => '',
            'myfatoorah_payment_mode' => 'test',
            'myfatoorah_language' => 'ar',
            'myfatoorah__webhook_url' => '',
            'myfatoorah_country' => 'EGY',
            'myfatoorah__successUrl' => '',
            'myfatoorah__errorUrl' => '',
            'myfatoorah__secret_key' => '',
            'direct_bank_transfer_method' => 0,
            'account_name' => 'MUQTARIN Store',
            'account_number' => '',
            'bank_name' => '',
            'bank_code' => '',
            'notes' => '',
            'cod_method' => 1,
        ];
        DB::table('settings')->updateOrInsert(
            ['variable' => 'payment_method'],
            ['value' => json_encode($paymentConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'updated_at' => $now]
        );

        $shippingConfig = [
            'local_shipping_method' => 1,
            'shiprocket_shipping_method' => 0,
            'email' => '',
            'password' => '',
            'webhook_token' => '',
            'standard_shipping_free_delivery' => 1,
            'minimum_free_delivery_order_amount' => '250',
        ];
        DB::table('settings')->updateOrInsert(
            ['variable' => 'shipping_method'],
            ['value' => json_encode($shippingConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'updated_at' => $now]
        );

        $webRow = DB::table('settings')->where('variable', 'web_settings')->first();
        $webVal = $webRow ? (json_decode($webRow->value, true) ?: []) : [];
        $webVal = array_merge($webVal, [
            'site_title' => 'MUQTARIN Store',
            'support_number' => '+20 100 245 8810',
            'support_email' => 'support@muqtarinstore.com',
            'copyright_details' => 'Copyright © 2026 MUQTARIN Store. All Rights Reserved.',
            'logo' => 'media/muqtarin-logo.svg',
            'favicon' => 'media/muqtarin-icon.svg',
            'address' => '15 Tahrir Square, Downtown, Cairo, Egypt',
            'app_short_description' => 'Curated multi-vendor marketplace by MUQTARIN Store — fast delivery & verified quality.',
            'map_iframe' => '<iframe src="https://maps.google.com/maps?q=Tahrir%20Square,Cairo,Egypt&t=&z=14&ie=UTF8&iwloc=&output=embed" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
            'meta_keywords' => 'MUQTARIN Store, multi-vendor marketplace, electronics, fashion, home living, Cairo',
            'meta_description' => 'Shop verified sellers across electronics, fashion, home living, and organic goods at MUQTARIN Store.',
            'app_download_section_title' => 'Shop Faster With the MUQTARIN App',
            'app_download_section_tagline' => 'Track orders, chat with sellers, and unlock app-only offers',
            'app_download_section_short_description' => 'Download MUQTARIN Store for iOS and Android to manage deliveries and rewards on the go.',
            'app_download_section_playstore_url' => 'https://play.google.com/store/apps/details?id=com.muqtarin.store',
            'app_download_section_appstore_url' => 'https://apps.apple.com/app/muqtarin-store/id1000000001',
            'twitter_link' => 'https://x.com/muqtarinstore',
            'facebook_link' => 'https://www.facebook.com/muqtarinstore',
            'instagram_link' => 'https://www.instagram.com/muqtarinstore',
            'youtube_link' => 'https://www.youtube.com/@muqtarinstore',
            'shipping_description' => 'Free delivery on eligible orders over $250',
            'return_description' => '30-day free return & exchange policy',
        ]);
        DB::table('settings')->updateOrInsert(
            ['variable' => 'web_settings'],
            ['value' => json_encode($webVal, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'updated_at' => $now]
        );

        $fbRow = DB::table('settings')->where('variable', 'firebase_settings')->first();
        if ($fbRow) {
            $fbVal = json_decode($fbRow->value, true) ?: [];
            if (!empty($fbVal['storageBucket']) && str_contains($fbVal['storageBucket'], 'ezeemart')) {
                $fbVal['storageBucket'] = 'muqtarin-store.firebasestorage.app';
                DB::table('settings')->where('variable', 'firebase_settings')->update([
                    'value' => json_encode($fbVal, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => $now,
                ]);
            }
        }

        $pwaRow = DB::table('settings')->where('variable', 'pwa_settings')->first();
        $pwaVal = $pwaRow ? (json_decode($pwaRow->value, true) ?: []) : [];
        $pwaVal = array_merge($pwaVal, [
            'name' => 'MUQTARIN Store',
            'short_name' => 'MUQTARIN',
            'theme_color' => '#0F4C3A',
            'background_color' => '#F8FAF9',
            'description' => 'Curated multi-vendor marketplace by MUQTARIN Store — fast delivery & verified quality.',
            'logo' => '/media/muqtarin-icon.svg',
        ]);
        DB::table('settings')->updateOrInsert(
            ['variable' => 'pwa_settings'],
            ['value' => json_encode($pwaVal, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'updated_at' => $now]
        );

        if (\Illuminate\Support\Facades\Schema::hasTable('seo_settings')) {
            DB::table('seo_settings')
                ->where('seo_type', 'global')
                ->where('reference_id', 0)
                ->update(['reference_id' => null, 'updated_at' => $now]);

            if (DB::table('seo_settings')->where('seo_type', 'global')->whereNull('reference_id')->count() === 0) {
                DB::table('seo_settings')->insert([
                    'seo_type' => 'global',
                    'reference_id' => null,
                    'meta_title' => 'MUQTARIN Store',
                    'meta_description' => 'Shop verified sellers across electronics, fashion, home living, and organic goods at MUQTARIN Store.',
                    'meta_keywords' => 'MUQTARIN Store, marketplace, Cairo, electronics, fashion',
                    'og_title' => 'MUQTARIN Store',
                    'og_description' => 'Curated multi-vendor marketplace by MUQTARIN Store.',
                    'og_type' => 'website',
                    'twitter_card' => 'summary_large_image',
                    'robots' => 'index,follow',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($variants as $v) {
                if (DB::table('seo_settings')->where('seo_type', 'product')->where('reference_id', $v['product_id'])->count() === 0) {
                    DB::table('seo_settings')->insert([
                        'seo_type' => 'product',
                        'reference_id' => $v['product_id'],
                        'meta_title' => $v['name'] . ' | MUQTARIN Store',
                        'meta_description' => 'Buy ' . $v['name'] . ' (' . $v['ar'] . ') from verified sellers at MUQTARIN Store with fast delivery and 30-day returns.',
                        'meta_keywords' => $v['tags'] . ',MUQTARIN Store',
                        'og_title' => $v['name'] . ' — MUQTARIN Store',
                        'og_description' => 'Verified catalog product at MUQTARIN Store with Cash on Delivery and buyer protection.',
                        'og_type' => 'product',
                        'twitter_card' => 'summary_large_image',
                        'robots' => 'index,follow',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            foreach ($categoriesData as $cIdx => $cat) {
                $catId = $categoryIds[$cIdx] ?? null;
                if ($catId && DB::table('seo_settings')->where('seo_type', 'category')->where('reference_id', $catId)->count() === 0) {
                    DB::table('seo_settings')->insert([
                        'seo_type' => 'category',
                        'reference_id' => $catId,
                        'meta_title' => $cat['en'] . ' | MUQTARIN Store',
                        'meta_description' => 'Explore curated ' . $cat['en'] . ' (' . $cat['ar'] . ') from trusted local sellers at MUQTARIN Store.',
                        'meta_keywords' => $cat['slug'] . ',' . $cat['en'] . ',MUQTARIN Store',
                        'og_title' => $cat['en'] . ' — MUQTARIN Store',
                        'og_description' => 'Shop top-rated ' . $cat['en'] . ' with reliable delivery across Egypt and MENA.',
                        'og_type' => 'website',
                        'twitter_card' => 'summary_large_image',
                        'robots' => 'index,follow',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        $policyDefaults = [
            'privacy_policy' => '<h4>MUQTARIN Store Privacy Policy</h4><p>MUQTARIN Store (مقترن ستور) is committed to protecting your personal data. We collect only the details required to process orders, verify accounts, coordinate courier deliveries, and prevent fraudulent activity.</p><h5>1. Information We Collect</h5><p>When you register, browse, or place an order, we store your name, contact number, email address, delivery addresses, and order history. Payment card numbers are processed through certified gateways and are never stored in plain text on our servers.</p><h5>2. How We Use Your Data</h5><p>Your information is shared strictly with the fulfilling verified seller and assigned delivery partner to complete shipment, issue invoices, and support 30-day return requests. You may request account review or deletion anytime via support@muqtarinstore.com.</p>',
            'return_policy' => '<h4>MUQTARIN Store Return &amp; Refund Policy</h4><p>Customer satisfaction is central to MUQTARIN Store. Eligible items purchased on the marketplace may be returned or exchanged within <strong>30 days</strong> of delivery.</p><h5>1. Eligibility Criteria</h5><p>Returned products must be unused, unwashed, and enclosed in their original packaging with all accessories, manuals, and seller invoice intact. Perishable organic groceries and opened personal-care seals are eligible only if damaged or defective on arrival.</p><h5>2. Pickup &amp; Refund Timeline</h5><p>Submit a return request from your order details page. Once approved, a MUQTARIN courier collects the item within 48 hours. After inspection, refunds are credited within 3–5 business days to your original payment method or instantly to your MUQTARIN wallet.</p>',
            'shipping_policy' => '<h4>MUQTARIN Store Shipping &amp; Delivery Policy</h4><p>We partner with trusted local and regional couriers to deliver curated marketplace orders quickly and safely across all active service zones.</p><h5>1. Dispatch &amp; Transit Times</h5><p>Verified sellers dispatch confirmed orders within 24 business hours. Standard delivery takes 24–48 hours inside Greater Cairo &amp; Giza, and 2–4 business days across Alexandria and other governorates.</p><h5>2. Free Delivery &amp; Order Tracking</h5><p>Orders meeting the zone minimum threshold qualify automatically for free standard delivery. Real-time tracking codes (MUQ-TRK) and Cash-on-Delivery OTP verification keep every handoff transparent.</p>',
            'terms_and_conditions' => '<h4>MUQTARIN Store Terms &amp; Conditions</h4><p>Welcome to MUQTARIN Store (مقترن ستور). By creating an account or placing an order through our web storefront or mobile apps, you agree to comply with these marketplace terms.</p><h5>1. Accounts &amp; Orders</h5><p>Customers must provide accurate contact and delivery information. Prices include applicable VAT where marked and remain confirmed once an order is accepted by the seller.</p><h5>2. Buyer Protection &amp; Disputes</h5><p>All purchases carry buyer protection covering damaged, counterfeit, or non-delivered items, backed by our 30-day return window and dedicated support team at support@muqtarinstore.com.</p>',
            'about_us' => '<h4>About MUQTARIN Store</h4><p>MUQTARIN Store (مقترن ستور) is a bilingual multi-vendor marketplace connecting trusted Egyptian and regional merchants with shoppers seeking verified quality across electronics, fashion, home living, and organic goods.</p><h5>1. Curated &amp; Verified Sellers</h5><p>Every merchant completes identity, tax, and catalog verification before going live, ensuring authentic items, transparent pricing, and reliable fulfillment.</p><h5>2. Built for Seamless Shopping</h5><p>With instant Arabic/English switching, Cash on Delivery, real-time courier tracking, and a 30-day return guarantee, MUQTARIN Store delivers confidence from checkout to doorstep.</p>',
            'admin_privacy_policy' => '<h4>Admin Portal Privacy Policy</h4><p>Administrative accounts on MUQTARIN Store operate under strict confidentiality and least-privilege access controls across all tenant stores.</p><h5>1. Access &amp; Audit Logging</h5><p>Administrators access customer, seller KYC, payout, and order dispute records solely for operational governance, compliance review, and fraud prevention.</p><h5>2. Data Handling</h5><p>Exporting personal contact details outside authorized administrative workflows is prohibited under MUQTARIN data-protection policy.</p>',
            'admin_terms_and_conditions' => '<h4>Admin Portal Terms &amp; Conditions</h4><p>Platform administrators are responsible for maintaining accurate catalog approvals, verified seller onboarding, and fair dispute resolution.</p><h5>1. Operational Governance</h5><p>Administrators must review flagged listings, return escalations, and wallet settlement batches within target SLAs while preserving impartial marketplace standards.</p><h5>2. Account Security</h5><p>Portal credentials must never be shared and all sessions should be locked when unattended.</p>',
            'seller_privacy_policy' => '<h4>Seller Privacy Policy</h4><p>MUQTARIN Store safeguards seller commercial registration, tax IDs, bank settlement accounts, and store analytics.</p><h5>1. Commercial Confidentiality</h5><p>Financial settlement data and verification documents are used strictly for KYC compliance, commission accounting, and scheduled payouts.</p><h5>2. Customer Data Limits</h5><p>Sellers receive customer shipping details solely to pack, invoice, and dispatch confirmed orders.</p>',
            'seller_terms_and_conditions' => '<h4>Seller Terms &amp; Conditions</h4><p>Approved merchants on MUQTARIN Store commit to listing authentic merchandise, maintaining accurate stock counts, and honoring published warranties.</p><h5>1. Fulfillment &amp; Returns</h5><p>Sellers must hand over confirmed orders within 24 business hours and honor MUQTARIN’s 30-day return policy for eligible items.</p><h5>2. Commissions &amp; Payouts</h5><p>Marketplace commissions are calculated transparently per category and settled to verified seller accounts upon order completion.</p>',
            'delivery_boy_privacy_policy' => '<h4>Courier Privacy Policy</h4><p>MUQTARIN Store collects courier identity documents, active zone assignments, and route telemetry solely to coordinate deliveries and settle bonuses.</p><h5>1. Route &amp; Contact Privacy</h5><p>Customer phone numbers and delivery locations visible in the courier app may only be used to complete active dispatches and OTP confirmation.</p>',
            'delivery_boy_terms_and_conditions' => '<h4>Courier Terms &amp; Conditions</h4><p>Delivery partners represent MUQTARIN Store at the customer’s doorstep and must uphold safe package handling and courteous service.</p><h5>1. OTP &amp; COD Settlement</h5><p>Couriers must verify delivery OTPs where enabled, collect exact Cash-on-Delivery amounts, and reconcile collected cash on schedule to receive bonus payouts.</p>',
        ];
        foreach ($policyDefaults as $varName => $html) {
            $row = DB::table('settings')->where('variable', $varName)->first();
            $rawVal = $row ? (string) $row->value : '';
            $decoded = $row ? (json_decode($rawVal, true) ?: []) : [];
            $currentText = is_array($decoded) ? trim((string) ($decoded[$varName] ?? '')) : '';
            $needsClean = !$row
                || $currentText === ''
                || mb_strlen(strip_tags($currentText)) < 350
                || preg_match('/14\s*days|ezeemart|eshop|cityecommerce/i', $rawVal)
                || substr_count($currentText, '<h5') > 4;
            if ($needsClean) {
                DB::table('settings')->updateOrInsert(
                    ['variable' => $varName],
                    [
                        'value' => json_encode([$varName => $html], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }

    private function ensureDemoImages(): void
    {
        $iconSrc = public_path('assets/brand/icon.jpg');
        $logoSrc = public_path('assets/brand/logo.jpg');
        $iconSvgSrc = public_path('assets/brand/icon.svg');
        $logoSvgSrc = public_path('assets/brand/logo.svg');
        if (!File::exists($iconSrc)) {
            return;
        }
        $bannerSrc = File::exists($logoSrc) ? $logoSrc : $iconSrc;

        $targets = [
            public_path('storage/media/demo-thumb.jpg') => $iconSrc,
            public_path('storage/media/demo-banner.jpg') => $bannerSrc,
            public_path('storage/sellers/demo-seller.jpg') => $iconSrc,
            public_path('storage/sellers/demo-banner.jpg') => $bannerSrc,
            public_path('storage/user_image/demo-avatar.jpg') => $iconSrc,
        ];

        if (File::exists($logoSvgSrc)) {
            $targets[public_path('storage/media/muqtarin-logo.svg')] = $logoSvgSrc;
        }
        if (File::exists($iconSvgSrc)) {
            $targets[public_path('storage/media/muqtarin-icon.svg')] = $iconSvgSrc;
        }

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
