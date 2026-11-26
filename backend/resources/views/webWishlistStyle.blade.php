<!DOCTYPE html>
<html lang="en" class="overflow-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist Styles - Showcase Only</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/plugins.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/vendor/photoswipe.min.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/bootstrap-table.min.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/style.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/theme.min.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/star-rating.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/star-rating.min.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/intlTelInput.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/select2.min.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/iziToast.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/daterangepicker.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/responsive.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/shareon.min.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/app.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/swiper-bundle.min.css') }}?v={{ $version }}">
    @include('partials.webShowcaseBridge')
</head>

<body>
    <div id="wishlist_display_style_for_web_1" class="content-section">
        <div class="tab-container">
            <div id="regular-wishlist" class="tab-content" wire:ignore.self="" style="display: block;">
                <table class="table align-middle text-center order-table">
                    <thead>
                        <tr class="table-head text-nowrap">
                            <th scope="col"></th>
                            <th scope="col" data-en="Image" data-ar="الصورة">Image</th>
                            <th scope="col" data-en="Product Details" data-ar="تفاصيل المنتج">Product Details</th>
                            <th scope="col" data-en="Price" data-ar="السعر">Price</th>
                            <th scope="col" data-en="Rating" data-ar="التقييم">Rating</th>
                            <th scope="col" data-en="Action" data-ar="الإجراء">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <button class="btn-close remove-favorite" data-product-id="816"></button>
                            </td>
                            <td>
                                <img class="blur-up lazyloaded"
                                    data-src="{{ asset('storage/system_images/wishlist_display_style.jpeg') }}"
                                    src="{{ asset('storage/system_images/wishlist_display_style.jpeg') }}"
                                    alt="Kidney Beans" title="Kidney Beans">
                            </td>
                            <td><span class="name" data-en="Kidney Beans" data-ar="فاصولياء حمراء">Kidney Beans</span></td>
                            <td>
                                <span class="price fw-500"> $20.00 - $10.00</span>
                            </td>
                            <td><span class="id">★ 4.7</span></td>
                            <td>
                                <div class="btn btn-md text-nowrap" data-en="Add to Cart" data-ar="أضف للسلة">Add to Cart</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="wishlist_display_style_for_web_2" class="content-section d-none">
        <div id="regular-wishlist" class="tab-content" wire:ignore.self="" style="display: block;">
            <div class="grid-products grid-view-items mt-4">
                <div class="col-row row row-cols-2 row-cols-lg-4 row-cols-md-3 row-cols-xl-5 justify-content-center">
                    <div class="item col-item">
                        <div class="product-box position-relative">
                            <button type="button" class="btn remove-icon close-btn remove-favorite"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Remove"
                                aria-label="Remove" data-product-id="816"><ion-icon
                                    name="close-outline" role="img" class="md hydrated"></ion-icon></button>
                            <div class="product-image">
                                <a href="#!" onclick="return false;"
                                    class="all-product-img product-img rounded-3 slider-link">
                                    <img class="blur-up lazyloaded"
                                        src="{{ asset('storage/system_images/wishlist_display_style.jpeg') }}"
                                        alt="Product" title="Kidney Beans" width="625" height="808">
                                </a>
                            </div>
                            <div class="product-details text-center">
                                <div class="product-name text-ellipsis">
                                    <a href="#!" onclick="return false;" data-en="Kidney Beans" data-ar="فاصولياء حمراء">Kidney Beans</a>
                                </div>
                                <div class="product-price">
                                    <span class="price fw-500"><span>$100.00</span></span>
                                </div>
                                <div class="button-action mt-3">
                                    <div class="addtocart-btn">
                                        <div class="btn btn-md text-nowrap" data-en="Add to Cart" data-ar="أضف للسلة">Add to Cart</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- WISHLIST STYLE 3: MUQTARIN Card Grid -->
    <div id="wishlist_display_style_for_web_3" class="content-section d-none">
        <div class="mq-showcase-stage">
            <div class="row g-3 justify-content-center">
                @foreach ([
                    ['en' => 'Kidney Beans Reserve', 'ar' => 'فاصولياء عضوية فاخرة', 'img' => 'wishlist_display_style.jpeg', 'price' => '$18.00'],
                    ['en' => 'ChocoLuxe Muffins', 'ar' => 'مافن شوكولا فاخر', 'img' => 'product_card_style.jpeg', 'price' => '$40.00'],
                ] as $item)
                    <div class="col-6 col-md-4">
                        <div class="mq-card">
                            <div class="mq-card-media">
                                <img src="{{ asset('storage/system_images/' . $item['img']) }}" alt="{{ $item['en'] }}">
                                <div class="mq-card-actions">
                                    <button type="button" class="mq-card-action-btn" aria-label="Remove"><i class="bx bx-x"></i></button>
                                </div>
                            </div>
                            <div class="mq-card-body">
                                <div class="mq-card-category" data-en="Saved Item" data-ar="محفوظ في المفضلة">Saved Item</div>
                                <div class="mq-card-title" data-en="{{ $item['en'] }}" data-ar="{{ $item['ar'] }}">{{ $item['en'] }}</div>
                                <div class="mq-card-footer">
                                    <span class="mq-price-now">{{ $item['price'] }}</span>
                                    <button type="button" class="mq-add-btn">
                                        <i class="bx bx-shopping-bag"></i>
                                        <span data-en="Move to Cart" data-ar="أضف للسلة">Move to Cart</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</body>

<script src="{{ asset('frontend/elegant/js/plugins.js') }}?v={{ $version }}"></script>
<script src="{{ asset('frontend/elegant/js/swiper-bundle.min.js') }}?v={{ $version }}"></script>
<script type="module" src="{{ asset('frontend/elegant/js/ionicons.js') }}?v={{ $version }}"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js?v={{ $version }}" defer></script>

</html>
