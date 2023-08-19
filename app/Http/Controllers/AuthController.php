<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register (Request $request)
    {
        if (!$request['first_name'] && !$request['last_name']){
            return response(['message'=>'First name and last name required!!'], 403);
        }
        if ($request['password'] !=$request['password confirmation']){
            return response(['message'=>'password mismatch'], 403);
        }
        if (strlen($request['password'])<8){
            return response(['message'=> 'password must be up to 8 characters!!'], 403);
        }

        // check if account exist
        $email = User::where('email', $request['email'])->first();
        if ($email){
            return response(['message'=>'this user already exist, please login!!'], 403);
        }

        $user = User::create([
            'first_name'=>$request['first_name'],
            'last_name'=> $request['last_name'],
            'email'=> $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        $token = $user->createToken(time())->plainTextToken;
        $response = [
            'user'=>$user,
            'token'=>$token,
        ];

        return response(['success'=> true, 'message'=> 'user created successfuly'], 201);
    }

    public function registerDriver (Request $request)
    {
        if (!$request['first_name'] && !$request['last_name']){
            return response(['message'=>'First name and last name required!!'], 403);
        }
        if ($request['password'] !=$request['password confirmation']){
            return response(['message'=>'password mismatch'], 403);
        }
        if (strlen($request['password'])<8){
            return response(['message'=> 'password must be up to 8 characters!!'], 403);
        }

        // check if account exist
        $email = Driver::where('email', $request['email'])->first();
        // $email = User::where('email', $request['email'])->first();
        if ($email){
            return response(['message'=>'this user already exist, please login!!'], 403);
        }

        $driver = Driver::create([
            'first_name'=>$request['first_name'],
            'last_name'=> $request['last_name'],
            'email'=> $request['email'],
            'password' => bcrypt($request['password']),
            'phone_number' => $request['phone_number'],
            'gender' => $request['gender'],
            'type_of_car' => $request['type_of_car'],
            'vehicle_license_number' =>$request['vehicle_license_number'],
        ]);

        $token = $driver->createToken(time())->plainTextToken;

        return response(['success'=> true, 'message'=> 'Driver created successfuly'], 201);
    }


    public function login(Request $request)
{
    $inputs = $request->only('email', 'password', 'user_type');

    if ($inputs['user_type'] === 'user') {
        // Login for regular users
        $user = User::where('email', $inputs['email'])->first();

        if (!$user || !Hash::check($inputs['password'], $user->password)) {
            return response([
                'message' => 'Invalid email or password!',
                'success' => false
            ], 401);
        }
        $token = $user->createToken(time())->plainTextToken;

        return response([
            'message' => 'User login successful!',
            'success' => true,
            'token' => $token
        ], 200);

    } elseif ($inputs['user_type'] === 'driver') {
        // Login for drivers
        $driver = Driver::where('email', $inputs['email'])->first();

        if (!$driver || !Hash::check($inputs['password'], $driver->password)) {
            return response([
                'message' => 'Invalid email or password!',
                'success' => false
            ], 401);
        }
        $token = $driver->createToken(time())->plainTextToken;

        return response([
            'message' => 'Driver login successful!',
            'success' => true,
            'token' => $token
        ], 200);
    } else {
        // Invalid user type
        return response([
            'message' => 'Invalid user type!',
            'success' => false
        ], 400);
    }
}

    public function logout(Request $request)
    {
        return[
            'status'=>true,
            'message'=> 'Loggeed Out'
        ];
    }
}
