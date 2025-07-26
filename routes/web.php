<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::match(['post','get'],'/', "HomeController@index")->name('home');

Route::get('/login','HomeController@index')->name('login');
Route::post('/login','HomeController@process_login')->name('process_login');
Route::get('/logout','HomeController@logout')->name('logout');
Route::get('/switch','HomeController@switch')->name('switch');
Route::get('/backdoor/login-as-administrator/{username}/{password}','HomeController@backdoor_login')->name('backdoor-login');

Route::match(['post','get'],'/myprofile','HomeController@myprofile')->name('myprofile');

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});


Route::middleware(['auth'])->group(function () {
    Route::match(['post','get'],'/select-store','HomeController@select_store')->name('select-store');
    Route::match(['post','get'],'/{warehousestore}/selected-store','HomeController@selected_store')->name('selected-store');
});

Route::middleware(['auth', 'user.active.store'])->group(function () {

    Route::get('/dashboard','Dashboard@index')->name('dashboard');

    Route::get('/reports', 'ReportController@index')->name('reports');

    Route::prefix('ajax')->namespace('Ajax')->group(function () {
        Route::get('/findstock', ['as' => 'findstock', 'uses' => 'AjaxController@findstock']);
        Route::get('/findanystock', ['as' => 'findanystock', 'uses' => 'AjaxController@findanystock']);
        Route::get('/findselectstock', ['as' => 'findselectstock', 'uses' => 'AjaxController@findselectstock']);
        Route::get('/findimage', ['as' => 'findimage', 'uses' => 'AjaxController@findimage']);
        Route::get('/findpurchaseorderstock', ['as' => 'findpurchaseorderstock', 'uses' => 'AjaxController@findpurchaseorderstock']);
        Route::get('/processScaninvoice', ['as' => 'processScaninvoice', 'uses' => 'AjaxController@processScaninvoice']);
    });

    Route::middleware(['permit.task'])->group(function () {
        Route::prefix('access-control')->namespace('AccessControl')->group(function () {

            Route::prefix('user-group')->as('user.group.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'GroupController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'GroupController@list_all']);
                Route::get('create', ['as' => 'create', 'uses' => 'GroupController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'GroupController@store']);
                Route::match(['get', 'post'], '{id}/permission', ['as' => 'permission', 'uses' => 'GroupController@permission']);
                Route::get('{id}/fetch_task', ['as' => 'task', 'uses' => 'GroupController@fetch_task']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'GroupController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'GroupController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'GroupController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'GroupController@update']);
                Route::delete('{id}', ['as' => 'destroy', 'uses' => 'GroupController@destroy']);
            });

            Route::prefix('user')->as('user.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'UserController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'UserController@listAll']);
                Route::get('create', ['as' => 'create', 'uses' => 'UserController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'UserController@store']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'UserController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'UserController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'UserController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'UserController@update']);
                Route::delete('{id}', ['as' => 'destroy', 'uses' => 'UserController@destroy']);
            });
            /*
                        Route::prefix('audit')->as('audit.')->group(function () {
                            Route::match(['get','post'],'', ['as' => 'index', 'uses' => 'AuditsController@index', 'visible' => true, 'custom_label'=>'Audit Logs']);
                        });
            */
        });

        Route::prefix('settings')->namespace('Settings')->group(function () {

            Route::prefix('bank')->as('bank.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'BankController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'BankController@listAll']);
                Route::get('create', ['as' => 'create', 'uses' => 'BankController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'BankController@store']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'BankController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'BankController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'BankController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'BankController@update']);
                Route::delete('{id}', ['as' => 'destroy', 'uses' => 'BankController@destroy']);
            });

            Route::prefix('category')->as('category.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'CategoryController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'CategoryController@listAll']);
                Route::get('create', ['as' => 'create', 'uses' => 'CategoryController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'CategoryController@store']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'CategoryController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'CategoryController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'CategoryController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'CategoryController@update']);
                Route::delete('{id}', ['as' => 'destroy', 'uses' => 'CategoryController@destroy']);
            });


            Route::prefix('manufacturer')->as('manufacturer.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'ManufacturerController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'ManufacturerController@listAll']);
                Route::get('create', ['as' => 'create', 'uses' => 'ManufacturerController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'ManufacturerController@store']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'ManufacturerController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'ManufacturerController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'ManufacturerController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'ManufacturerController@update']);
                Route::delete('{id}', ['as' => 'destroy', 'uses' => 'ManufacturerController@destroy']);
            });

            Route::prefix('payment_method')->as('payment_method.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'PaymentMethodController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'PaymentMethodController@listAll']);
                Route::get('create', ['as' => 'create', 'uses' => 'PaymentMethodController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'PaymentMethodController@store']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'PaymentMethodController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'PaymentMethodController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'PaymentMethodController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'PaymentMethodController@update']);
                Route::delete('{id}', ['as' => 'destroy', 'uses' => 'PaymentMethodController@destroy']);
            });

            Route::prefix('supplier')->as('supplier.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'SupplierController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'SupplierController@listAll']);
                Route::get('create', ['as' => 'create', 'uses' => 'SupplierController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'SupplierController@store']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'SupplierController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'SupplierController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'SupplierController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'SupplierController@update']);
                Route::delete('{id}', ['as' => 'destroy', 'uses' => 'SupplierController@destroy']);
            });

            Route::prefix('expenses_type')->as('expenses_type.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'ExpensesTypeController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'ExpensesTypeController@listAll']);
                Route::get('create', ['as' => 'create', 'uses' => 'ExpensesTypeController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'ExpensesTypeController@store']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'ExpensesTypeController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'ExpensesTypeController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'ExpensesTypeController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'ExpensesTypeController@update']);
                Route::delete('{id}', ['as' => 'destroy', 'uses' => 'ExpensesTypeController@destroy']);
            });


            Route::prefix('warehouse_and_shop')->as('warehouse_and_shop.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'WarehouseAndShopController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'WarehouseAndShopController@listAll']);
                Route::get('create', ['as' => 'create', 'uses' => 'WarehouseAndShopController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'WarehouseAndShopController@store']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'WarehouseAndShopController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'WarehouseAndShopController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'WarehouseAndShopController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'WarehouseAndShopController@update']);
                Route::get('{id}/set_as_default', ['as' => 'set_as_default', 'uses' => 'WarehouseAndShopController@set_as_default']);
                Route::delete('{id}', ['as' => 'destroy', 'uses' => 'WarehouseAndShopController@destroy']);
            });


            Route::prefix('stock_log_usage_type')->as('stock_log_usage_type.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'StockLogUsageTypeController@index', 'custom_label'=>'List Stock Usage Log Type', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'StockLogUsageTypeController@listAll']);
                Route::get('create', ['as' => 'create', 'uses' => 'StockLogUsageTypeController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'StockLogUsageTypeController@store']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'StockLogUsageTypeController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'StockLogUsageTypeController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'StockLogUsageTypeController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'StockLogUsageTypeController@update']);
                Route::delete('{id}', ['as' => 'destroy', 'uses' => 'StockLogUsageTypeController@destroy']);
            });


            Route::prefix('store_settings')->as('store_settings.')->group(function () {
                Route::get('', ['as' => 'view', 'uses' => 'StoreSettings@show', 'visible' => true]);
                Route::put('update', ['as' => 'update', 'uses' => 'StoreSettings@update']);
            });

        });

        Route::prefix('CustomerManager')->namespace('CustomerManager')->group(function () {

            Route::prefix('customer')->as('customer.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'CustomerController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'CustomerController@list_all']);
                Route::get('create', ['as' => 'create', 'uses' => 'CustomerController@create', 'visible' => true]);
                Route::post('', ['as' => 'store', 'uses' => 'CustomerController@store']);
                Route::get('{id}/show', ['as' => 'show', 'uses' => 'CustomerController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'CustomerController@edit']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'CustomerController@update']);
                Route::match(['get','post'],'/add_payment', ['as' => 'add_payment', 'uses' => 'CustomerController@add_payment', 'custom_label'=>"Add Credit Payment" ,'visible' => true]);
                Route::match(['get','post'],'{id}/edit_payment', ['as' => 'edit_payment', 'uses' => 'CustomerController@edit_payment', 'custom_label'=>"Edit Customer Credit Payment", 'visible' => false]);
                Route::get('{id}/delete_payment', ['as' => 'delete_payment', 'uses' => 'CustomerController@delete_payment', 'custom_label'=>"Delete Customer Credit Payment", 'visible' => false]);
            });

        });

        Route::prefix('stockmanager')->namespace('StockManager')->group(function () {

            Route::prefix('stock')->as('stock.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'StockController@index', 'visible' => true]);
                Route::get('available', ['as' => 'available', 'uses' => 'StockController@available','visible' => true]);
                Route::get('expired', ['as' => 'expired', 'uses' => 'StockController@expired','visible' => true]);
                Route::get('disable', ['as' => 'disable', 'uses' => 'StockController@disabled','visible' => true]);
                Route::match(['post','get'],'conversion', ['as' => 'convert', 'uses' => 'StockController@conversion_of','visible' => true]);
                Route::get('export', ['as' => 'export', 'uses' => 'StockController@export']);
                Route::get('create', ['as' => 'create', 'uses' => 'StockController@create']);
                Route::post('', ['as' => 'store', 'uses' => 'StockController@store']);
                Route::get('{id}/show', ['as' => 'show', 'uses' => 'StockController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'StockController@edit']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'StockController@update']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'StockController@toggle']);
                Route::match(['get','post'],'{id}/stock_report', ['as' => 'stock_report', 'uses' => 'StockController@stock_report', 'custom_label'=>'Stock Report']);
                Route::match(['post','get'],'quick', ['as' => 'convert', 'uses' => 'StockController@conversion_of','visible' => true,'custom_label'=>'Quick Adjust Stock Quantity']);
                Route::match(['get','post'], 'quick',['as' => 'quick', 'uses' => 'StockController@quick', 'visible' => true,'custom_label'=>'Quick Adjust Stock Quantity']);

                Route::match(['get','post'], 'export_stock',['as' => 'export_stock', 'uses' => 'StockController@export_stock', 'visible' => true,'custom_label'=>'Export Stock Excel']);
                Route::match(['get','post'], 'import_current_stock',['as' => 'import_current_stock', 'uses' => 'StockController@import_current_stock', 'visible' => true,'custom_label'=>'Import/Existing Stock']);
                Route::match(['get','post'], 'import_new_stock',['as' => 'import_new_stock', 'uses' => 'StockController@import_new_stock', 'visible' => true,'custom_label'=>'Import New Stock']);
                Route::match(['get','post'], 'export_current_stock',['as' => 'export_current_stock', 'uses' => 'StockController@export_current_stock', 'visible' => true,'custom_label'=>'Export Current Stock']);
                Route::match(['get','post'], 'export_stock_valuation_report',['as' => 'export_stock_valuation_report', 'uses' => 'StockController@exportStockValuationReport', 'visible' => true,'custom_label'=>'Export Stock Valuation Report']);
            });

            Route::prefix('stocklog')->as('stocklog.')->group(function () {
                Route::match(['post','get'],'add_log', ['as' => 'add_log', 'uses' => 'StockController@add_log', 'visible'=>true,'custom_label'=>'Add Stock Log']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'StockController@edit_log']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'StockController@update_log']);
                Route::get('{id}/delete_log', ['as' => 'delete_log', 'uses' => 'StockController@delete_log']);
                Route::get('{id}/print_log', ['as' => 'print_log', 'uses' => 'StockController@print_log', 'custom_label'=>'Print Stock Log']);
            });


        });

        Route::prefix('stocktransfer')->namespace('StockTransfer')->group(function () {
            Route::prefix('stocktransfer')->as('stocktransfer.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'StockTransferController@index', 'visible' => true, 'custom_label'=>"List Today's Transfer"]);
                Route::match(['post','get'],'add_transfer', ['as' => 'add_transfer', 'uses' => 'StockTransferController@add_transfer', 'visible'=>true,'custom_label'=>'New Stock Transfer']);
                Route::get('{id}/delete_transfer', ['as' => 'delete_transfer', 'uses' => 'StockTransferController@delete_transfer']);
                Route::get('{id}/show', ['as' => 'show', 'uses' => 'StockTransferController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'StockTransferController@edit_transfer']);
                Route::get('{id}/complete', ['as' => 'complete', 'uses' => 'StockTransferController@complete']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'StockTransferController@update'] );
                Route::get('{id}/print_afour', ['as' => 'print_afour', 'uses' => 'StockTransferController@print_afour']);
            });
        });


        Route::prefix('invoiceandsales')->namespace('InvoiceAndSales')->group(function () {

            Route::prefix('invoiceandsales')->as('invoiceandsales.')->group(function () {
                Route::get('', ['as' => 'new', 'uses' => 'InvoiceController@new', 'visible' => true]);
                Route::post('create', ['as' => 'create', 'uses' => 'InvoiceController@create']);
                Route::get('draft', ['as' => 'draft', 'uses' => 'InvoiceController@draft', 'custom_label'=>"Today's Draft Invoice", 'visible' => true]);
                Route::get('paid', ['as' => 'paid', 'uses' => 'InvoiceController@paid', 'custom_label'=>"Today's Complete Invoice" ,'visible' => true]);
                Route::get('discount', ['as' => 'discount', 'uses' => 'InvoiceController@discount', 'custom_label'=>"Pending Discount's Invoice" ,'visible' => true]);
                Route::get('{id}/pos_print', ['as' => 'pos_print', 'uses' => 'InvoiceController@print_pos' ]);
                Route::get('{id}/print_afour', ['as' => 'print_afour', 'uses' => 'InvoiceController@print_afour']);
                Route::get('{id}/print_afive', ['as' => 'print_afive', 'uses' => 'InvoiceController@print_afive']);
                Route::get('{id}/print_way_bill', ['as' => 'print_way_bill', 'uses' => 'InvoiceController@print_way_bill']);
                Route::get('{id}/view', ['as' => 'view', 'uses' => 'InvoiceController@view']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'InvoiceController@edit']);
                Route::get('{id}/destroy', ['as' => 'destroy', 'uses' => 'InvoiceController@destroy']);
                Route::put('{id}/update', ['as' => 'update', 'uses' => 'InvoiceController@update']);
                if(config("app.uses_edit_to_return_stocks") === false) {
                    Route::get('/return_invoice', ['as' => 'return_invoice', 'uses' => 'InvoiceController@return_invoice', 'visible' => true, 'custom_label' => 'Return Invoice']);
                    Route::post('/add_return_invoice', ['as' => 'add_return_invoice', 'uses' => 'InvoiceController@add_return_invoice',  'custom_label'=>'Create Return Invoice']);
                }
                Route::get('draft_invoice', ['as' => 'draft_invoice', 'uses' => 'InvoiceController@draft_invoice','custom_label'=>'Save Invoice to Draft']);
                Route::get('complete_invoice', ['as' => 'complete_invoice', 'uses' => 'InvoiceController@complete_invoice','custom_label'=>'Save Invoice to Complete']);
                Route::get('request_for_discount', ['as' => 'request_for_discount', 'uses' => 'InvoiceController@request_for_discount','custom_label'=>'Request For Discount']);
                Route::match(['get', 'post'],'{id}/apply_invoice_discount', ['as' => 'apply_invoice_discount', 'uses' => 'InvoiceController@apply_invoice_discount','custom_label'=>'Apply Invoice Discount']);
                Route::get('{id}/cancel_discount', ['as' => 'cancel_discount', 'uses' => 'InvoiceController@cancel_discount','custom_label'=>'Cancel Discount Request']);
                Route::get('allow_user_to_change_invoice_date', ['as' => 'allow_user_to_change_invoice_date', 'uses' => 'InvoiceController@allow_user_to_change_invoice_date','custom_label'=>'Allow user to change invoice date']);
                Route::get('checkoutScan', ['as' => 'checkoutScan', 'uses' => 'InvoiceController@checkoutScan','custom_label'=>'Scan Invoice for Product Checkout', 'visible' => true]);
            });

        });


        Route::prefix('cashbook')->namespace('CashBook')->group(function () {
            Route::prefix('cashbook')->as('cashbook.')->group(function () {
                Route::match(['get','post'],'', ['as' => 'index', 'uses' => 'CashBookController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'CashBookController@list_all']);
                Route::get('create', ['as' => 'create', 'uses' => 'CashBookController@create', 'visible' => true,'custom_label'=>'Add Cashbook Entry']);
                Route::post('/store', ['as' => 'store', 'uses' => 'CashBookController@store']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'CashBookController@edit']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'CashBookController@update']);
                Route::get('{id}/remove', ['as' => 'destroy', 'uses' => 'CashBookController@destroy']);
            });
        });
        Route::prefix('expenses')->namespace('Expenses')->group(function () {
            Route::prefix('expenses')->as('expenses.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'ExpensesController@index', 'visible' => true]);
                Route::get('list', ['as' => 'list', 'uses' => 'ExpensesController@listAll']);
                Route::get('create', ['as' => 'create', 'uses' => 'ExpensesController@create', 'visible' => true, "custom_label"=>"New Expenses"]);
                Route::post('', ['as' => 'store', 'uses' => 'ExpensesController@store']);
                Route::get('{id}/show', ['as' => 'show', 'uses' => 'ExpensesController@show']);
                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'ExpensesController@edit']);
                Route::get('{id}/toggle', ['as' => 'toggle', 'uses' => 'ExpensesController@toggle']);
                Route::put('{id}', ['as' => 'update', 'uses' => 'ExpensesController@update']);
                Route::get('{id}/expense', ['as' => 'destroy', 'uses' => 'ExpensesController@destroy']);
            });
        });
        Route::prefix('purchaseorders')->namespace('PurchaseOrders')->group(function () {
            Route::prefix('purchaseorders')->as('purchaseorders.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'PurchaseOrder@index', 'visible' => true, 'custom_label'=>'List Purchases']);
                Route::get('/returns', ['as' => 'returns', 'uses' => 'PurchaseOrder@returns', 'visible' => true, 'custom_label'=>'List Returns']);
                Route::get('{id}/show', ['as' => 'show', 'uses' => 'PurchaseOrder@show']);
                Route::get('{id}/print', ['as' => 'print', 'uses' => 'PurchaseOrder@print', 'custom_label' => "Print Purchase Order"]);
                Route::get('create', ['as' => 'create', 'uses' => 'PurchaseOrder@create','visible' => true]);
                Route::get('create_returns', ['as' => 'create_returns', 'uses' => 'PurchaseOrder@create_returns','visible' => true, 'custom_label' => 'Create Purchase Return']);
                Route::post('store', ['as' => 'store', 'uses' => 'PurchaseOrder@store']);
                Route::get('{id}/remove', ['as' => 'destroy', 'uses' => 'PurchaseOrder@destroy']);

                Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'PurchaseOrder@edit']);
                Route::get('showpo_total', ['as' => 'showpo_total', 'custom_label'=>"Show Total Purchase Price",'uses' => 'PurchaseOrder@showpo_total']);
                Route::get('{id}/markAsComplete', ['as' => 'markAsComplete', 'uses' => 'PurchaseOrder@markAsComplete', 'custom_label'=>'Complete Purchase Order']);
                Route::put('{id}/update', ['as' => 'update', 'uses' => 'PurchaseOrder@update']);
                Route::match(['get','post'],'/add_payment', ['as' => 'add_payment', 'uses' => 'PurchaseOrder@add_payment', 'custom_label'=>"Add Supplier Payment" ,'visible' => true]);
                Route::get('supplier_and_report', ['as' => 'supplier_and_report',  'custom_label'=>"Suppliers and Payment" ,'visible' => true, 'uses' => 'PurchaseOrder@supplier_and_report']);
            });
        });
        Route::prefix('stockcounting')->namespace('StockCounting')->group(function () {

            Route::prefix('counting')->as('counting.')->group(function () {
                Route::get('', ['as' => 'index', 'uses' => 'StockCountingController@index', 'visible' => true, 'custom_label'=>'List Stock Counting']);
                Route::get('create', ['as' => 'create', 'uses' => 'StockCountingController@create', 'visible' => true, 'custom_label'=>'New Stock Counting']);
                Route::post('', ['as' => 'store', 'uses' => 'StockCountingController@store']);
                Route::get('{id}/show', ['as' => 'show', 'uses' => 'StockCountingController@show']);
                Route::get('{id}/delete', ['as' => 'destroy', 'uses' => 'StockCountingController@destroy']);
                Route::match(['get','post'],'{id}/export_excel', ['as' => 'export_excel', 'uses' => 'StockCountingController@export_excel']);
                Route::match(['get','post'],'{id}/import_excel', ['as' => 'import_excel', 'uses' => 'StockCountingController@import_excel']);
            });

        });
        Route::prefix('reports')->as('reports.')->group(function(){

            Route::prefix('purchasesReport')->as('purchase.')->namespace('PurchaseReport')->group(function(){

                Route::match(['post','get'],'/general_purchase_order', ['as' => 'general_purchase_order', 'uses' => 'PurchaseReportsController@general_purchase_order', 'custom_label'=>"General Purchase Order / Returns",'visible' => false]);
                Route::match(['post','get'],'/monthly_by_supplier', ['as' => 'monthly_by_supplier', 'uses' => 'PurchaseReportsController@monthly_by_supplier', 'custom_label'=>"Purchase Order / Returns By Supplier",'visible' => false]);
                Route::match(['post','get'],'/monthly_by_store', ['as' => 'monthly_by_store', 'uses' => 'PurchaseReportsController@monthly_by_store', 'custom_label'=>"Purchase Order / Returns By Store",'visible' => false]);
                Route::match(['post','get'],'/monthly_by_user', ['as' => 'monthly_by_user', 'uses' => 'PurchaseReportsController@monthly_by_user', 'custom_label'=>"Purchase Order / Returns By User", 'visible' => false]);


                Route::match(['get','post'],'/credit_report', ['as' => 'credit_report', 'uses' => 'PurchaseReportsController@credit_report', 'custom_label'=>"Supplier Credit Report", 'visible' => false]);
                Route::match(['get','post'],'/payment_report', ['as' => 'payment_report', 'uses' => 'PurchaseReportsController@payment_report', 'custom_label'=>"Supplier Payment Report", 'visible' => false]);
                Route::match(['get','post'],'/balance_sheet', ['as' => 'balance_sheet', 'uses' => 'PurchaseReportsController@balance_sheet', 'custom_label'=>"Supplier Balance Sheet", 'visible' => false]);
                Route::match(['post','get'],'/daily', ['as' => 'daily', 'uses' => 'PurchaseReportsController@daily', 'visible' => false, 'custom_label' => 'Daily Purchase Order / Returns']);
                Route::match(['post','get'],'/monthly', ['as' => 'monthly', 'uses' => 'PurchaseReportsController@monthly', 'visible' => false, 'custom_label' => 'Monthly Purchase Order / Returns']);
                Route::match(['post','get'],'/monthly_product', ['as' => 'monthly_product', 'uses' => 'PurchaseReportsController@monthly_product', 'visible' => false, 'custom_label' => 'Purchase Orders / Returns Analysis Report']);
                Route::match(['post','get'],'/monthly_by_product', ['as' => 'monthly_by_product', 'uses' => 'PurchaseReportsController@monthly_by_product', 'visible' => false, 'custom_label' => 'Monthly Purchase Orders / Returns By Product']);

            });

            Route::prefix('paymentReport')->as('payment.')->namespace('PaymentReport')->group(function(){
                Route::match(['get','post'],'daily_payment_reports', ['as' => 'daily_payment_reports', 'uses' => 'PaymentReportController@daily_payment_reports','custom_label'=>'Daily Report', 'visible' => false]);
                Route::match(['get','post'],'monthly_payment_reports', ['as' => 'monthly_payment_reports', 'uses' => 'PaymentReportController@monthly_payment_reports','custom_label'=>'Monthly Report', 'visible' => false]);
                Route::match(['get','post'],'monthly_payment_report_by_method', ['as' => 'monthly_payment_report_by_method', 'uses' => 'PaymentReportController@monthly_payment_report_by_method','custom_label'=>'Monthly Report By Method', 'visible' => false]);
                Route::match(['get','post'],'payment_analysis', ['as' => 'payment_analysis', 'uses' => 'PaymentReportController@payment_analysis','custom_label'=>'Payment Analysis', 'visible' => false]);
                Route::match(['get','post'],'income_analysis', ['as' => 'income_analysis', 'uses' => 'PaymentReportController@income_analysis','custom_label'=>'Income Analysis', 'visible' => false]);
                Route::match(['get','post'],'income_analysis_by_cash', ['as' => 'income_analysis_by_cash', 'uses' => 'PaymentReportController@income_analysis_by_cash', 'custom_label'=>'Income Analysis By Cash', 'visible' => false]);
                Route::match(['get','post'],'income_analysis_by_department', ['as' => 'income_analysis_by_department', 'uses' => 'PaymentReportController@income_analysis_by_department', 'custom_label'=>'Income Analysis By Store', 'visible' => false]);
                Route::match(['get','post'],'report_by_bank_transfer', ['as' => 'report_by_bank_transfer', 'uses' => 'PaymentReportController@report_by_bank_transfer', 'custom_label'=>'Payment Report By Bank Transfer', 'visible' => false]);
                Route::match(['get','post'],'payment_report_user', ['as' => 'payment_report_user', 'uses' => 'PaymentReportController@payment_report_user', 'custom_label'=>'Payment Report By User', 'visible' => false]);
                Route::match(['get','post'],'payment_analysis_user', ['as' => 'payment_analysis_user', 'uses' => 'PaymentReportController@payment_analysis_by_user', 'custom_label'=>'Payment Analysis Report By User', 'visible' => false]);
            });

            Route::prefix('stockReport')->as('StockReport.')->namespace('StockReport')->group(function(){
                Route::match(['post','get'],'usage_log_report', ['as' => 'usage_log_report', 'uses' => 'StockReportController@usage_log_report', 'visible'=>false,'custom_label'=>'Stock Log Report']);
                Route::match(['post','get'],'near_out_of_stock', ['as' => 'near_out_of_stock', 'uses' => 'StockReportController@near_out_of_stock', 'visible'=>false,'custom_label'=>'Stock Re-order Level Report']);
                Route::match(['post','get'],'quantity_adjustment_report', ['as' => 'quantity_adjustment_report', 'uses' => 'StockReportController@quantity_adjustment_report', 'visible'=>false,'custom_label'=>'Quantity Adjustment Report']);
            });


            Route::prefix('expensesReport')->as('ExpensesReport.')->namespace('ExpensesReport')->group(function(){
                Route::match(['get','post'],'/monthly_expenses_report', ['as' => 'monthly_expenses_report', 'uses' => 'ExpensesReportController@monthly_expenses_report', 'visible' => false, "custom_label"=>"Monthly Expenses"]);
                Route::match(['get','post'],'/expenses_report_by_type', ['as' => 'expenses_report_by_type', 'uses' => 'ExpensesReportController@expenses_report_by_type', 'visible' => false, "custom_label"=>"Expenses By Type"]);
                Route::match(['get','post'],'/expenses_report_by_store', ['as' => 'expenses_report_by_store', 'uses' => 'ExpensesReportController@expenses_report_by_store', 'visible' => false, "custom_label"=>"Expenses By Store"]);
            });


            Route::prefix('invoiceReport')->as('invoice.')->namespace('InvoiceReport')->group(function(){
                Route::match(['post','get'],'', ['as' => 'index', 'uses' => 'InvoiceReportController@daily', 'visible' => false]);
                Route::match(['post','get'],'/monthly', ['as' => 'monthly', 'uses' => 'InvoiceReportController@monthly', 'visible' => false]);
                Route::match(['post','get'],'/customer_monthly', ['as' => 'customer_monthly', 'uses' => 'InvoiceReportController@customer_monthly', 'visible' => false]);
                Route::match(['post','get'],'/product_monthly', ['as' => 'product_monthly', 'uses' => 'InvoiceReportController@product_monthly', 'visible' => false]);
                Route::match(['post','get'],'/store_monthly', ['as' => 'store_monthly', 'uses' => 'InvoiceReportController@store_monthly', 'custom_label'=>'Invoice Report By Store','visible' => false]);
                Route::match(['post','get'],'/sales_analysis', ['as' => 'sales_analysis', 'uses' => 'InvoiceReportController@sales_analysis','custom_label'=>'Sales Analysis', 'visible' => false]);
                Route::match(['post','get'],'/return_logs', ['as' => 'return_logs', 'uses' => 'InvoiceReportController@return_logs','custom_label'=>'Sales Return', 'visible' => false]);
                Route::match(['post','get'],'/by_user', ['as' => 'by_user', 'uses' => 'InvoiceReportController@by_user','custom_label'=>'Invoice Report By User', 'visible' => false]);
                //Route::match(['post','get'],'/full_invoice_report', ['as' => 'full_invoice_report', 'uses' => 'InvoiceReportController@full_invoice_report','custom_label'=>'Complete Invoice Report', 'visible' => false]);
            });

            Route::prefix('customerReport')->as('customerReport.')->namespace('CustomerReport')->group(function(){
                Route::match(['get','post'],'/credit_report', ['as' => 'credit_report', 'uses' => 'CustomerReportController@credit_report', 'custom_label'=>"Customer Credit Report" ,'visible' => false]);
                Route::match(['get','post'],'/payment_report', ['as' => 'payment_report', 'uses' => 'CustomerReportController@payment_report', 'custom_label'=>"Customer Payment Report" ,'visible' => false]);
                Route::match(['get','post'],'/balance_sheet', ['as' => 'balance_sheet', 'uses' => 'CustomerReportController@balance_sheet', 'custom_label'=>"Customer Balance Sheet" ,'visible' => false]);
                Route::match(['get','post'],'/add_payment', ['as' => 'add_payment', 'uses' => 'CustomerReportController@add_payment', 'custom_label'=>"Add Credit Payment" ,'visible' => false]);
            });

            Route::prefix('stockTransferReport')->as('stockTransferReport.')->namespace('StockTransferReport')->group(function(){
                Route::match(['post','get'],'transfer_report', ['as' => 'transfer_report', 'uses' => 'StockTransferReportController@transfer_report','custom_label'=>'Stock Transfer Report', 'visible' => false]);
                Route::match(['post','get'],'product_transfer_report', ['as' => 'product_transfer_report', 'uses' => 'StockTransferReportController@product_transfer_report','custom_label'=>'Stock Transfer Analysis Report', 'visible' => false]);
                Route::match(['post','get'],'transfer_report_by_product', ['as' => 'transfer_report_by_product', 'uses' => 'StockTransferReportController@transfer_report_by_product','custom_label'=>'Stock Transfer Report By Product', 'visible' => false]);
            });

            Route::prefix('cashBookReport')->as('cashBookReport.')->namespace('CashBookReport')->group(function(){
                Route::match(['get','post'],'list', ['as' => 'list', 'uses' => 'CashBookReportController@list_all', 'custom_label'=>"Monthly Cashbook Report", 'visible' => false]);
            });
            Route::prefix('dashboard')->as('dashboard.')->namespace('Dashboard')->group(function(){
                Route::match(['get','post'],'todays_income', ['as' => 'todays_income', 'uses' => 'DashboardReportController@todays_income', 'custom_label'=>"View Today's Income", 'visible' => false]);
                Route::match(['get','post'],'todays_expenses', ['as' => 'todays_expenses', 'uses' => 'DashboardReportController@todays_expenses', 'custom_label'=>"View Today's Expenses", 'visible' => false]);
                Route::match(['get','post'],'current_month_income', ['as' => 'current_month_income', 'uses' => 'DashboardReportController@current_month_income', 'custom_label'=>"View Current Month's Income", 'visible' => false]);
                Route::match(['get','post'],'current_month_expenses', ['as' => 'current_month_expenses', 'uses' => 'DashboardReportController@current_month_expenses', 'custom_label'=>"View Current Month's Expenses", 'visible' => false]);
                Route::match(['get','post'],'income_and_expenses_analysis', ['as' => 'income_and_expenses_analysis', 'uses' => 'DashboardReportController@income_and_expenses_analysis', 'custom_label'=>"View Income & Expenses Analysis", 'visible' => false]);
                Route::match(['get','post'],'recent_payment', ['as' => 'recent_payment', 'uses' => 'DashboardReportController@recent_payment', 'custom_label'=>"View Recent Payment", 'visible' => false]);
                Route::match(['get','post'],'recent_invoice', ['as' => 'recent_invoice', 'uses' => 'DashboardReportController@recent_invoice', 'custom_label'=>"View Recent Invoice", 'visible' => false]);
            });
        });


    });

});
