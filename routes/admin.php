<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AuthController,
    UserController,
    CategoriesController,
    SubcategoryController,
    ProductController,
    MetalController,
    AttributeController,
    BlogController,
    ContentController,
    DashboardController,
    GlobalSearchController,
    NewsletterController,
    NotificationController,
    OrderController,
    ReviewController,
    RoleController,
    SettingController,
    StaffController,
    TransactionsController
};

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:3,1')->name('login.store');

    Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])->middleware('throttle:1,1')->name('password.email');

    Route::get('reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'updatePassword'])->name('password.update');
});



/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Dashboard Routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('permission:dashboard.view');
        Route::get('stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
        Route::get('revenue-chart', [DashboardController::class, 'getRevenueChart'])->name('dashboard.revenue-chart');
        Route::get('top-countries', [DashboardController::class, 'getTopCountries'])->name('dashboard.top-countries');
        Route::get('trending-products', [DashboardController::class, 'getTrendingProducts'])->name('dashboard.trending-products');
        Route::get('best-selling-products', [DashboardController::class, 'getBestSellingProducts'])->name('dashboard.best-selling-products');
        Route::get('top-categories', [DashboardController::class, 'getTopCategories'])->name('dashboard.top-categories');
        Route::get('recent-transactions', [DashboardController::class, 'getRecentTransactions'])->name('dashboard.recent-transactions');
        Route::get('top-customers', [DashboardController::class, 'getTopCustomers'])->name('dashboard.top-customers');
        Route::get('recent-orders', [DashboardController::class, 'getRecentOrders'])->name('dashboard.recent-orders');
    });



    Route::get('global-search', [GlobalSearchController::class, 'globalSearch'])->name('admin.global.search')->middleware('permission:global.search,admin');

    /*
    |--------------------------------------------------------------------------
    | Users Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->name('users.')->group(function () {

        Route::get('/', [UserController::class, 'index'])
            ->name('index')
            ->middleware('permission:users.view.any,admin');

        Route::get('create', [UserController::class, 'create'])
            ->name('create')
            ->middleware('permission:users.create,admin');

        Route::post('/', [UserController::class, 'store'])
            ->name('store')
            ->middleware('permission:users.create,admin');

        Route::get('{id}/edit', [UserController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:users.update,admin');

        Route::put('{id}', [UserController::class, 'update'])
            ->name('update')
            ->middleware('permission:users.update,admin');

        Route::get('{id}/password', [UserController::class, 'editPassword'])
            ->name('password.edit')
            ->middleware('permission:users.reset.password,admin');

        Route::put('{id}/password', [UserController::class, 'updatePassword'])
            ->name('password.update')
            ->middleware('permission:users.reset.password,admin');

        Route::put('{id}/status', [UserController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:users.update.status,admin');

        Route::delete('{id}', [UserController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:users.delete,admin');


        Route::get('{id}/stats', [UserController::class, 'getStats'])
            ->name('stats')
            ->middleware('permission:users.view');


        Route::get('{id}/orders', [UserController::class, 'getOrders'])
            ->name('orders')
            ->middleware('permission:users.view');


        Route::get('{id}/transactions', [UserController::class, 'getTransactions'])
            ->name('transactions')
            ->middleware('permission:users.view');



        Route::get('{id}', [UserController::class, 'show'])
            ->name('show')
            ->middleware('permission:users.view,admin');
    });


    /*
    |--------------------------------------------------------------------------
    | STAFF Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])
            ->name('index')
            ->middleware('permission:staff.view.any,admin');

        Route::get('create', [StaffController::class, 'create'])
            ->name('create')
            ->middleware('permission:staff.create,admin');

        Route::post('/', [StaffController::class, 'store'])
            ->name('store')
            ->middleware('permission:staff.create,admin');

        Route::get('{id}/edit', [StaffController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:staff.update,admin');

        Route::put('{id}', [StaffController::class, 'update'])
            ->name('update')
            ->middleware('permission:staff.update,admin');

        Route::get('{id}/password', [StaffController::class, 'editPassword'])
            ->name('password.edit')
            ->middleware('permission:staff.reset.password,admin');

        Route::put('{id}/password', [StaffController::class, 'updatePassword'])
            ->name('password.update')
            ->middleware('permission:staff.reset.password,admin');

        Route::put('{id}/status', [StaffController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:staff.update.status,admin');

        Route::delete('{id}', [StaffController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:staff.delete,admin');

        Route::get('{id}', [StaffController::class, 'show'])
            ->name('show')
            ->middleware('permission:staff.view,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | Categories Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])
            ->name('index')
            ->middleware('permission:categories.view.any,admin');

        Route::get('create', [CategoriesController::class, 'create'])
            ->name('create')
            ->middleware('permission:categories.create,admin');

        Route::post('/', [CategoriesController::class, 'store'])
            ->name('store')
            ->middleware('permission:categories.create,admin');

        Route::get('{id}/edit', [CategoriesController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:categories.update,admin');

        Route::put('{id}', [CategoriesController::class, 'update'])
            ->name('update')
            ->middleware('permission:categories.update,admin');

        Route::put('{id}/status', [CategoriesController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:categories.update.status,admin');

        Route::delete('{id}', [CategoriesController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:categories.delete,admin');

        Route::get('{id}', [CategoriesController::class, 'show'])
            ->name('show')
            ->middleware('permission:categories.view,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | Subcategories Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('subcategories')->name('subcategories.')->group(function () {
        Route::get('/', [SubcategoryController::class, 'index'])
            ->name('index')
            ->middleware('permission:subcategories.view.any,admin');

        Route::get('create', [SubcategoryController::class, 'create'])
            ->name('create')
            ->middleware('permission:subcategories.create,admin');

        Route::post('/', [SubcategoryController::class, 'store'])
            ->name('store')
            ->middleware('permission:subcategories.create,admin');

        Route::get('{id}/edit', [SubcategoryController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:subcategories.update,admin');

        Route::put('{id}', [SubcategoryController::class, 'update'])
            ->name('update')
            ->middleware('permission:subcategories.update,admin');

        Route::put('{id}/status', [SubcategoryController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:subcategories.update.status,admin');

        Route::delete('{id}', [SubcategoryController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:subcategories.delete,admin');

        Route::get('ajax/{id}', [SubcategoryController::class, 'subcategories'])
            ->name('ajax');

        Route::get('{id}', [SubcategoryController::class, 'show'])
            ->name('show')
            ->middleware('permission:subcategories.view,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | Products Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])
            ->name('index')
            ->middleware('permission:products.view.any,admin');

        Route::get('create', [ProductController::class, 'create'])
            ->name('create')
            ->middleware('permission:products.create,admin');

        Route::post('/', [ProductController::class, 'store'])
            ->name('store')
            ->middleware('permission:products.create,admin');

        Route::get('{id}/edit', [ProductController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:products.update,admin');

        Route::put('{id}', [ProductController::class, 'update'])
            ->name('update')
            ->middleware('permission:products.update,admin');

        Route::put('{id}/status', [ProductController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:products.update.status,admin');

        Route::get('{id}/variants', [ProductController::class, 'variants'])
            ->name('variants')
            ->middleware('permission:products.view,admin');

        Route::put('{id}/variant-status', [ProductController::class, 'updateVariantStatus'])
            ->name('variant.status')
            ->middleware('permission:products.update,admin');

        Route::delete('image/{image}', [ProductController::class, 'deleteImage'])
            ->name('image.delete')
            ->middleware('permission:products.images.delete,admin');

        Route::delete('{id}', [ProductController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:products.delete,admin');

        Route::get('{id}', [ProductController::class, 'show'])
            ->name('show')
            ->middleware('permission:products.view,admin');
    });


    Route::prefix('images')->group(function () {
        Route::post('store', [ProductController::class, 'uploadImage'])
            ->name('images.store');

        Route::get('get', [ProductController::class, 'uploadImageList'])
            ->name('images.index');
    });


    /*
    |--------------------------------------------------------------------------
    | Metals Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('metals')->name('metals.')->group(function () {
        Route::get('/', [MetalController::class, 'index'])
            ->name('index')
            ->middleware('permission:metals.view.any,admin');

        Route::put('{id}', [MetalController::class, 'update'])
            ->name('update')
            ->middleware('permission:metals.update,admin');

        Route::get('assign/{type}', [MetalController::class, 'assign'])
            ->name('assign')
            ->middleware('permission:metals.assign,admin');

        Route::post('assign', [MetalController::class, 'assignCategory'])
            ->name('assign.category')
            ->middleware('permission:metals.assign,admin');

        Route::delete('{id}/category/{category}', [MetalController::class, 'deleteMainCategory'])
            ->name('category.delete')
            ->middleware('permission:metals.category.delete,admin');

        Route::delete('{id}/category/{category}/subcategory/{subcategory}', [MetalController::class, 'deleteSubCategory'])
            ->name('subcategory.delete')
            ->middleware('permission:metals.subcategory.delete,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | Attributes Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('attributes')->name('attributes.')->group(function () {
        Route::get('/', [AttributeController::class, 'index'])
            ->name('index')
            ->middleware('permission:attributes.view.any,admin');

        Route::get('create', [AttributeController::class, 'create'])
            ->name('create')
            ->middleware('permission:attributes.create,admin');

        Route::post('/', [AttributeController::class, 'store'])
            ->name('store')
            ->middleware('permission:attributes.create,admin');

        Route::put('{id}/status', [AttributeController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:attributes.update.status,admin');

        Route::delete('{id}', [AttributeController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:attributes.delete,admin');

        Route::get('ajax/{id}', [AttributeController::class, 'fetchAttributeValues'])
            ->name('ajax');

        Route::get('{id}/values', [AttributeController::class, 'addValue'])
            ->name('values.create')
            ->middleware('permission:attributes.values.create,admin');

        Route::post('{id}/values', [AttributeController::class, 'storeValue'])
            ->name('values.store')
            ->middleware('permission:attributes.values.create,admin');

        Route::delete('values/{value}', [AttributeController::class, 'deleteValue'])
            ->name('values.delete')
            ->middleware('permission:attributes.values.delete,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | CONTENT Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('contents')->name('contents.')->group(function () {
        Route::get('/', [ContentController::class, 'index'])
            ->name('index')
            ->middleware('permission:contents.view.any,admin');

        Route::get('{id}/edit', [ContentController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:contents.update,admin');

        Route::put('{id}', [ContentController::class, 'update'])
            ->name('update')
            ->middleware('permission:contents.update,admin');

        Route::put('{id}/status', [ContentController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:contents.update.status,admin');

        Route::get('{id}', [ContentController::class, 'show'])
            ->name('show')
            ->middleware('permission:contents.view,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | NEWSLETTER Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('newsletters')->name('newsletters.')->group(function () {
        Route::get('/', [NewsletterController::class, 'index'])
            ->name('index')
            ->middleware('permission:newsletters.view.any,admin');

        Route::put('{id}/status', [NewsletterController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:newsletters.update.status,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | BLOG Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('blogs')->name('blogs.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])
            ->name('index')
            ->middleware('permission:blogs.view.any,admin');

        Route::get('create', [BlogController::class, 'create'])
            ->name('create')
            ->middleware('permission:blogs.create,admin');

        Route::post('/', [BlogController::class, 'store'])
            ->name('store')
            ->middleware('permission:blogs.create,admin');

        Route::get('{id}/edit', [BlogController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:blogs.update,admin');

        Route::put('{id}', [BlogController::class, 'update'])
            ->name('update')
            ->middleware('permission:blogs.update,admin');

        Route::put('{id}/status', [BlogController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:blogs.update.status,admin');

        Route::delete('{id}', [BlogController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:blogs.delete,admin');

        Route::get('{id}', [BlogController::class, 'show'])
            ->name('show')
            ->middleware('permission:blogs.view,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | REVIEW Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])
            ->name('index')
            ->middleware('permission:reviews.view.any,admin');

        Route::put('{id}/status', [ReviewController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:reviews.update.status,admin');

        Route::delete('{id}', [ReviewController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:reviews.delete,admin');

        Route::get('{id}', [ReviewController::class, 'show'])
            ->name('show')
            ->middleware('permission:reviews.view,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | SETTINGS Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('', [SettingController::class, 'index'])
            ->name('index')
            ->middleware('permission:settings.view,admin');

        Route::put('', [SettingController::class, 'update'])
            ->name('update')
            ->middleware('permission:settings.update,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | ROLES Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->name('index')
            ->middleware('permission:roles.view.any,admin');

        Route::get('create', [RoleController::class, 'create'])
            ->name('create')
            ->middleware('permission:roles.create,admin');

        Route::post('/', [RoleController::class, 'store'])
            ->name('store')
            ->middleware('permission:roles.create,admin');

        Route::get('{id}', [RoleController::class, 'show'])
            ->name('show')
            ->middleware('permission:roles.view,admin');

        Route::get('{id}/edit', [RoleController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:roles.update,admin');

        Route::put('{id}', [RoleController::class, 'update'])
            ->name('update')
            ->middleware('permission:roles.update,admin');

        Route::delete('{id}', [RoleController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:roles.delete,admin');

        Route::put('{id}/status', [RoleController::class, 'updateStatus'])
            ->name('status')
            ->middleware('permission:roles.update.status,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | TRANSACTIONS Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionsController::class, 'index'])
            ->name('index')
            ->middleware('permission:transactions.view.any,admin');

        Route::get('{id}', [TransactionsController::class, 'show'])
            ->name('show')
            ->middleware('permission:transactions.view,admin');
    });



    /*
|--------------------------------------------------------------------------
| Orders Module
|--------------------------------------------------------------------------
*/
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])
            ->name('index')
            ->middleware('permission:orders.view.any,admin');

        Route::get('{id}', [OrderController::class, 'show'])
            ->name('show')
            ->middleware('permission:orders.view,admin');
        Route::put('{id}/status', [OrderController::class, 'updateStatus'])->name('status')->middleware('permission:orders.update.status,admin');
    });

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('', [NotificationController::class, 'index'])
            ->name('index');

        Route::post('read/{id}', [NotificationController::class, 'markAsRead'])
            ->name('read');

        Route::post('read-all', [NotificationController::class, 'markAllRead'])
            ->name('readAll');
    });



    /*
    |--------------------------------------------------------------------------
    | DISCOUNT Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('discount')->name('discount.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])
            ->name('index')
            ->middleware('permission:discount.view.any,admin');

        Route::get('{id}', [OrderController::class, 'show'])
            ->name('show')
            ->middleware('permission:discount.view,admin');
        Route::put('{id}/status', [OrderController::class, 'updateStatus'])->name('status')->middleware('permission:discount.update.status,admin');
    });
});
