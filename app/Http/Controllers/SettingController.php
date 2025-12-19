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
        ];
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'allow_registration' => 'required|in:0,1',
        ]);

        Setting::set('allow_registration', $request->allow_registration);

        alert()->success('Berhasil!', 'Pengaturan sistem telah diperbarui.');
        return back();
    }
}
