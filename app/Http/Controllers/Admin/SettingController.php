<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HospitalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = HospitalSetting::where('branch_id', auth()->user()->branch_id ?? 1)
            ->get()
            ->pluck('value', 'key');

        return view('pages.admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            HospitalSetting::updateOrCreate(
                ['branch_id' => auth()->user()->branch_id ?? 1, 'key' => $key],
                ['value' => $value, 'group' => 'general']
            );
            Cache::forget("setting." . (auth()->user()->branch_id ?? 1) . ".{$key}");
        }

        return back()->with('success', 'Settings saved.');
    }
}
