<?php

namespace App\Http\Controllers\API;

use App\Models\Room;
use App\Models\User;

class UserController extends Controller
{

    public function index()
    {
        return User::get();
    }

    public function show($id)
    {
        return User::find($id);
    }

    public function store()
    {
        return User::create([
            'name' => request()->input('name'),
            'email' => request()->input('email'),
            'password' => bcrypt(request()->input('password')),
        ]);
    }

    public function update($id)
    {
        return tap(User::find($id))->update([
            'name' => request()->input('name'),
            'email' => request()->input('email'),
            'password' => bcrypt(request()->input('password')),
        ]);
    }

    public function destroy($id)
    {
        User::find($id)->delete();
    }

}
