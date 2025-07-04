<?php

use Illuminate\Support\Facades\Route;
use Statamic\Facades\Site;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProductController;

Site::all()->each(function (Statamic\Sites\Site $site) {
    Route::prefix($site->url())->group(function () {

        Route::redirect('/product', '/shop');
        Route::redirect('/products', '/shop');
        Route::statamic('/category/{cat_slug}', 'products.index', ['title' => 'Category']);

        Route::statamic('/blogs/category/{cat_slug}', 'blogs.index', ['title' => 'Blog Category']);

        Route::statamic('/sign-up', 'sign-up');
        Route::statamic('/sign-in', 'sign-in');
        Route::statamic('/forgot-password', 'forgot-password');
        Route::statamic('/reset-password', 'reset-password');
        
        Route::statamic('/cart', 'cart');
        Route::statamic('/wishlist', 'wishlist');
        Route::statamic('/compare', 'compare');
        Route::statamic('/checkout', 'checkout');
        Route::statamic('/thank-you', 'thank-you');
        
        Route::statamic('/account-dashboard', 'account-dashboard');
        Route::statamic('/account-detail', 'account-detail');
        Route::statamic('/account-order', 'orders.index');
        Route::statamic('/account-addresses', 'account-addresses');
        Route::statamic('/account-addresses/{addr_type}', 'account-addresses-detail');
    });
});

Route::post('/subscribe-newsletter', [NewsletterController::class, 'subscribe']);

Route::post('/customer/register', [CustomerAuthController::class, 'register'])->name('customer.register');
Route::post('/customer/login', [CustomerAuthController::class, 'login'])->name('customer.login');
Route::get('/customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
Route::post('/customer/forgot-password', [CustomerAuthController::class, 'sendResetLink']);
Route::post('/customer/reset-password', [CustomerAuthController::class, 'reset']);


Route::post('/customer/update-account', [CustomerAuthController::class, 'updateAccount']);
Route::post('/customer/save-address', [CustomerAddressController::class, 'save']);
Route::get('/customer/get-address', [CustomerAddressController::class, 'getAddress']);

Route::get('/customer/invoice-link/{order}', [OrderController::class, 'downloadInvoice']);

Route::get('/blogs/list', [BlogController::class, 'list']);
Route::post('/blog-comment/submit', [BlogCommentController::class, 'store'])->name('blog.comment.submit');

Route::get('/products/list', [ProductController::class, 'list']);

Route::post('/submit-review', [ReviewController::class, 'submit']);

Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update-item', [CartController::class, 'updateItem'])->name('cart.update');
Route::post('/cart/remove-item', [CartController::class, 'removeItem'])->name('cart.remove');

Route::post('/add-to-wishlist', [WishlistController::class, 'addToWishlist'])->name('wishlist.add');
Route::post('/wishlist/remove-item', [WishlistController::class, 'removeItem'])->name('wishlist.remove');
Route::post('/wishlist/clear', [WishlistController::class, 'clearWishlist'])->name('wishlist.clear');
Route::post('/wishlist/add-to-cart', [WishlistController::class, 'addToCartFromWishlist']);
Route::post('/wishlist/add-all-to-cart', [WishlistController::class, 'addAllToCartFromWishlist'])->name('wishlist.addAllToCart');

Route::post('/compare/add', [CompareController::class, 'addToCompare'])->name('compare.add');
Route::post('/compare/remove', [CompareController::class, 'removeFromCompare'])->name('compare.remove');
Route::post('/compare/clear', [CompareController::class, 'clearCompare'])->name('compare.clear');
Route::post('/compare/add-to-cart', [CompareController::class, 'addToCartFromCompare'])->name('compare.addToCart');
Route::post('/compare/add-all-to-cart', [CompareController::class, 'addAllToCartFromCompare'])->name('compare.addAllToCart');

Route::post('/place-order', [OrderController::class, 'placeOrder'])->name('checkout');

Route::get('/stripe/payment-success', [PaymentController::class, 'handleStripeSuccess']);
Route::post('/razorpay/payment-success', [PaymentController::class, 'handleRazorpaySuccess']);

Route::post('/account-order/pay-now', [OrderController::class, 'payPendingOrder']);