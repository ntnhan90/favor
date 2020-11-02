<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin','namespace' => 'Admin','middleware' => 'adminCheck'], function () {

	Route::get('/clear-cache', function () {
	    $exitCode = Artisan::call('cache:clear');
	    $exitCode = Artisan::call('config:clear');
	    $exitCode = Artisan::call('route:clear');
	});

	Route::get('/phpinfo', function () {
	    phpinfo();
	});

	/* add login */
	Route::get('/login', 'AdminController@login')->name('admin.login');
	Route::post('/checkLogin', 'AdminController@checkLogin');

	//profile admin 
	Route::get('/profile', 'AdminController@profile');
    Route::post('/update', 'AdminController@update');
    Route::post('/updatepassword', 'AdminController@updatepassword');

	//admin managements
   	Route::get('/admins', 'AdminController@admins')->middleware('view_manage_admin');
    Route::get('/addadmins', 'AdminController@addadmins')->middleware('add_manage_admin');
    Route::post('/addnewadmin', 'AdminController@addnewadmin')->middleware('add_manage_admin');
    Route::get('/editadmin/{id}', 'AdminController@editadmin')->middleware('edit_manage_admin');
    Route::post('/updateadmin', 'AdminController@updateadmin')->middleware('edit_manage_admin');
    Route::post('/deleteadmin', 'AdminController@deleteadmin')->middleware('delete_manage_admin');

    Route::get('/manageroles', 'AdminController@manageroles')->middleware('manage_role');
    Route::get('/addrole/{id}', 'AdminController@addrole')->middleware('manage_role');
    Route::post('/addnewroles', 'AdminController@addnewroles')->middleware('manage_role');
    Route::get('/addadmintype', 'AdminController@addadmintype')->middleware('add_admin_type');
    Route::post('/addnewtype', 'AdminController@addnewtype')->middleware('add_admin_type');
    Route::get('/editadmintype/{id}', 'AdminController@editadmintype')->middleware('edit_admin_type');
    Route::post('/updatetype', 'AdminController@updatetype')->middleware('edit_admin_type');
    Route::post('/deleteadmintype', 'AdminController@deleteadmintype')->middleware('delete_admin_type');

	Route::get('/dashboard', 'DashboarhController@index')->name('admin');

    //banners
    Route::group(['prefix' => 'banners'], function () {
        Route::get('/', 'BannersController@banners')->middleware('view_app_setting');
        Route::get('/add', 'BannersController@addbanner')->middleware('edit_app_setting');
        Route::post('/insert', 'BannersController@addNewBanner')->middleware('edit_app_setting');
        Route::get('/edit/{id}', 'BannersController@editbanner')->middleware('edit_app_setting');
        Route::post('/update', 'BannersController@updateBanner')->middleware('edit_app_setting');
        Route::post('/delete', 'BannersController@deleteBanner')->middleware('edit_app_setting');
        Route::get('/filter', 'BannersController@filterbanners')->middleware('edit_app_setting');
    });

	Route::get('/menus', 'MenusController@menus');
    Route::get('/addmenus', 'MenusController@addmenus');
    Route::post('/addnewmenu', 'MenusController@addnewmenu');
    Route::get('/editmenu/{id}', 'MenusController@editmenu');
    Route::post('/updatemenu', 'MenusController@updatemenu');
    Route::get('/deletemenu/{id}', 'MenusController@deletemenu');
    Route::post('/deletemenu', 'MenusController@deletemenu');
    Route::post('/menuposition', 'MenusController@menuposition');


    Route::group(['prefix' => 'currencies'], function () {
        Route::get('/', 'CurrencyController@index');
    });

    Route::group(['prefix' => 'customers'], function () {
        Route::get('/', 'CustomersController@index');
        Route::get('/add', 'CustomersController@add');
        Route::post('/add', 'CustomersController@insert');
        Route::get('/edit/{id}', 'CustomersController@edit');
        Route::post('/update', 'CustomersController@update');
        Route::post('/delete', 'CustomersController@delete');
        Route::get('/filter', 'CustomersController@filter');
    });

    Route::group(['prefix' => 'coupons'], function () {
        Route::get('/', 'CouponsController@display');
        Route::get('/add', 'CouponsController@add');
        Route::post('/insert', 'CouponsController@insert');
        Route::get('/edit/{id}', 'CouponsController@edit');
        Route::post('/update', 'CouponsController@update');
        Route::post('/delete', 'CouponsController@delete');
        Route::get('/filter', 'CouponsController@filter');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoriesController@display');
        Route::get('/add', 'CategoriesController@add');
        Route::post('/add', 'CategoriesController@insert');
        Route::get('/edit/{id}', 'CategoriesController@edit');
        Route::post('/update', 'CategoriesController@update');
        Route::post('/delete', 'CategoriesController@delete');
        Route::get('/filter', 'CategoriesController@filter');
    });

    Route::group(['prefix' => 'manufacturers'], function () {
        Route::get('/', 'ManufacturerController@display');
        Route::get('/add', 'ManufacturerController@add');
        Route::post('/add', 'ManufacturerController@insert');
        Route::get('/edit/{id}', 'ManufacturerController@edit');
        Route::post('/update', 'ManufacturerController@update');
        Route::post('/delete', 'ManufacturerController@delete');
        Route::get('/filter', 'ManufacturerController@filter');
    });

    Route::group(['prefix' => 'media'], function () {
        Route::get('/setting', 'MediaController@setting');
        Route::get('/', 'MediaController@index');
        Route::post('/updatemediasetting', 'MediaController@updatemediasetting');
        Route::post('/uploadimage', 'MediaController@fileUpload');
        Route::post('/delete', 'MediaController@deleteimage');
        Route::get('/detail/{id}', 'MediaController@detailimage');
        Route::get('/refresh', 'MediaController@refresh');
        Route::post('/regenerateimage', 'MediaController@regenerateimage');
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('/', 'ProductController@display');
        Route::get('/add', 'ProductController@add');
        Route::post('/add', 'ProductController@insert');
        Route::get('/edit/{id}', 'ProductController@edit');
        Route::post('/update', 'ProductController@update');
        Route::post('/delete', 'ProductController@delete');
        Route::get('/filter', 'ProductController@filter');
        Route::group(['prefix' => 'inventory'], function () {
            Route::get('/display', 'ProductController@addinventoryfromsidebar')->middleware('view_product');
            // Route::post('/addnewstock', 'ProductController@addinventory')->middleware('view_product');
            Route::get('/ajax_min_max/{id}/', 'ProductController@ajax_min_max')->middleware('view_product');
            Route::get('/ajax_attr/{id}/', 'ProductController@ajax_attr')->middleware('view_product');
            Route::post('/addnewstock', 'ProductController@addnewstock')->middleware('add_product');
            Route::post('/addminmax', 'ProductController@addminmax')->middleware('add_product');
            Route::get('/addproductimages/{id}/', 'ProductController@addproductimages')->middleware('add_product');
        });
        Route::group(['prefix' => 'images'], function () {
            Route::get('/display/{id}/', 'ProductController@displayProductImages')->middleware('view_product');
            Route::get('/add/{id}/', 'ProductController@addProductImages')->middleware('add_product');
            Route::post('/insertproductimage', 'ProductController@insertProductImages')->middleware('add_product');
            Route::get('/editproductimage/{id}', 'ProductController@editProductImages')->middleware('edit_product');
            Route::post('/updateproductimage', 'ProductController@updateproductimage')->middleware('edit_product');
            Route::post('/deleteproductimagemodal', 'ProductController@deleteproductimagemodal')->middleware('edit_product');
            Route::post('/deleteproductimage', 'ProductController@deleteproductimage')->middleware('edit_product');
        });
        Route::group(['prefix' => 'attach/attribute'], function () {
            Route::get('/display/{id}', 'ProductController@addproductattribute')->middleware('view_product');
            Route::group(['prefix' => '/default'], function () {
                Route::post('/', 'ProductController@addnewdefaultattribute')->middleware('view_product');
                Route::post('/edit', 'ProductController@editdefaultattribute')->middleware('edit_product');
                Route::post('/update', 'ProductController@updatedefaultattribute')->middleware('edit_product');
                Route::post('/deletedefaultattributemodal', 'ProductController@deletedefaultattributemodal')->middleware('edit_product');
                Route::post('/delete', 'ProductController@deletedefaultattribute')->middleware('edit_product');
                Route::group(['prefix' => '/options'], function () {
                    Route::post('/add', 'ProductController@showoptions')->middleware('view_product');
                    Route::post('/edit', 'ProductController@editoptionform')->middleware('edit_product');
                    Route::post('/update', 'ProductController@updateoption')->middleware('edit_product');
                    Route::post('/showdeletemodal', 'ProductController@showdeletemodal')->middleware('edit_product');
                    Route::post('/delete', 'ProductController@deleteoption')->middleware('edit_product');
                    Route::post('/getOptionsValue', 'ProductController@getOptionsValue')->middleware('edit_product');
                    Route::post('/currentstock', 'ProductController@currentstock')->middleware('view_product');
                });
            });
        });
    });

    Route::group(['prefix' => 'products/attributes'], function () {
        Route::get('/', 'ProductAttributesController@display')->middleware('view_product');
        Route::get('/add', 'ProductAttributesController@add')->middleware('view_product');
        Route::post('/insert', 'ProductAttributesController@insert')->middleware('view_product');
        Route::get('/edit/{id}', 'ProductAttributesController@edit')->middleware('view_product');
        Route::post('/update', 'ProductAttributesController@update')->middleware('view_product');
        Route::post('/delete', 'ProductAttributesController@delete')->middleware('view_product');

        Route::group(['prefix' => 'options/values'], function () {
            Route::get('/display/{id}', 'ProductAttributesController@displayoptionsvalues')->middleware('view_product');
            Route::post('/insert', 'ProductAttributesController@insertoptionsvalues')->middleware('edit_product');
            Route::get('/edit/{id}', 'ProductAttributesController@editoptionsvalues')->middleware('edit_product');
            Route::post('/update', 'ProductAttributesController@updateoptionsvalues')->middleware('edit_product');
            Route::post('/delete', 'ProductAttributesController@deleteoptionsvalues')->middleware('edit_product');
            Route::post('/addattributevalue', 'ProductAttributesController@addattributevalue')->middleware('edit_product');
            Route::post('/updateattributevalue', 'ProductAttributesController@updateattributevalue')->middleware('edit_product');
            Route::post('/checkattributeassociate', 'ProductAttributesController@checkattributeassociate')->middleware('edit_product');
            Route::post('/checkvalueassociate', 'ProductAttributesController@checkvalueassociate')->middleware('edit_product');
        });
    });

    Route::group(['prefix' => 'products/reviews'], function () {
        Route::get('/', 'ProductController@reviews')->middleware('view_reviews');
        Route::get('/edit/{id}/{status}', 'ProductController@editreviews')->middleware('edit_reviews');
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'OrdersController@display')->middleware('view_order');
        Route::get('/vieworder/{id}', 'OrdersController@vieworder');
        Route::post('/updateOrder', 'OrdersController@updateOrder');
        Route::post('/deleteOrder', 'OrdersController@deleteOrder');
        Route::get('/invoiceprint/{id}', 'OrdersController@invoiceprint');
        
    });

    Route::group(['prefix' => 'orderstatus'], function () {
        Route::get('/', 'SiteSettingController@orderstatus');
        Route::get('/addorderstatus', 'SiteSettingController@addorderstatus');
        Route::post('/addNewOrderStatus', 'SiteSettingController@addNewOrderStatus');
        Route::get('/editorderstatus/{id}', 'SiteSettingController@editorderstatus');
        Route::post('/updateOrderStatus', 'SiteSettingController@updateOrderStatus');
        Route::post('/deleteOrderStatus', 'SiteSettingController@deleteOrderStatus');
    });
});
