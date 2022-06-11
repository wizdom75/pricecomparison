<?php

namespace App\Http\Controllers\Admin;

use App\Migration;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MigrationController extends Controller
{

    public function index() : View
    {
        $migrations = Migration::orderBy('id', 'DESC')->paginate(10);

        return view('admin.migrations.index')->with(compact('migrations'));
    }

    public function destroy($id)
    {
        $m = Migration::find($id);

        $m->delete();
        return back()->with('success', 'Delete successful');
    }
}
