<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use Auth;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $profile = Profile::first();
        return view('admin.profile', compact('profile'));
    }

    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'college_name' => 'required|string|max:255',
            'name_in_hindi' => 'nullable|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|max:255',
            'lc_prefix' => ['nullable', 'string', 'max:30', 'regex:/^[A-Za-z0-9_\-\/]+$/'],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
        ]);

        $profile = Profile::first();

        if ($request->hasFile('logo') || $request->hasFile('banner')) {
            File::ensureDirectoryExists(public_path('uploads'));
        }

        $lcPrefix = null;
        if ($request->filled('lc_prefix')) {
            $lcPrefix = strtoupper(trim((string) $request->lc_prefix));
            $lcPrefix = trim(preg_replace('#/+#', '/', $lcPrefix), '/');
        }

        $data = [
            'user_id' => Auth::user()->id,
            'college_name' => $request->college_name,
            'name_in_hindi' => $request->name_in_hindi,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'address' => $request->address,
            'website' => $request->website,
            'lc_prefix' => $lcPrefix,
        ];

        if ($request->hasFile('logo')) {
            // Generate unique filename
            $file = $request->file('logo');
            $fname = 'logo_' . time() . '.' . $file->getClientOriginalExtension();

            // Delete old logo if it exists.
            if ($profile && $profile->logo && file_exists(public_path($profile->logo))) {
                unlink(public_path($profile->logo));
            }

            // Move file to public/uploads
            $file->move(public_path('uploads'), $fname);
            $data['logo'] = 'uploads/' . $fname; // Store relative path
        }
        // Banner 
        if ($request->hasFile('banner')) {
            // Generate unique filename
            $file = $request->file('banner');
            $fname = 'banner_' . time() . '.' . $file->getClientOriginalExtension();

            // Delete old banner if it exists.
            if ($profile && $profile->banner && file_exists(public_path($profile->banner))) {
                unlink(public_path($profile->banner));
            }

            // Move file to public/uploads
            $file->move(public_path('uploads'), $fname);
            $data['banner'] = 'uploads/' . $fname; // Store relative path
        }
        // 

        if ($profile) {
            $profile->update($data);
            $message = 'Profile updated successfully.';
        } else {
            Profile::create($data);
            $message = 'Profile created successfully.';
        }

        // Clear cached profile so the new logo/name appears everywhere immediately
        cache()->forget('college_profile');

        return redirect()->to(url('admin/profile'))->with('success', $message);
    }
}
?>
