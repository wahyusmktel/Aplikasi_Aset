<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'allow_registration' => Setting::get('allow_registration', '1'),
            'app_logo' => Setting::get('app_logo'),
            'kop_surat' => Setting::get('kop_surat'),
        ];
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'allow_registration' => 'sometimes|in:0,1',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'kop_surat' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        if ($request->has('allow_registration')) {
            Setting::set('allow_registration', $request->allow_registration);
        }

        if ($request->hasFile('app_logo')) {
            // Delete old file if exists
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('app_logo')->store('settings', 'public');
            Setting::set('app_logo', $path);
        }

        if ($request->hasFile('kop_surat')) {
            // Delete old file if exists
            $oldKopSurat = Setting::get('kop_surat');
            if ($oldKopSurat && Storage::disk('public')->exists($oldKopSurat)) {
                Storage::disk('public')->delete($oldKopSurat);
            }
            $path = $request->file('kop_surat')->store('kop_surat', 'public');
            Setting::set('kop_surat', $path);
        }

        alert()->success('Berhasil!', 'Pengaturan sistem telah diperbarui.');
        return back();
    }

    public function deleteKopSurat()
    {
        $kopSurat = Setting::get('kop_surat');
        if ($kopSurat && Storage::disk('public')->exists($kopSurat)) {
            Storage::disk('public')->delete($kopSurat);
        }
        Setting::where('key', 'kop_surat')->delete();

        alert()->success('Berhasil!', 'Kop Surat berhasil dihapus.');
        return back();
    }
}
