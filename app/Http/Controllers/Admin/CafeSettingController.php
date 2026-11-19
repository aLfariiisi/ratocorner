<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCafeSettingRequest;
use App\Models\CafeSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CafeSettingController extends Controller
{
    /**
     * Show the form for editing the cafe settings.
     */
    public function edit(): View
    {
        $setting = CafeSetting::getSettings();

        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Update the cafe settings.
     */
    public function update(UpdateCafeSettingRequest $request): RedirectResponse
    {
        $setting = CafeSetting::getSettings();
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('cafe', 'public');
        }

        unset($validated['logo']);

        $setting->update($validated);

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Pengaturan Cafe Ratocorner berhasil diperbarui.');
    }
}
