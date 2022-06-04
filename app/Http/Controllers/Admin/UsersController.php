<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\User;
use App\RoleUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Request()->get('q')){
            $users = User::where('name', 'like', '%'.Request()->get('q').'%')->paginate(10);
        }else{
            $users = User::paginate(10);
        }
        //$users = User::with('roles')->paginate(10);

        return view('admin.users.index', ['users' => $users->appends(Input::except('page'))])->with('users', $users);
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::all()->pluck('name', 'id');
        $user_role = RoleUser::where('user_id', $id)->first();
        return view('admin.users.edit')->with(compact('user', 'roles','user_role'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'role' => 'required',
            'name' => 'required',
            'email' => 'required'
        ]);

        $user = User::find($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        $role = RoleUser::where('user_id', $id)->first();
        $role->role_id = $request->input('role');
        $role->save();

        return redirect()->back()->with('success', 'User has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return User::find($id)->delete();
    }
}
