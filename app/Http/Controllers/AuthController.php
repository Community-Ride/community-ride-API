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


        // $request->validate([
        //     'first_name' => 'required',
        //     'last_name' => 'required',
        //     'email' => 'required|email|unique:users',
        //     'password' => 'required|min:8',
        //     'user_type' => 'required|in:user,driver',
        //     'vehicle_license_number' => 'required_if:user_type,driver',
        //     'type_of_car' => 'required_if:user_type,driver',
        //     'gender' => 'required_if:user_type,driver',
        // ]);

        // echo "this reached here";
        // exit;
    
        // if (!$request['first_name'] && !$request['last_name']){
        //     return response(['message'=>'First name and last name required!!'], 403);
        // }
        // if ($request['password'] !=$request['password confirmation']){
        //     return response(['message'=>'password mismatch'], 403);
        // }
        // if (strlen($request['password'])<8){
        //     return response(['message'=> 'password must be up to 8 characters!!'], 403);
        // }

        // // check if account exist
        // $email = User::where('email', $request['email'])->first();
        // if ($email){
        //     return response(['message'=>'this user already exist, please login!!'], 403);
        // }
        // $email = Driver::where('email', $request['email'])->first();
        // if ($email){
        //     return response(['message'=>'this driver already exist, please login!!'], 403);
        // }

        
    
        // // Create the user
        // $user = User::create([
        //     'first_name' => $request->input('first_name'),
        //     'last_name' => $request->input('last_name'),
        //     'phone_number' => $request->input('phone_number'),
        //     'email' => $request->input('email'),
        //     'password' => bcrypt($request->input('password')),
        // ]);
    
        // // Create the driver if user_type is "driver"
        // if ($request->input('user_type') === 'driver') {
        //     $driver = Driver::create([
        //         'user_id' => $user->id,
        //         'gender' => $request->input('gender'),
        //         'type_of_car' => $request->input('type_of_car'),
        //         'vehicle_license_number' => $request->input('vehicle_license_number'),
        //     ]);
    
            
        // }

        // $token = $user->createToken(time())->plainTextToken;
        // $response = [
        //     'user'=>$user,
        //     'token'=>$token,
        // ];
    
        // // Return a success response
        // return response([ 'succes'=> true, 'message' => ' Rregistration successfull!!'], 201);
    }

    public function login (Request $request)
    {
        $inputs = $request->validate([
            'email'=> 'required|string',
            'password'=> 'required|string'
        ]);

        // check email
        $user = User::where('email', $inputs['email'])->first();

        // check password

        if (!$user || !Hash::check($inputs['password'], $user->password))
        {
            return response([
                'message'=> 'invalid email or password!!',
                'success'=> false
            ], 401);
        }

        $token = $user->createToken(time())->plainTextToken;

        return response(['success'=>true, 'message'=> 'logged in successfully!!', 'token'=>$token ], 200);
    }

    public function logout(Request $request)
    {
        return[
            'status'=>true,
            'message'=> 'Loggeed Out'
        ];
    }
}
