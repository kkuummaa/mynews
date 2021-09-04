<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Profile;

class ProfileController extends Controller
{
    //
    public function add()
    {
        return view('admin.profile.create');
    }
    
    public function create(Request $request)
    {
        // Validation
        $this->validate($request, Profile::$rules);
        
        return redirect('admin/profile/create');
    }
    
    public function edit(Request $request)
    {
        return view('admin.profile.edit');
    }
    
    public function update(Request $request)
    {
        return redirect('admin/profile/edit');
    }
}
