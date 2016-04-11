<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Request;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        /**
        * Response Macro to return view, redirect or json, in case that the client
        * asks for json this macro always will return a json
        * @author Ramón Lozano <ramon.lozano@ttr.com.mx>
        * @param array $response
        * @param String $type = {json, view, redirect}
        * @param LaravelRoute $route
        * @param HTTP Error Code $code
        * @param array $params
        * @since 0.1.0
        * @version 0.1.0
        * @return VIEW/REDIRECT/JSON
        */
        $factory->macro( 'responseType', function ($response, $type = 'json', $route = '', $code = 200, $params=[] ) use ( $factory ) {
            if ( Request::wantsJson() ) {
                foreach ( $response as $key => $value ) {
                    if( $value instanceof \Illuminate\Pagination\LengthAwarePaginator ) {
                        $response['total'] = $value->total();
                        $pages = (int)( $response['total'] / PER_PAGE );
                        if ( $response['total']%PER_PAGE > 0 ) {
                            $pages = $pages + 1;
                        }
                        $response['pages'] = $pages;
                        $response[$key]    = $value->items();
                    }
                }
                return $factory->json( $response, $code );
            }else{
                switch ( $type ) {
                    case 'view':
                        return $factory->view( $route, $response, $code );
                        break;

                    case 'json':
                        return $factory->json( $response, $code );
                        break;

                    case 'redirect':
                        if( $code === 200 ){
                            $code = 302;
                        }
                        return $factory->redirectToRoute( $route, $response, $code )->with( $params ); //redirect flash data
                        break;

                    default:
                        return $factory->json( [] , 204 );
                        break;
                }
            }
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
