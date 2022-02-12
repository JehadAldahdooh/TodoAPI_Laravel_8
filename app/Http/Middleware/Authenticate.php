<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Authenticate extends Middleware {
    /**
    * Exclude these routes from authentication check.
    *
    * @var array
    */
    protected $except = [
        'api/v1/logout'
    ];

    /**
    * Ensure the user is authenticated.
    *
    * @param \Illuminate\Http\Request $request
    * @param \Closure $next
    * @return mixed
    */

    public function handle( $request, Closure $next ) {
        try {
            foreach ( $this->except as $excluded_route ) {
                if ( $request->path() === $excluded_route ) {
                    \Log::debug( "Skipping $excluded_route from auth check..." );
                    return $next( $request );
                }
            }
            \Log::debug( 'Authenticating... '. $request->url() );
            $user = JWTAuth::parseToken()->authenticate();
            $request->attributes->add( [
                'user' => $user
            ] );

            return $next( $request );
        } catch ( TokenExpiredException $e ) {

            \Log::debug( 'token expired' );
            try {
                $customClaims = [];
                $refreshedToken = JWTAuth::claims( $customClaims )
                ->refresh( JWTAuth::getToken() );
            } catch ( TokenExpiredException $e ) {
                return response()->json( [
                    'message' => 'The token has been expired',
                    'refresh' => false,
                ], 401 );
            } catch ( TokenBlacklistedException $e ) {
                \Log::debug( 'The token has been blacklisted' );
                return response()->json( [
                    'message' => 'The token has been blacklisted',
                ], 401 );
            }

            return response()->json( [
                'message' => 'The token has been expired and refreshed',
                'refresh' => [
                    'token' => $refreshedToken,
                ],
            ], 401 );
        } catch ( TokenInvalidException $e ) {
            \Log::debug( 'token invalid' );
            return response()->json( [
                'message' => 'The token is not valid',
            ], 401 );
        } catch ( TokenBlacklistedException $e ) {
            \Log::debug( 'The token has been blacklisted' );
            return response()->json( [
                'message' => 'The token has been blacklisted',
            ], 401 );
        } catch ( JWTException $e ) {
            \Log::debug( 'token absent' );
            return response()->json( [
                'message' => 'The token is absent',
            ], 401 );
        }

    }

    /**
    * Get the path the user should be redirected to when they are not authenticated.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return string|null
    */
    protected function redirectTo( $request ) {
        if ( ! $request->expectsJson() ) {
            return route( 'api/v1/signin' );
        }
    }
}
