<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================== Frontend ====================
$routes->get('/sitemap.xml', 'Pages::sitemap');
$routes->get('/faq', 'Pages::faq', ['filter' => 'customerFilter']);
$routes->get('/contact', 'Pages::contact', ['filter' => 'customerFilter']);

// Artikel (frontend)
$routes->get('/article', 'Pages::article', ['filter' => 'customerFilter']);
$routes->post('/actionsearcharticle', 'Pages::actionSearchArticle', ['filter' => 'customerFilter']);
$routes->get('/article/find/(:any)', 'Pages::findArticle/$1', ['filter' => 'customerFilter']);
$routes->get('/article/category/(:any)', 'Pages::articleCategory/$1', ['filter' => 'customerFilter']);
$routes->get('/article/(:any)', 'Pages::article/$1', ['filter' => 'customerFilter']);

$routes->get('/tentang', 'Pages::tentang', ['filter' => 'customerFilter']);
$routes->get('/partner', 'Pages::partner', ['filter' => 'customerFilter']);
$routes->get('/syarat', 'Pages::syarat', ['filter' => 'customerFilter']);
$routes->get('/kebijakan', 'Pages::kebijakan', ['filter' => 'customerFilter']);
$routes->get('/visimisi', 'Pages::visiMisi', ['filter' => 'customerFilter']);
$routes->get('/iklan', 'Pages::iklan', ['filter' => 'customerFilter']);

$routes->get('/', 'Pages::index', ['filter' => 'customerFilter']);
$routes->get('/indexgalih', 'Pages::indexGalih', ['filter' => 'customerFilter']);
$routes->get('/product', 'Pages::product', ['filter' => 'customerFilter']);
$routes->get('/product/category/(:any)', 'Pages::productCategory/$1', ['filter' => 'customerFilter']);
$routes->get('/product/(:any)', 'Pages::product/$1', ['filter' => 'customerFilter']);
$routes->get('/product/(:any)/(:any)', 'Pages::product/$1/$2', ['filter' => 'customerFilter']);

$routes->post('/actionfind', 'Pages::actionFind', ['filter' => 'customerFilter']);
$routes->get('/search/suggest', 'Pages::searchSuggestions', ['filter' => 'customerFilter']);
$routes->get('/find', 'Pages::findSmart', ['filter' => 'customerFilter']);
$routes->get('/find/(:any)', 'Pages::find/$1', ['filter' => 'customerFilter']);

$routes->get('/cart', 'Pages::cart', ['filter' => 'customerFilter']);
$routes->post('/addcart/(:any)/(:any)/(:any)', 'Pages::addCart/$1/$2/$3', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/deletecart/(:any)', 'Pages::deleteCart/$1', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/reducecart/(:any)', 'Pages::reduceCart/$1', ['filter' => ['customerFilter', 'csrf']]);

$routes->get('/getkota/(:any)', 'Pages::getKota/$1', ['filter' => 'customerFilter']);
$routes->get('/getkec/(:any)', 'Pages::getKec/$1', ['filter' => 'customerFilter']);
$routes->get('/getkode/(:any)', 'Pages::getKode/$1', ['filter' => 'customerFilter']);
$routes->get('/address', 'Pages::address', ['filter' => 'customerFilter']);

// NOTE: kamu sebelumnya ga kasih filter di addaddress.
// Kalau memang harus login customer, saran: pakai customerLoginFilter/customerFilter sesuai sistemmu.
$routes->post('/addaddress', 'Pages::addAddress', ['filter' => ['customerFilter', 'csrf']]);

