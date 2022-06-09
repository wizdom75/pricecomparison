<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\Alert;
use App\RoleUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AlertsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Request()->get('q')){
            $alerts = Alert::where('email', 'like', '%'.Request()->get('q').'%')->paginate(10);
        }else{
            $alerts = Alert::orderBy('id', 'DESC')->paginate(10);
        }

        return view('admin.alerts.index')->with('alerts', $alerts);
    }

    public function edit($id)
    {
        $user = Alert::find($id);
        return view('admin.alerts.edit')->with(compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'role' => 'required',
            'name' => 'required',
            'email' => 'required'
        ]);

        $user = Alert::find($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();


        return redirect()->back()->with('success', 'Alert has been updated');
    }

    public function destroy($id)
    {
        return Alert::find($id)->delete();
    }
}
