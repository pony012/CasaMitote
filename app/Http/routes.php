<?php
use Illuminate\Support\Facades\Request as rq;
/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/', function () {
    return view('app');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

    /**
    * this route group is going to work as an API-RESTFUL
    * @author Ramón Lozano <ramon.lozano@ttr.com.mx>
    * @param Array options (Look for them in {@linkhttps://laravel.com/docs/5.1/routing#route-groups})
    * @param Callback
    * @since 0.1.0
    * @version 0.1.0
    */
    Route::group(['prefix' => env('APP_API', 'API')], function () {

    	
    });

});