$routes->post('/deleteaddress/(:any)/(:any)', 'Pages::deleteAddress/$1/$2', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/editaddress/(:any)', 'Pages::editAddress/$1', ['filter' => ['customerFilter', 'csrf']]);

// Voucher (frontend)
$routes->post('/redeemvoucher/(:num)', 'Pages::redeemVoucher/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
$routes->post('/usevoucher/(:any)', 'Pages::useVoucher/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
$routes->post('/cancelvoucher/(:any)', 'Pages::cancelVoucher/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
$routes->post('/removevoucher/(:num)', 'Pages::removeVoucher/$1', ['filter' => ['customerShippingFilter', 'csrf']]);

$routes->get('/shipping/(:any)', 'Pages::shipping/$1', ['filter' => 'customerShippingFilter']);
$routes->get('/payment/method/(:any)/(:any)', 'Pages::paymentMethod/$1/$2', ['filter' => 'customerShippingFilter']);
$routes->get('/payment/finish', 'Pages::paymentFinishRedirect');
$routes->get('/payment/unfinish', 'Pages::paymentUnfinishRedirect');
$routes->get('/payment/error', 'Pages::paymentErrorRedirect');
$routes->get('/payment/status/(:any)', 'Pages::paymentStatus/$1');
$routes->get('/payment/(:any)', 'Pages::payment/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
$routes->post('/actionpaycore/(:any)', 'Pages::actionPayCore/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
$routes->post('/updatetransaction', 'Pages::updateTransaction');
$routes->post('/midtrans/notification', 'Pages::midtransNotificationDispatcher');

$routes->get('/wishlist', 'Pages::wishlist', ['filter' => 'customerFilter']);
$routes->post('/addwishlist/(:any)', 'Pages::addWishlist/$1', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/delwishlist/(:any)', 'Pages::delWishlist/$1', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/wishlisttocart', 'Pages::wishlistToCart', ['filter' => ['customerFilter', 'csrf']]);

$routes->get('/order', 'Pages::order', ['filter' => 'customerFilter']);
$routes->get('/order/(:any)', 'Pages::order/$1', ['filter' => 'customerFilter']);
$routes->post('/order/cancel/(:any)', 'Pages::cancelCustomerOrder/$1', ['filter' => ['customerFilter', 'csrf']]);
$routes->get('/orderdetail/(:any)', 'Pages::orderDetail/$1', ['filter' => 'customerFilter']);
$routes->get('/invoice/(:any)/pdf', 'Pages::invoicePdf/$1', ['filter' => 'customerFilter']);
$routes->get('/invoice/(:any)', 'Pages::invoice/$1', ['filter' => 'customerFilter']);
$routes->get('/account', 'Pages::account', ['filter' => 'customerLoginFilter']);

$routes->get('/register', 'Pages::register', ['filter' => 'customerLogoutFilter']);
$routes->post('/actionregister', 'Pages::actionRegister', ['filter' => ['customerFilter', 'csrf']]);
$routes->get('/verify', 'Pages::verify', ['filter' => 'customerFilter']);
$routes->post('/actionverify', 'Pages::actionVerify', ['filter' => ['customerFilter', 'csrf']]);
$routes->get('/login', 'Pages::login', ['filter' => 'customerLogoutFilter']);
$routes->post('/actionlogin', 'Pages::actionLogin', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/kirimotp', 'Pages::kirimOTP', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/editsandi/(:any)', 'Pages::editSandi/$1', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/logout', 'Pages::actionLogout', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/cancelorder/(:any)', 'Pages::cancelOrder/$1', ['filter' => ['adminFilter', 'csrf']]);
$routes->post('/addlikearticle/(:any)', 'Pages::addLikeArticle/$1', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/addsharearticle/(:any)', 'Pages::addShareArticle/$1', ['filter' => ['customerFilter', 'csrf']]);
$routes->get('/editarticle/(:any)', 'AdminController::editArticle/$1', ['filter' => 'adminFilter']);


// ==================== GambarController ====================
$routes->post('/changepic/(:any)', 'GambarController::gantiUkuran/$1', ['filter' => ['adminFilter', 'csrf']]);
$routes->post('/gantilokasi/(:any)', 'GambarController::gantiLokasi/$1', ['filter' => ['adminFilter', 'csrf']]);
$routes->get('/viewpic/(:segment)', 'GambarController::tampilGambarBarang/$1');
$routes->get('/product-cover/(:segment)', 'Pages::productCoverImage/$1');
$routes->get('/viewpichover/(:segment)', 'GambarController::tampilGambarBarangHover/$1');
$routes->get('/viewvar/(:segment)/(:num)', 'GambarController::tampilGambarVarian/$1/$2');
$routes->get('/viewvar3000/(:segment)/(:num)', 'GambarController::tampilGambarVarian3000/$1/$2');
$routes->get('/imgart/(:any)', 'GambarController::tampilGambarArtikel/$1');
$routes->get('/imgart/(:any)/(:any)', 'GambarController::tampilGambarArtikel/$1/$2');
$routes->get('/home-image/(:segment)', 'GambarController::homeImage/$1');
$routes->get('/imgheader/(:any)', 'GambarController::tampilGambarHeader/$1');
$routes->get('/imgheaderhp/(:any)', 'GambarController::tampilGambarHeaderHp/$1');

// Customer support chat: session-token ownership, CSRF on mutations, and short polling only.
$routes->get('/chat/bootstrap', 'SupportChatController::bootstrap', ['filter' => 'customerFilter']);
$routes->get('/chat/poll', 'SupportChatController::poll', ['filter' => 'customerFilter']);
$routes->post('/chat/send', 'SupportChatController::send', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/chat/handoff', 'SupportChatController::handoff', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/chat/resolve', 'SupportChatController::resolve', ['filter' => ['customerFilter', 'csrf']]);
$routes->post('/chat/rating', 'SupportChatController::rate', ['filter' => ['customerFilter', 'csrf']]);


// ==================== Admin Controller ====================
// CSRF applies once to every route in this group; do not add per-route csrf filters here.
$routes->group('admin', ['filter' => ['adminFilter', 'csrf']], static function($routes) {

    // Dashboard website admin
    $routes->get('', 'AdminController::dashboard');

    // Produk
    $routes->get('producttable', 'AdminController::listProductTable');
    $routes->get('product', 'AdminController::listProduct');
    $routes->get('menu-previews', 'AdminController::menuPreviews');
    $routes->post('menu-previews', 'AdminController::saveMenuPreviews');
    $routes->get('product-bulk-edit', 'AdminController::bulkEditProduct');
    $routes->post('product-bulk-edit', 'AdminController::actionBulkEditProduct');

    // Artikel (admin)
    $routes->get('article', 'AdminController::article');
    $routes->get('article/category/(:any)', 'AdminController::articleCategory/$1');
    $routes->post('deletearticle/(:any)', 'AdminController::deleteArticle/$1');
    $routes->get('addarticle', 'AdminController::addArticle');
    $routes->post('addarticle', 'AdminController::actionAddArticle');
    $routes->get('editarticle/(:any)', 'AdminController::editArticle/$1');
    $routes->post('editarticle/(:any)', 'AdminController::actionEditArticle/$1');
    $routes->post('addgaleriarticle', 'AdminController::actionAddGaleriArticle');

    // Produk (admin)
    $routes->get('addproduct', 'AdminController::addProduct');
    $routes->get('editproduct/(:any)', 'AdminController::editProduct/$1');
    $routes->post('editproduct/(:any)', 'AdminController::actionEditProduct/$1');
    $routes->post('product', 'AdminController::actionAddProduct');
    $routes->post('deleteproduct/(:any)', 'AdminController::deleteProduct/$1');
    $routes->post('activeproduct/(:any)', 'AdminController::activeProduct/$1');

    // legacy (kalau masih dipakai)
    $routes->post('product/(:any)', 'AdminController::actionEditProduct/$1');

    // Order Online (admin)
    $routes->get('order', 'AdminController::order');
    $routes->get('order/online', 'AdminController::order');
    $routes->post('order/delete/(:any)', 'AdminController::deleteOnlineOrder/$1');
    $routes->post('order/delete-bulk', 'AdminController::deleteSelectedOnlineOrders');
    $routes->get('order/add', 'AdminController::orderAdd');
    $routes->post('order/add', 'AdminController::actionOrderAdd');
    $routes->post('actioneditresi', 'AdminController::actionEditResi');
    $routes->get('reprint', 'AdminController::reprint');
    $routes->get('suratjalan/(:any)', 'AdminController::suratJalan/$1');
    $routes->get('marketplace', 'AdminController::marketplace');
    $routes->post('confirm-mp/(:any)', 'AdminController::confirmMarketplace/$1');
    $routes->get('edit-mp/(:any)', 'AdminController::editMarketplace/$1');
    $routes->post('accreprint/(:any)', 'AdminController::accReprint/$1');
    $routes->post('denyreprint/(:any)', 'AdminController::denyReprint/$1');
    $routes->post('ordertoko/(:any)', 'AdminController::orderToko/$1', ['filter' => 'loginToko']);
    $routes->post('suratjalan/(:any)/printed', 'AdminController::markSuratJalanPrinted/$1');

    // Mutasi (admin)
    $routes->get('mutasiconfirm', 'AdminController::mutasiConfirm');
    $routes->get('mutasi', 'AdminController::mutasi');
    $routes->get('mutasi/(:any)', 'AdminController::mutasi/$1');
    $routes->post('actionaddmutasi', 'AdminController::actionAddMutasi');
    $routes->post('accmutasi/(:any)', 'AdminController::accMutasi/$1');
    $routes->post('denymutasi/(:any)', 'AdminController::denyMutasi/$1');
    $routes->get('labelbarang/(:any)', 'AdminController::labelBarang/$1');

    // Home layout (admin)
    $routes->get('homelayout', 'AdminController::homeLayout');
    $routes->post('homelayout', 'AdminController::actionHomeLayout');
    $routes->get('changepic', 'AdminController::changePic');
    $routes->get('partners', 'AdminController::partners');
    $routes->post('partners', 'AdminController::actionPartner');
    $routes->post('partners/(:num)', 'AdminController::actionPartner/$1');
    $routes->post('partners/delete/(:num)', 'AdminController::deletePartner/$1');

    // Gratis ongkir per wilayah
    $routes->get('free-shipping', 'AdminController::freeShipping');
    $routes->post('free-shipping', 'AdminController::actionFreeShipping');
    $routes->get('meta-capi', 'AdminController::metaCapi');
    $routes->post('meta-capi', 'AdminController::actionMetaCapi');
    $routes->get('activity-log', 'AdminController::activityLog');
    $routes->get('rbac', 'AdminController::rbac');
    $routes->post('rbac/role', 'AdminController::actionRbacRole');
    $routes->post('rbac/role/(:num)', 'AdminController::actionRbacRole/$1');
    $routes->post('rbac/role/delete/(:num)', 'AdminController::deleteRbacRole/$1');
    $routes->post('rbac/assign', 'AdminController::assignRbacRole');
    $routes->post('rbac/assign/delete', 'AdminController::deleteRbacAssignment');
    $routes->post('rbac/sync-users', 'AdminController::syncRbacUserRoles');
    $routes->get('spam-cleanup', 'AdminController::spamCleanup');
    $routes->post('spam-cleanup', 'AdminController::actionSpamCleanup');
    $routes->get('customers', 'AdminController::customers');
    $routes->post('customers/suspend', 'AdminController::actionSuspendCustomer');
    $routes->post('customers/reinstate', 'AdminController::actionReinstateCustomer');

    // ==================== SEO helper (admin) — optional ====================
    $routes->get('seo-check/(:any)', 'AdminController::seoCheck/$1');

    // ==================== Voucher System (admin) ====================
    $routes->get('voucher', 'AdminController::voucher');
    $routes->post('voucher/add', 'AdminController::actionAddVoucher');
    $routes->post('voucher/delete/(:any)', 'AdminController::deleteVoucher/$1');
    $routes->get('voucher/edit/(:num)', 'AdminController::editVoucher/$1');
    $routes->post('voucher/edit/(:num)', 'AdminController::editVoucher/$1');
    $routes->post('voucher/toggle/(:num)', 'AdminController::toggleVoucher/$1');
    $routes->get('voucher/usage', 'AdminController::voucherUsage');
    $routes->post('voucher/usage/delete/(:num)', 'AdminController::deleteVoucherUsage/$1');

    // Hybrid AI + Human Support Chat
    $routes->get('support-chat', 'SupportChatController::adminIndex');
    $routes->get('support-chat/queue', 'SupportChatController::adminQueue');
    $routes->get('support-chat/feedback', 'SupportChatController::adminFeedback');
    $routes->get('support-chat/telemetry', 'SupportChatController::adminTelemetry');
    $routes->get('support-chat/messages/(:num)', 'SupportChatController::adminMessages/$1');
    $routes->post('support-chat/knowledge/draft-from-chat/(:num)', 'SupportChatController::draftKnowledgeFromChat/$1');
    $routes->post('support-chat/read/(:num)', 'SupportChatController::adminRead/$1');
    $routes->post('support-chat/reply/(:num)', 'SupportChatController::adminReply/$1');
    $routes->post('support-chat/claim/(:num)', 'SupportChatController::adminClaim/$1');
    $routes->post('support-chat/close/(:num)', 'SupportChatController::adminClose/$1');
    $routes->post('support-chat/knowledge/save', 'SupportChatController::saveKnowledge');
    $routes->post('support-chat/knowledge/delete/(:num)', 'SupportChatController::deleteKnowledge/$1');
    $routes->post('support-chat/product-spec/save', 'SupportChatController::saveProductSpec');
    $routes->post('support-chat/provider/save', 'SupportChatController::saveProviderSettings');

});


// ==================== Fixing ====================

// English storefront routes. Indonesian keeps its established unprefixed URLs;
// the locale placeholder lets CodeIgniter set the request locale for /en/... .
$routes->useSupportedLocalesOnly(true);
$routes->group('{locale}', ['filter' => 'customerFilter'], static function ($routes) {
    $routes->get('', 'Pages::index');
    $routes->get('faq', 'Pages::faq');
    $routes->get('contact', 'Pages::contact');
    $routes->get('article', 'Pages::article');
    $routes->post('actionsearcharticle', 'Pages::actionSearchArticle');
    $routes->get('article/find/(:any)', 'Pages::findArticle/$1');
    $routes->get('article/category/(:any)', 'Pages::articleCategory/$1');
    $routes->get('article/(:any)', 'Pages::article/$1');
    $routes->post('addlikearticle/(:any)', 'Pages::addLikeArticle/$1', ['filter' => ['csrf']]);
    $routes->post('addsharearticle/(:any)', 'Pages::addShareArticle/$1', ['filter' => ['csrf']]);
    $routes->get('tentang', 'Pages::tentang');
    $routes->get('partner', 'Pages::partner');
    $routes->get('syarat', 'Pages::syarat');
    $routes->get('kebijakan', 'Pages::kebijakan');
    $routes->get('visimisi', 'Pages::visiMisi');
    $routes->get('iklan', 'Pages::iklan');
    $routes->get('product', 'Pages::product');
    $routes->get('product/category/(:any)', 'Pages::productCategory/$1');
    $routes->get('product/(:any)', 'Pages::product/$1');
    $routes->get('product/(:any)/(:any)', 'Pages::product/$1/$2');
    $routes->get('find', 'Pages::findSmart');
    $routes->get('find/(:any)', 'Pages::find/$1');
    $routes->get('search/suggest', 'Pages::searchSuggestions');
    $routes->post('actionfind', 'Pages::actionFind');
    $routes->get('cart', 'Pages::cart');
    $routes->post('deletecart/(:any)', 'Pages::deleteCart/$1', ['filter' => ['csrf']]);
    $routes->post('reducecart/(:any)', 'Pages::reduceCart/$1', ['filter' => ['csrf']]);
    $routes->get('order', 'Pages::order');
    $routes->get('order/(:any)', 'Pages::order/$1');
    $routes->post('order/cancel/(:any)', 'Pages::cancelCustomerOrder/$1', ['filter' => ['csrf']]);
    $routes->get('orderdetail/(:any)', 'Pages::orderDetail/$1');
    $routes->get('invoice/(:any)/pdf', 'Pages::invoicePdf/$1');
    $routes->get('invoice/(:any)', 'Pages::invoice/$1');
    $routes->get('wishlist', 'Pages::wishlist');
    $routes->post('addwishlist/(:any)', 'Pages::addWishlist/$1', ['filter' => ['csrf']]);
    $routes->post('delwishlist/(:any)', 'Pages::delWishlist/$1', ['filter' => ['csrf']]);
    $routes->post('wishlisttocart', 'Pages::wishlistToCart', ['filter' => ['csrf']]);
    $routes->post('addcart/(:any)/(:any)/(:any)', 'Pages::addCart/$1/$2/$3', ['filter' => ['csrf']]);
    $routes->get('address', 'Pages::address');
    $routes->get('getkota/(:any)', 'Pages::getKota/$1');
    $routes->get('getkec/(:any)', 'Pages::getKec/$1');
    $routes->get('getkode/(:any)', 'Pages::getKode/$1');
    $routes->post('actionregister', 'Pages::actionRegister', ['filter' => ['csrf']]);
    $routes->get('verify', 'Pages::verify');
    $routes->post('actionverify', 'Pages::actionVerify', ['filter' => ['csrf']]);
    $routes->post('kirimotp', 'Pages::kirimOTP', ['filter' => ['csrf']]);
    $routes->post('actionlogin', 'Pages::actionLogin', ['filter' => ['csrf']]);
    $routes->post('logout', 'Pages::actionLogout', ['filter' => ['csrf']]);
    $routes->post('editsandi/(:any)', 'Pages::editSandi/$1', ['filter' => ['csrf']]);
    $routes->post('addaddress', 'Pages::addAddress', ['filter' => ['csrf']]);
    $routes->post('deleteaddress/(:any)/(:any)', 'Pages::deleteAddress/$1/$2', ['filter' => ['csrf']]);
    $routes->post('editaddress/(:any)', 'Pages::editAddress/$1', ['filter' => ['csrf']]);
    $routes->get('shipping/(:any)', 'Pages::shipping/$1', ['filter' => 'customerShippingFilter']);
    $routes->post('redeemvoucher/(:num)', 'Pages::redeemVoucher/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
    $routes->post('usevoucher/(:any)', 'Pages::useVoucher/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
    $routes->post('cancelvoucher/(:any)', 'Pages::cancelVoucher/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
    $routes->post('removevoucher/(:num)', 'Pages::removeVoucher/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
    $routes->get('payment/method/(:any)/(:any)', 'Pages::paymentMethod/$1/$2', ['filter' => 'customerShippingFilter']);
    $routes->get('payment/finish', 'Pages::paymentFinishRedirect');
    $routes->get('payment/unfinish', 'Pages::paymentUnfinishRedirect');
    $routes->get('payment/error', 'Pages::paymentErrorRedirect');
    $routes->get('payment/status/(:any)', 'Pages::paymentStatus/$1');
    $routes->get('payment/(:any)', 'Pages::payment/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
    $routes->post('actionpaycore/(:any)', 'Pages::actionPayCore/$1', ['filter' => ['customerShippingFilter', 'csrf']]);
});
$routes->get('{locale}/login', 'Pages::login', ['filter' => 'customerLogoutFilter']);
$routes->get('{locale}/register', 'Pages::register', ['filter' => 'customerLogoutFilter']);
$routes->get('{locale}/account', 'Pages::account', ['filter' => 'customerLoginFilter']);


// ==================== Marketplace ====================
$routes->get('/market/product', 'MarketplaceController::product', ['filter' => 'marketFilter']);
$routes->post('/market/actionfind', 'MarketplaceController::actionFind', ['filter' => 'marketFilter']);
$routes->get('/market/find/(:any)', 'MarketplaceController::find/$1', ['filter' => 'marketFilter']);
$routes->get('/market/cart', 'MarketplaceController::cart', ['filter' => 'marketFilter']);
$routes->post('/market/addcart/(:any)/(:any)', 'MarketplaceController::addCart/$1/$2', ['filter' => ['marketFilter', 'csrf']]);
$routes->post('/market/reducecart/(:any)', 'MarketplaceController::reduceCart/$1', ['filter' => ['marketFilter', 'csrf']]);
$routes->post('/market/submitorder', 'MarketplaceController::submitOrder', ['filter' => ['marketFilter', 'csrf']]);
$routes->post('/maket/submitorder', 'MarketplaceController::submitOrder', ['filter' => ['marketFilter', 'csrf']]);

// routes untuk nyolong raja ongkir (sesuai punya kamu - tapi ini aneh karena actionnya submitOrder)


// ==================== Analytics (admin) ====================
$routes->get('/analytics', 'Analytics::index', ['filter' => 'adminFilter']);
$routes->get('/analytics/live', 'Analytics::live', ['filter' => 'adminFilter']);
$routes->get('/analytics/exportCsv', 'Analytics::exportCsv', ['filter' => 'adminFilter']);
$routes->get('/analytics/exportPdf', 'Analytics::exportPdf', ['filter' => 'adminFilter']);
$routes->post('/analytics/blacklist/add', 'Analytics::addBlacklist', ['filter' => ['adminFilter', 'csrf']]);
$routes->post('/analytics/blacklist/del/(:any)', 'Analytics::delBlacklist/$1', ['filter' => ['adminFilter', 'csrf']]);


// Tracking
$routes->post('/track/hit', 'Track::hit');


// ==================== 404 fallback ====================
$routes->get('/(:any)', 'Pages::notFound');
