<?php

namespace App\Http\Controllers;

use App\Models\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\File;
use SawaStacks\Utils\Kropify;

class AdminController extends Controller
{
    public function adminDashboard(Request $request)
    {
        $data = [
            'pageTitle' => 'Dashboard'
        ];
        return view('back.pages.dashboard', $data);
    }

    public function logoutHandler(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();
        return redirect()->route('admin.login')->with('fail', 'You are now logged out!.');
    } // End Method

    public function profileView(Request $request)
    {
        $data = [
            'pageTitle' => 'Profile',
        ];

        return view('back.pages.profile', $data);
    }

    public function updateProfilePicture(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $path = 'images/users/';
        $file = $request->file('profilePictureFile');
        $old_picture = $user->getAttributes()['picture'];
        $filename = 'IMG_' . uniqid() . '.png';

        $upload = Kropify::getFile($file, $filename)
            ->setPath($path)
            ->save();

        if ($upload) {
            //delete old picture if exists
            if ($old_picture && File::exists(public_path($path . $old_picture))) {
                File::delete(public_path($path . $old_picture));
            }
            $user->update([
                'picture' => $filename,
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Profile picture updated successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to upload profile picture.',
            ]);
        }
    }

    public function generalSettings(Request $request)
    {
        $data = [
            'pageTitle' => 'General Settings'
        ];

        return view('back.pages.general-settings', $data);
    }

    public function updateLogo(Request $request){
        $settings = GeneralSettings::take(1)->first();

        if(!is_null($settings)){
            $path='/images/site/';
            $old_logo = $settings->site_logo;
            $file = $request->file('site_logo');
            $filename = 'logo_'.uniqid().'.png';

            if($request->hasFile('site_logo')){
                $upload = $file->move(public_path($path),$filename);

                if($upload){
                    if($old_logo != null && File::exists(public_path($path.$old_logo))){
                        File::delete(public_path($path.$old_logo));
                    }

                    $settings->update(['site_logo'=>$filename]);

                    return response()->json(['status' => 1, 'image_path'=>$path.$filename, 'message'=>'Site logo has been updated successfully.']);
                }
                else{
                    return response()->json(['status'=>0, 'message'=>'Something went wrong in uploading new logo.']);
                }
            }
        }else{
            return response()->json(['status'=>0,'message'=>'Make sure you updated general settings form first.']);
        }
    } 
}
