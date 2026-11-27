 <!--   Core JS Files   -->
 <script src="{{ asset('/assets/admin/js/jquery.min.js') }}"></script>
 <script src="{{ asset('/assets/admin/js/jquery.js') }}"></script>
 <script src="{{ asset('/assets/js/core/popper.min.js') }}"></script>

 <script src="{{ asset('/assets/js/core/bootstrap.js') }}"></script>

 <script src="{{ asset('/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
 <script src="{{ asset('/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
 <script src="{{ asset('/assets/js/plugins/apexcharts.js') }}"></script>
 <script src="{{ asset('assets/admin/js/iziToast.min.js') }}"></script>
 <script src="{{ asset('assets/brand/muqtarin-toast.js') }}?v={{ filemtime(public_path('assets/brand/muqtarin-toast.js')) }}"></script>
 <script src="{{ asset('assets/admin/js/dropzone.js') }}"></script>
 <script src="{{ asset('assets/admin/js/bootstrap-table.min.js') }}"></script>
 <script>
     (function() {
         if (typeof $ !== 'undefined' && $.fn && $.fn.bootstrapTable && $.fn.bootstrapTable.defaults) {
             var lblNoMatches = {!! json_encode(labels('admin_labels.no_matching_records', 'No matching records found'), JSON_UNESCAPED_UNICODE) !!};
             var lblSearch = {!! json_encode(labels('admin_labels.search_records', 'Search records...'), JSON_UNESCAPED_UNICODE) !!};
             var lblLoading = {!! json_encode(labels('admin_labels.loading_data', 'Loading data...'), JSON_UNESCAPED_UNICODE) !!};
             var lblShowingRows = {!! json_encode(labels('admin_labels.table_showing_rows', 'Showing %s to %s of %s rows'), JSON_UNESCAPED_UNICODE) !!};
             var lblRowsPerPage = {!! json_encode(labels('admin_labels.table_rows_per_page', '%s rows per page'), JSON_UNESCAPED_UNICODE) !!};

             $.extend($.fn.bootstrapTable.defaults, {
                 formatNoMatches: function() {
                     return '<div class="muqtarin-table-empty"><i class="bx bx-inbox"></i><span>' + lblNoMatches + '</span></div>';
                 },
                 formatSearch: function() {
                     return lblSearch;
                 },
                 formatLoadingMessage: function() {
                     return lblLoading;
                 },
                 formatShowingRows: function(pageFrom, pageTo, totalRows) {
                     return lblShowingRows.replace('%s', pageFrom).replace('%s', pageTo).replace('%s', totalRows);
                 },
                 formatRecordsPerPage: function(pageNumber) {
                     return lblRowsPerPage.replace('%s', pageNumber);
                 }
             });
         }
     })();
 </script>
 <script src="{{ asset('assets/admin/js/select2.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/tagify.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/jstree.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/jquery.blockUI.js') }}"></script>
 <script src="{{ asset('assets/admin/js/jquery-slimScroll.js') }}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>

 <script src="{{ asset('assets/admin/js/sweetalert2.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/tinymce.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/jquery.repeater.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/sortable.js') }}"></script>
 <script src="{{ asset('assets/admin/js/jquery-sortable.js') }}"></script>
 <script src="{{ asset('assets/admin/js/lightbox.min.js') }}"></script>
 <script src="{{ asset('js/main.js') }}?v={{ filemtime(public_path('js/main.js')) }}"></script>
 <script>
     var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
     var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
         return new bootstrap.Popover(popoverTriggerEl, {
             trigger: 'hover',
             html: true
         })
     })
 </script>
 <script src="{{ asset('js/sidebarMenu.js') }}"></script>

 <!-- Rating library -->

 <script src="{{ asset('/assets/js/plugins/jquery.rateyo.min.js') }}"></script>

 <!-- =================== js files for datepicker ========================================= -->


 <script src="{{ asset('/assets/admin/js/moment.min.js') }}"></script>
 <script src="{{ asset('/assets/admin/js/daterangepicker.js') }}"></script>

 <!-- =================== js files for stepper ========================================= -->
 <script src="{{ asset('assets/admin/js/jquery.validate.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/additional-methods.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/jquery.steps.min.js') }}"></script>

 <script src="{{ asset('assets/admin/js/nouislider.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/wNumb.js') }}"></script>
 <script>
     window.adminUrls = window.adminUrls || {
         webHomePageTheme: "{{ url('admin/web_home_page_theme') }}",
         webProductCardStyle: "{{ url('admin/web_product_card_style') }}",
         webCategoriesStyle: "{{ url('admin/web_categories_style') }}",
         webBrandsStyle: "{{ url('admin/web_brands_style') }}",
         webWishlistStyle: "{{ url('admin/web_wishlist_style') }}",
     };
     window.adminTranslations = {!! json_encode([
         'error' => labels('admin_labels.error', 'Error'),
         'validation_error' => labels('admin_labels.validation_error', 'Validation Error'),
         'warning' => labels('admin_labels.warning', 'Warning'),
         'info' => labels('admin_labels.info', 'Info'),
         'success' => labels('admin_labels.success', 'Success'),
         'preset_applied' => labels('admin_labels.preset_applied', 'MUQTARIN storefront preset applied!'),
         'palette_synced' => labels('admin_labels.palette_synced', 'Recommended MUQTARIN Pine palette applied to Step 1 colors.'),
         'please_wait' => labels('admin_labels.please_wait', 'Please Wait...'),
         'an_error_occurred' => labels('admin_labels.an_error_occurred', 'An error occurred. Please check your input and try again.'),
         'unexpected_error_occurred' => labels('admin_labels.unexpected_error_occurred', 'An unexpected server error occurred. Please try again.'),
         'operation_completed_successfully' => labels('admin_labels.operation_completed_successfully', 'Operation completed successfully.'),
         'payload_too_large' => labels('admin_labels.payload_too_large', 'The uploaded files are too large for the server limit. Please use smaller images.'),
         'session_expired_refresh' => labels('admin_labels.session_expired_refresh', 'Your session has expired. Please refresh the page and try again.'),
         'network_error_occurred' => labels('admin_labels.network_error_occurred', 'Network connection failed. Please check your internet and try again.'),
         'file_too_large' => labels('admin_labels.file_too_large', 'File is too large'),
         'maximum_file_size_is' => labels('admin_labels.maximum_file_size_is', 'Maximum file size is {filesize}'),
         'file_of_invalid_type' => labels('admin_labels.file_of_invalid_type', 'File of invalid type'),
         'please_enter_name' => labels('admin_labels.please_enter_name', 'Please enter name.'),
         'please_enter_description' => labels('admin_labels.please_enter_description', 'Please enter a description.'),
         'please_choose_primary_color' => labels('admin_labels.please_choose_primary_color', 'Please choose Primary Theme Color.'),
         'please_choose_secondary_color' => labels('admin_labels.please_choose_secondary_color', 'Please choose Secondary Theme Color.'),
         'please_choose_hover_color' => labels('admin_labels.please_choose_hover_color', 'Please choose Link Hover Color.'),
         'please_choose_active_color' => labels('admin_labels.please_choose_active_color', 'Please choose Link Active Color.'),
         'please_choose_background_color' => labels('admin_labels.please_choose_background_color', 'Please choose Background Color.'),
         'please_choose_image' => labels('admin_labels.please_choose_image', 'Please upload the main Store Image.'),
         'please_choose_banner_image' => labels('admin_labels.please_choose_banner_image', 'Please upload the Banner Image.'),
         'please_choose_most_selling_banner' => labels('admin_labels.please_choose_most_selling_banner', 'Please upload the Most Selling Products Banner Image.'),
         'please_choose_stack_image' => labels('admin_labels.please_choose_stack_image', 'Please upload the Stack Image.'),
         'please_choose_login_image' => labels('admin_labels.please_choose_login_image', 'Please upload the Login Page Image.'),
         'please_choose_half_logo' => labels('admin_labels.please_choose_half_logo', 'Please upload the Half Store Logo.'),
         'please_select_store_style' => labels('admin_labels.please_select_store_style', 'Please select a Store Style.'),
         'please_select_product_style' => labels('admin_labels.please_select_product_style', 'Please select a Product Style.'),
         'please_enter_category_section_title' => labels('admin_labels.please_enter_category_section_title', 'Please enter the Category Section Title.'),
         'please_select_category_style' => labels('admin_labels.please_select_category_style', 'Please select a Category Style.'),
         'please_select_category_card_style' => labels('admin_labels.please_select_category_card_style', 'Please select a Category Card Style.'),
         'please_select_brand_style' => labels('admin_labels.please_select_brand_style', 'Please select a Brand Style.'),
         'please_select_offer_slider_style' => labels('admin_labels.please_select_offer_slider_style', 'Please select an Offer Slider Style.'),
         'please_select_category' => labels('admin_labels.please_select_category', 'Please select a category.'),
         'please_select_seller' => labels('admin_labels.please_select_seller', 'Please select a seller.'),
         'please_enter_short_description' => labels('admin_labels.please_enter_short_description', 'Please enter a short description.'),
         'please_enter_product_name' => labels('admin_labels.please_enter_product_name', 'Please enter a product name.'),
         'please_select_product_type' => labels('admin_labels.please_select_product_type', 'Please select product type.'),
         'please_select_product_image' => labels('admin_labels.please_select_product_image', 'Please select product image.'),
         'please_select_valid_zones' => labels('admin_labels.please_select_valid_zones', 'Please select valid delivery zones.'),
         'please_enter_price' => labels('admin_labels.please_enter_price', 'Please enter Price.'),
         'please_enter_special_price' => labels('admin_labels.please_enter_special_price', 'Please enter Special Price.'),
         'special_price_less_than_price' => labels('admin_labels.special_price_less_than_price', 'Special price should be less than price.'),
         'please_enter_sku' => labels('admin_labels.please_enter_sku', 'Please enter SKU.'),
         'please_enter_total_stock' => labels('admin_labels.please_enter_total_stock', 'Please enter total stock.'),
         'please_provide_attribute_value' => labels('admin_labels.please_provide_attribute_value', 'Please provide at least one valid attribute value.'),
     ], JSON_UNESCAPED_UNICODE) !!};
     window.adminT = function(key, fallback) {
         if (window.adminTranslations && Object.prototype.hasOwnProperty.call(window.adminTranslations, key) && window.adminTranslations[key]) {
             return window.adminTranslations[key];
         }
         return fallback || key;
     };
 </script>
 <script src="{{ asset('assets/admin/js/stepper.js') }}?v={{ filemtime(public_path('assets/admin/js/stepper.js')) }}"></script>

 <script src="{{ asset('/assets/js/argon-dashboard.min.js') }}"></script>

 {{-- filepond  --}}
 <script src="{{ asset('assets/admin/js/filepond.js') }}"></script>
 <script src="/assets/filepond/dist/filepond.min.js"></script>
 <script src="/assets/filepond/dist/filepond-plugin-image-preview.min.js"></script>
 <script src="/assets/filepond/dist/filepond-plugin-pdf-preview.min.js"></script>
 <script src="/assets/filepond/dist/filepond-plugin-file-validate-size.js"></script>
 <script src="/assets/filepond/dist/filepond-plugin-file-validate-type.js"></script>
 <script src="/assets/filepond/dist/filepond-plugin-image-validate-size.js"></script>
 <script src="/assets/filepond/dist/filepond.jquery.js"></script>


 {{-- bootstrap table export --}}

 <script src="{{ asset('/assets/admin/js/tableExport.min.js') }}"></script>
 <script src="{{ asset('/assets/admin/js/bootstrap-table-export.min.js') }}"></script>

 {{-- data sortable and dragable js --}}

 <script src="{{ asset('assets/admin/js/TweenMax.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/draggable.min.js') }}"></script>

 {{-- <script src="{{ asset('assets/admin/custom/custom.js') }}"></script> --}}
 {{-- <script src="https://eshop-pro-dev.eshopweb.store/assets/admin/custom/custom.js?v=152"></script> --}}

 <script src="{{ asset('assets/admin/custom/custom.js') }}?v={{ filemtime(public_path('assets/admin/custom/custom.js')) }}"></script>

 <script>
     $(document).ready(function() {
         // Global iziToast settings
         iziToast.settings({
             timeout: 5000,
             resetOnHover: true,
             transitionIn: 'bounceInUp',
             transitionOut: 'fadeOut',
             transitionInMobile: 'fadeInUp',
             transitionOutMobile: 'fadeOutDown',
             position: 'topRight',
             close: true,
             progressBar: true,
             pauseOnHover: true,
         });

         // Session Success
         @if (session('success'))
             iziToast.success({
                 title: "{{ labels('admin_labels.success', 'Success') }}",
                 message: "{{ session('success') }}",
             });
         @endif

         // Session Error
         @if (session('error'))
             iziToast.error({
                 title: "{{ labels('admin_labels.error', 'Error') }}",
                 message: "{{ session('error') }}",
             });
         @endif

         // Session Warning
         @if (session('warning'))
             iziToast.warning({
                 title: "{{ labels('admin_labels.warning', 'Warning') }}",
                 message: "{{ session('warning') }}",
             });
         @endif

         // Session Info
         @if (session('info') || session('message'))
             iziToast.info({
                 title: "{{ labels('admin_labels.info', 'Info') }}",
                 message: "{{ session('info') ?? session('message') }}",
             });
         @endif

         // Session Error Message (Backup key)
         @if (session('error_message'))
             iziToast.error({
                 title: "{{ labels('admin_labels.error', 'Error') }}",
                 message: "{{ session('error_message') }}",
             });
         @endif

         // Laravel Validation Errors
         @if ($errors->any())
             @foreach ($errors->all() as $error)
                 iziToast.error({
                     title: "{{ labels('admin_labels.validation_error', 'Validation Error') }}",
                     message: "{{ $error }}",
                 });
             @endforeach
         @endif
     });

     // MUQTARIN Admin Shell: Collapsible Sidebar (sessionStorage) & Dark/Light Mode (localStorage)
     (function() {
         function syncThemeButton() {
             var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
             var icon = document.getElementById('muqtarin-theme-icon');
             if (icon) {
                 icon.className = isDark ? 'bx bx-sun' : 'bx bx-moon';
             }
         }

         window.muqtarinToggleTheme = function() {
             var html = document.documentElement;
             var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
             html.setAttribute('data-theme', next);
             try { localStorage.setItem('muqtarin_admin_theme', next); } catch (e) {}
             syncThemeButton();
         };

         window.muqtarinToggleSidebar = function() {
             var width = window.innerWidth;
             var isMobile = width < 768;
             var isTablet = width >= 768 && width < 1200;
             var sidebar = document.querySelector('.navbar-vertical');
             var flyout = document.getElementById('muqtarin-sidebar-flyout');
             if (flyout) {
                 flyout.classList.remove('is-visible');
                 flyout.setAttribute('aria-hidden', 'true');
             }
             if (isMobile) {
                 if (sidebar) sidebar.classList.toggle('show');
                 document.body.classList.toggle('sidebar-mobile-open');
             } else if (isTablet) {
                 document.body.classList.toggle('sidebar-tablet-expanded');
                 var expanded = document.body.classList.contains('sidebar-tablet-expanded') ? '1' : '0';
                 try { sessionStorage.setItem('muqtarin_tablet_sidebar_expanded', expanded); } catch (e) {}
             } else {
                 document.body.classList.toggle('sidebar-collapsed');
                 var collapsed = document.body.classList.contains('sidebar-collapsed') ? '1' : '0';
                 try { sessionStorage.setItem('muqtarin_sidebar_collapsed', collapsed); } catch (e) {}
             }
         };

         document.addEventListener('DOMContentLoaded', function() {
             syncThemeButton();
             var backdrop = document.getElementById('muqtarin-sidebar-backdrop');
             if (backdrop) {
                 backdrop.addEventListener('click', function() {
                     var sidebar = document.querySelector('.navbar-vertical');
                     if (sidebar) sidebar.classList.remove('show');
                     document.body.classList.remove('sidebar-mobile-open');
                 });
             }
             var closeMobile = document.getElementById('iconSidenav');
             if (closeMobile) {
                 closeMobile.addEventListener('click', function() {
                     var sidebar = document.querySelector('.navbar-vertical');
                     if (sidebar) sidebar.classList.remove('show');
                     document.body.classList.remove('sidebar-mobile-open');
                     if (window.innerWidth >= 768 && window.innerWidth < 1200) {
                         document.body.classList.remove('sidebar-tablet-expanded');
                         try { sessionStorage.setItem('muqtarin_tablet_sidebar_expanded', '0'); } catch (e) {}
                     }
                 });
             }
         });
     })();

     // MUQTARIN Page-Level Skeleton Lifecycle
     (function() {
         var root = document.documentElement;
         var startTs = (window.performance && performance.now) ? performance.now() : Date.now();
         var navSafetyTimer = null;

         function hideSkeleton() {
             if (window.__mqSkeletonGuard) {
                 clearTimeout(window.__mqSkeletonGuard);
                 window.__mqSkeletonGuard = null;
             }
             if (navSafetyTimer) {
                 clearTimeout(navSafetyTimer);
                 navSafetyTimer = null;
             }
             root.classList.remove('mq-page-loading', 'mq-nav-loading');
         }

         function showSkeleton(isNav) {
             root.classList.add('mq-page-loading');
             if (isNav) root.classList.add('mq-nav-loading');
             if (navSafetyTimer) clearTimeout(navSafetyTimer);
             navSafetyTimer = setTimeout(hideSkeleton, 4500);
         }

         window.muqtarinHideSkeleton = hideSkeleton;
         window.muqtarinShowSkeleton = showSkeleton;

         function scheduleReveal() {
             var now = (window.performance && performance.now) ? performance.now() : Date.now();
             var elapsed = now - startTs;
             var wait = Math.max(0, 180 - elapsed);
             setTimeout(function() {
                 requestAnimationFrame(hideSkeleton);
             }, wait);
         }

         var domReadyRevealed = false;
         function onDomReady() {
             if (domReadyRevealed) return;
             domReadyRevealed = true;
             if (document.readyState === 'complete') {
                 scheduleReveal();
                 return;
             }
             var maxWait = setTimeout(scheduleReveal, 650);
             window.addEventListener('load', function() {
                 clearTimeout(maxWait);
                 scheduleReveal();
             }, { once: true });
         }

         if (document.readyState === 'loading') {
             document.addEventListener('DOMContentLoaded', onDomReady, { once: true });
         } else {
             onDomReady();
         }

         // Ensure Back/Forward browser cache (bfcache) never leaves skeleton stuck
         window.addEventListener('pageshow', hideSkeleton);

         // Trigger skeleton & top progress bar on internal admin route navigation
         document.addEventListener('click', function(e) {
             if (e.defaultPrevented || e.button !== 0) return;
             if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
             var link = e.target && e.target.closest ? e.target.closest('a[href]') : null;
             if (!link) return;
             if (link.hasAttribute('download') || link.getAttribute('target') === '_blank') return;
             if (link.hasAttribute('data-bs-toggle') || link.hasAttribute('data-toggle')) return;
             if (link.classList.contains('no-skeleton')) return;

             var rawHref = (link.getAttribute('href') || '').trim();
             if (!rawHref || rawHref.charAt(0) === '#' || /^(javascript|mailto|tel):/i.test(rawHref)) return;

             try {
                 var url = new URL(link.href, window.location.href);
                 if (url.origin !== window.location.origin) return;
                 if (url.pathname === window.location.pathname && url.search === window.location.search) return;
                 if (/(\/export|\/download|\.(pdf|csv|xlsx|xls|zip|png|jpg|jpeg|svg))$/i.test(url.pathname)) return;
                 showSkeleton(true);
             } catch (err) {}
         }, false);
     })();
 </script>
 <script src="{{ asset('assets/brand/muqtarin-cursor.js') }}?v={{ filemtime(public_path('assets/brand/muqtarin-cursor.js')) }}" defer></script>
