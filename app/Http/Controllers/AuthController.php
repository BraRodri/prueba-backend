<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

    // Login
    public function auth(Request $request)
    {
        // Define validation rules for the incoming request.
        $rules = [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8'
        ];

        // Validate the incoming request data against the defined rules.
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return a JSON response with error details.
        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        try {
            // Attempt authentication using the provided credentials (username and password).
            $token = Auth::attempt($request->only('username', 'password'));

            // If authentication is successful, update the user's last login time in the database.
            if ($token) {
                $user = User::findOrFail(Auth::user()->id);
                $user->last_login = now();
                $user->save();

                // Return a JSON response with success meta details and token information.
                return response()->json([
                    'meta' => [
                        'success' => true,
                        'errors' => []
                    ],
                    'data' => [
                        'token' => $token,
                        'minutes_to_expire' => auth()->factory()->getTTL()
                    ]
                ]);
            } else {
                // If authentication fails due to incorrect credentials, return an error JSON response.
                return response()->json([
                    'meta' => [
                        'success' => false,
                        'errors' => ["Password incorrect for: $request->username"]
                    ]
                ], 401);
            }
        } catch (JWTException $e) {
            // If there is an error while attempting authentication, return an error JSON response.
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => [$e]
                ]
            ], 500);
        }
    }

}
