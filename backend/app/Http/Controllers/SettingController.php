<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Breadcrumbs;
use Illuminate\Support\Facades\Redirect;


class SettingController extends Controller
{
    public function index()
    {
       Breadcrumbs::add('Dashboard', route('dashboard'));
        Breadcrumbs::add('Setting');
        Breadcrumbs::add('Configuration');

        $configuration = Settings::select('*')->get();
        $configuration_data = [];
        foreach ($configuration as $item) {
            $configuration_data[$item->meta_name] = $item->meta_value;
        }
        return view('configuration.Settings',compact('configuration_data'));
    }

    public function addSettings(Request $request,Settings $settings){
       if($request){
            foreach($request->except('_token')   as $key => $value){
                if($key != '_token'){
                    $setting = Settings::where('meta_name', $key)->first();
                    if ($setting) {
                        $setting->update([
                            'meta_value' => $value
                        ]);
                    } else {
                        Settings::create([
                            'meta_name' => $key,
                            'meta_value' => $value,
                            'status' => 0,
                            'updated_at' => time()
                        ]);
                    }
                }
            }
            return Redirect::route('setting.index')->with(['type'=>'success','msg' => 'Setting updated successfully.']);
       }
    }
}
