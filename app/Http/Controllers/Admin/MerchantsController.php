<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Merchant;
use Illuminate\Support\Facades\Input;

class MerchantsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $merchants = Merchant::orderBy('name', 'asc')->paginate(10);
        if(Request()->get('q') !== null){
            $q = Request()->get('q');
            $merchants = Merchant::where('name','like', '%'.$q.'%')->paginate(10);
        }


        return view('admin.merchants.index', ['merchants' => $merchants->appends(Input::except('page'))])->with('merchants', $merchants);
    }

    public function create()
    {
        return view('admin.merchants.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|unique:merchants|max:10',
            'userId' => 'required',
            'name' => 'required|max:60',
            'url' => 'required',
            'email' => 'required',
            'logo' => 'image|max:1999'

        ]);

        $m = Merchant::find($request->input('id'));
        if($m){
            echo 'This record is a duplicte';
        }else{


        $merchant = new Merchant;

        $merchant->id = $request->input('id');
        $merchant->user_id = (int)$request->input('userId');
        $merchant->name = $request->input('name');
        $merchant->slug = makeSlug($merchant->name);
        $merchant->url = $request->input('url');
        $merchant->email = $request->input('email');
        $merchant->address_line_1 = $request->input('address_line_1');
        $merchant->address_line_2 = $request->input('address_line_2');
        $merchant->county = $request->input('county');
        $merchant->city = $request->input('city');
        $merchant->post_code = $request->input('post_code');
        $merchant->strapline = $request->input('strapline');
        $merchant->description = $request->input('description');
        $merchant->is_valid = (int)$request->input('is_valid');
        if($request->hasFile('logo')){
            //get the file extension
            $ext = $request->file('logo')->getClientOriginalExtension();
            $filename_save = 'logo.'.$ext;

            $file = $request->file('logo')->storeAs('public/merchants/'.$request->input('id'), $filename_save);
            $logo_path = 'storage/merchants/'.$request->input('id').'/'.$filename_save;
            $merchant->logo = $logo_path;
        }

        $merchant->save();
    }
        return redirect('/admin/merchants')->with('success', 'Listing added.');

    }

    public function show($id)
    {
        $merchant = Merchant::find($id);
        return view('admin.merchants.show')->with('merchant', $merchant);
    }

    public function edit($id)
    {
        $merchant = Merchant::where('id', $id)->first();
        return view('admin.merchants.edit')->with('merchant', $merchant);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'userId' => 'required',
            'name' => 'required|max:60',
            'url' => 'required',
            'email' => 'required',
            'logo' => 'image|max:1999'

        ]);

        $merchant = Merchant::where('id', $id)->first();

        $merchant->user_id = $request->input('userId');
        $merchant->name = $request->input('name');
        $merchant->url = $request->input('url');
        $merchant->email = $request->input('email');
        $merchant->address_line_1 = $request->input('address_line_1');
        $merchant->address_line_2 = $request->input('address_line_2');
        $merchant->county = $request->input('county');
        $merchant->city = $request->input('city');
        $merchant->post_code = $request->input('post_code');
        $merchant->strapline = $request->input('strapline');
        $merchant->description = $request->input('description');
        $merchant->is_valid = $request->input('is_valid');
        if($request->hasFile('logo')){
            //get the file extension
            $ext = $request->file('logo')->getClientOriginalExtension();
            $filename_save = 'logo.'.$ext;

            $file = $request->file('logo')->storeAs('public/merchants/'.$request->input('id'), $filename_save);
            $logo_path = 'storage/merchants/'.$request->input('id').'/'.$filename_save;
            $merchant->logo = $logo_path;
        }


        $merchant->save();
        return redirect('/admin/merchants')->with('success', 'Listing '.$merchant->title .' update.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $merchant = Merchant::find($id);
        $merchant->delete();

        return redirect()->back()->with('success', 'Merchant '.$merchant->name.' has been deleted.');
    }

    /**
     * Method for displaying a mercants csv form
     */
    public function csvform()
    {
        return view('admin.merchants.csv-form');
    }

    /**
     * Method for processing the csv file
     */
    public function csvStore(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,txt'
        ]);

        if($request->hasFile('file'))
        {
            //get the file extension
            $ext = $request->file('file')->getClientOriginalExtension();
            $filename_save = 'merchants.'.$ext;
            $file = $request->file('file')->storeAs('public/merchants', $filename_save);
        }
        $handle = fopen($request->file, 'r');
        fgetcsv($handle, 1000, ',');
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE){

            if(Merchant::find($request->affiliate.''.$data[0])){
                echo 'Duplicate record skipped';
                continue;
            }
            $merchant = new Merchant;
            $merchant->id = $request->affiliate.''.$data[0];
            $merchant->user_id = 1;
            $merchant->name = $data[1];
            $merchant->logo = $data[2];
            $merchant->slug = makeSlug($data[1]);
            $merchant->is_valid = 1;
            $merchant->save();
        }
        fclose($handle);
        return redirect('/admin/merchants')->with('success', 'Merchants successfully imported');
    }
}
