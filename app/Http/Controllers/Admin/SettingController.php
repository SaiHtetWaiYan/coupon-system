<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::query()
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        $logoPath = Setting::getValue('logo_path');
        $logoUrl = $logoPath && Storage::disk('public')->exists($logoPath)
            ? Storage::disk('public')->url($logoPath)
            : null;

        return view('admin.settings.index', compact('settings', 'logoUrl'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        foreach ($request->validated('settings') as $key => $value) {
            $setting = Setting::query()->where('key', $key)->first();

            if ($setting) {
                Setting::setValue($key, $value, $setting->type, $setting->group);
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
        ]);

        $oldLogoPath = Setting::getValue('logo_path');
        if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
            Storage::disk('public')->delete($oldLogoPath);
        }

        $path = $request->file('logo')->store('logos', 'public');

        Setting::setValue('logo_path', $path, 'string', 'general');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Logo uploaded successfully.');
    }

    public function deleteLogo(): RedirectResponse
    {
        $logoPath = Setting::getValue('logo_path');

        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            Storage::disk('public')->delete($logoPath);
        }

        Setting::setValue('logo_path', '', 'string', 'general');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Logo deleted successfully.');
    }
}
