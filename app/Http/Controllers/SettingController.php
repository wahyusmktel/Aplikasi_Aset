<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'allow_registration' => Setting::get('allow_registration', '1'),
            'app_logo' => Setting::get('app_logo'),
            'app_kop_surat' => Setting::get('app_kop_surat'),
        ];
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'allow_registration' => 'sometimes|in:0,1',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'app_kop_surat' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->has('allow_registration')) {
            Setting::set('allow_registration', $request->allow_registration);
        }

        if ($request->hasFile('app_logo')) {
            $path = $request->file('app_logo')->store('settings', 'public');
            Setting::set('app_logo', $path);
        }

        if ($request->hasFile('app_kop_surat')) {
            $path = $request->file('app_kop_surat')->store('settings', 'public');
            Setting::set('app_kop_surat', $path);
        }

        alert()->success('Berhasil!', 'Pengaturan sistem telah diperbarui.');
        return back();
    }
}
