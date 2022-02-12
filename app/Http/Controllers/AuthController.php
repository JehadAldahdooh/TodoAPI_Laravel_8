<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AuthController extends Controller {
    public $loginAfterSignUp = false;

    public function login( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ] );
        if ( $validator->fails() ) {
            return response()->json( $validator->errors(), 422 );
        }

        if ( ! $token = JWTAuth::attempt( $validator->validated() ) ) {
            return response()->json( [ 'error' => 'Unauthorized',
            'message' => 'Incorrect' ], 401 );
        }

        return response()->json( [
            'message' => 'Logged In Successfully',
            'token' => $token
        ], 200 );
    }

    public function register( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ] );

        if ( $validator->fails() ) {
            return response()->json( $validator->errors()->toJson(), 400 );
        }
        try {
            $user = new User();
            $user->email = $request->email;
            $user->password = bcrypt( $request->password );
            $user->save();

            if ( $this->loginAfterSignUp ) {
                return $this->login( $request );
            }

            return response()->json( [
                'message' => 'Registered Successfully',
                'user' => $user
            ], 201 );

        } catch( \Exception $e ) {
            return response()->json( [
                'status' => false,
                'message' => $e->getMessage()
            ] );
        }
    }

    public function change_pass( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'email' => 'required|email',
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',

        ] );
        if ( $validator->fails() ) {
            return response()->json( $validator->errors(), 422 );
        }
        $user = User::whereEmail( $request->email )->first();
        $user->update( [
            'password'=>bcrypt( $request->new_password )
        ] );
        try {
            $refreshed = JWTAuth::refresh( JWTAuth::getToken() );
            $user = JWTAuth::setToken( $refreshed )->toUser();
            return response()->json( [
                'data' => 'Password changed successfully.',
                'new_token'=> $refreshed
            ], 200 );

        } catch ( JWTException $e ) {
            return response()->json( [
                'status' => false,
                'message' => $e->getMessage()
            ] );
        }
    }

    public function logout( Request $request ) {
        $this->validate( $request, [
            'token' => 'required'
        ] );
        try {
            auth()->logout();
            return response()->json( [
                'status' => true,
                'message' => 'logout'
            ] );
        } catch( JWTException $e ) {
            return response()->json( [
                'status' => false,
                'message' => $e->getMessage()
            ] );
        }
    }

}
