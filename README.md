# Mo7i Store (eShop Plus Multi-Vendor)

مشروع متجر إلكتروني متعدد التجار والتطبيقات (eShop Plus v1.1.2).

---

## 📁 هيكلية المشروع (Project Structure)

```
mo7i-store/
├── backend/                  # مشروع Laravel الكامل (يشمل: لوحة تحكم الإدارة + متجر الويب + واجهات API للتطبيقات)
│   ├── app/                  # النماذج والمتحكمات (Controllers, Models, APIs)
│   ├── routes/               # مسارات الويب و API
│   ├── resources/            # واجهات Blade و JavaScript/React
│   ├── public/               # الملفات العامة (Assets, CSS, JS)
│   ├── vendor/               # حزم Composer
│   ├── node_modules/         # حزم NPM
│   └── .env                  # إعدادات البيئة وقاعدة البيانات
│
├── mobile-apps/              # تطبيقات الهواتف الذكية (Flutter)
│   ├── customer/             # تطبيق العملاء (Flutter Customer App)
│   ├── seller/               # تطبيق البائعين / التجار (Flutter Seller App)
│   └── deliveryboy/          # تطبيق مندوب التوصيل (Flutter Delivery Boy App)
│
├── database/                 # قاعدة البيانات
│   └── eshop_plus.sql        # ملف قاعدة البيانات الأصلي الجاهز للاستيراد
│
└── docs/                     # التوثيق والكتالوجات
    ├── Eshop Plus Documentation/ # دليل التثبيت والاستخدام الكامل (HTML/Assets)
    └── changeLog_1.1.2.pdf       # سجل التحديثات للإصدار 1.1.2
```

---

## 🛠 المتطلبات الأساسية للتشغيل

- **الواجهة الخلفية (Backend):**
  - PHP >= 8.1
  - MySQL / MariaDB >= 10.4
  - Apache / Nginx
  - Extensions: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML, GD/Imagick

- **تطبيقات الموبايل (Flutter Apps):**
  - Flutter SDK 3.x
  - Dart SDK
  - Android Studio / Xcode
