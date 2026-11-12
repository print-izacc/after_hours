<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Models\Profile;

class ProfileController extends Controller
{
    // Display a listing of profiles
    public function index()
    {
        $profiles = Profile::latest()->paginate(10);

        return view('profiles.index', compact('profiles'));
    }

    // Store a newly created profile in the database
    public function store(StoreProfileRequest $request)
    {
        Profile::create($request->validated());

        return redirect()->back()->with('success', 'Profile created successfully!');
    }
}
