<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Preference;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return view('shopfront.account.dashboard');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_profile(Request $request, $id)
    {
        $clean_data = $request->validate([
            'phone' => 'max:21',
            'newsletter' => 'max:10',
            'alerts' => 'max:10'
        ]);

        $preference = Preference::find('id');

        $newsOn = (isset($clean_data['newsletter']))?'1':'0';
        $alertsOn = (isset($clean_data['alerts']))?'1':'0';

        if($preference){
            $preference->phone = $clean_data['phone'];
            $preference->newsletters = $newsOn;
            $preference->alerts = $alertsOn;

            $preference->save();
        }else{
            $preference = new Preference;
            $preference->user_id = $id;
            $preference->phone = $clean_data['phone'];
            $preference->newsletters = $newsOn;
            $preference->alerts = $alertsOn;

            $preference->save();
        }

        return back()->with('success', 'Your profile has been updated');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lists()
    {
        return view('shopfront.account.lists');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_lists(Request $request, $id)
    {
        //
    }

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function alerts()
    {
        return view('shopfront.account.alerts');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_alerts(Request $request, $id)
    {
        //
    }
}
