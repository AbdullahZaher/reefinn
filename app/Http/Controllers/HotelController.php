<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;
use App\Http\Requests\HotelRequest;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class HotelController extends Controller
{
    public function update(GeneralSettings $settings, HotelRequest $request) {
        $settings->hotel_name = $request->validated()['hotel_name'];
        $settings->branch_no = $request->validated()['branch_no'];
        $settings->phone = $request->validated()['phone'];
        $settings->commercial_register = $request->validated()['commercial_register'];
        $settings->tax_number = $request->validated()['tax_number'];
        $settings->checkout_default_time = $request->validated()['checkout_default_time'];

        if (array_key_exists('logo', $request->validated()) && $request->validated()['logo']) {
            if (File::exists(public_path($settings->logo))) File::delete(public_path($settings->logo));

            $imageName = time() . '_' . Str::random(6) . '.' . $request->validated()['logo']->extension();
            $destinationPath = 'storage/application/' . $imageName;
            $fullPath = public_path($destinationPath);

            Image::make($request->validated()['logo'])
            ->resize(150, 150, fn ($constraint) => $constraint->aspectRatio())
            ->save($fullPath);

            $settings->logo = $destinationPath;
        }

        $settings->save();

        redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Hotel information has been updated.')]);
    }

    public function destroy_logo(GeneralSettings $settings) {
        $logo_path = public_path($settings->logo);

        if (File::exists($logo_path)) File::delete($logo_path);

        $settings->logo = null;
        $settings->save();

        redirect()->back()->with('toast', ['type' => 'success', 'message' => __('Hotel logo has been removed.')]);
    }
}