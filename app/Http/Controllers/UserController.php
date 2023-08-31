<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\forgotPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
