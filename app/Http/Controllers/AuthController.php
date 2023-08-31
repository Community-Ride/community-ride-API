<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use App\Mail\signupMail;
use Illuminate\Http\Request;
use App\Mail\signupMailDriver;
use App\Mail\forgotPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        if (!$request['first_name'] && !$request['last_name']) {
            return response(['message' => 'First name and last name required!!'], 403);
        }
        if ($request['password'] != $request['password confirmation']) {
            return response(['message' => 'password mismatch'], 403);
        }
        if (strlen($request['password']) < 8) {
            return response(['message' => 'password must be up to 8 characters!!'], 403);
        }

        // check if account exist
        $email = User::where('email', $request['email'])->first();
        if ($email) {
            return response(['message' => 'this user already exist, please login!!'], 403);
        }
        $emailDr = Driver::where('email', $request['email'])->first();
        if ($emailDr) {
            return response(['message' => 'this user already exist as a driver, please login!!'], 403);
        }

        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        //add queue to this mail function
        Mail::to($request['email'])->send(new signupMail($request['first_name'], $user['user_id']));

        event(new Registered($user));

        return response(['success' => true, 'message' => 'user created successfuly'], 201);
    }



    public function registerDriver(Request $request)
    {
        if (!$request['first_name'] && !$request['last_name']) {
            return response(['message' => 'First name and last name required!!'], 403);
        }
        if ($request['password'] != $request['password confirmation']) {
            return response(['message' => 'password mismatch'], 403);
        }
        if (strlen($request['password']) < 8) {
            return response(['message' => 'password must be up to 8 characters!!'], 403);
        }

        // check if account exist
        $emailDr = Driver::where('email', $request['email'])->first();
        // $email = User::where('email', $request['email'])->first();
        if ($emailDr) {
            return response(['message' => 'this user already exist, please login!!'], 403);
        }
        $email = User::where('email', $request['email'])->first();
        if ($email) {
            return response(['message' => 'this user already exist, please login!!'], 403);
        }

        $driver = Driver::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
            'phone_number' => $request['phone_number'],
            'gender' => $request['gender'],
            'type_of_car' => $request['type_of_car'],
            'vehicle_license_number' => $request['vehicle_license_number'],
        ]);

        //add queue to this mail function
        Mail::to($request['email'])->send(new signupMailDriver($request['first_name'], $driver['driver_id']));

        event(new Registered($driver));


        return response(['success' => true, 'message' => 'Driver created successfuly'], 201);
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


    
    public function forgotPassword(Request $request)
    {
        $email = $request['email'];

        $driver = Driver::where('email', $email)->first();
        $user = User::where('email', $email)->first();

        if ($driver) {
            $user_name = $driver->first_name;
            $last_name = $driver->last_name;

            // Send recovery email for driver
            Mail::to($email)->send(new forgotPasswordMail($user_name, $last_name));

            return response(['message' => 'Recovery email sent for driver'], 201);
        } elseif ($user) {
            $user_name = $user->first_name;
            $user_id = $user->id;

            // Send recovery email for user
            Mail::to($email)->send(new forgotPasswordMail($user_name, $user_id));

            return response(['message' => 'Recovery email sent for user'], 201);
        }

        return response(['message' => 'Email not found'], 404);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user(); // Get the authenticated user

        // Validate the request data
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ]);

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response(['message' => 'Current password is incorrect'], 400);
        }

        // Update the password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response(['message' => 'Password updated successfully'], 200);
    }



    public function logout(Request $request)
    {
        return [
            'status' => true,
            'message' => 'Loggeed Out'
        ];
    }
}
