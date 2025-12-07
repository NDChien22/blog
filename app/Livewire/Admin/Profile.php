<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Helpers\Cmail;
use App\Models\UserSocialLink;

class Profile extends Component
{
    public $tab = null;
    public $tabname = 'personal_details';
    protected $queryString = ['tab'=>['keep'=>true]];

    public $name, $email, $username, $bio;

    public $current_password, $new_password, $new_password_confirmation;

    public $facebook_url, $instagram_url, $youtube_url, $linkedin_url, $twitter_url, $github_url;

    protected $listeners = [
        'updateProfile' => '$refresh'
    ];

    public function selectTab($tabname){
        $this->tab = $tabname;
    }

    public function mount(){
        $this->tab = Request('tab') ? Request('tab') : $this->tabname;

        //Populate
        $user = User::with('social_links')->findOrFail(Auth::id());
        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->bio = $user->bio;

        //Populate Social Links form
        if (!is_null($user->social_links)){
            $this->facebook_url = $user->social_links->facebook_url;
            $this->instagram_url = $user->social_links->instagram_url;
            $this->youtube_url = $user->social_links->youtube_url;
            $this->linkedin_url = $user->social_links->linkedin_url;
            $this->twitter_url = $user->social_links->twitter_url;
            $this->github_url = $user->social_links->github_url;
        }
    }

    public function updatePersonalDetails(){
        $user = User::find(Auth::id());

        $this->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,'.$user->id,
        ]);

        //Update User Info
        $user->name = $this->name;
        $user->username = $this->username;
        $user->bio = $this->bio;
        $update = $user->save();

        sleep(0.5);

        if($update){
            $this->dispatch('showToastr', ['type'=>'success', 'message'=>'Your personal details have been update successfully.']);
            $this->dispatch('updateTopUserInfo')->to(TopUserInfo::class);
        }else{
            $this->dispatch('showToastr', ['type' => 'error', 'message'=>'Somthing went wrong.']);
        }
    }

    public function updatePassword(){
        $user = User::findOrFail(Auth::id());

        //validate
        $this->validate([
            'current_password' => [
                'required',
                'min: 5',
                function($attribute, $value, $fail) use ($user) {
                    if(!Hash::check($value, $user->password)){
                        return $fail(__('Your current password does not match our records.'));
                    }
                }
            ],
            'new_password' => 'required|min:5|confirmed',
        ]);

        //Update Password
        $update = $user->update([
            'password' => Hash::make(($this->new_password))
        ]);

        if($update){
            //Send Email Notification
            $data = array(
                'user' =>$user,
                'new_password' => $this->new_password,
            );

            $mail_body = view('email-templates.password-changes-template', $data)->render();

            $mail_cofig = array(
                'recipient_address' => $user->email,
                'recipient_name' => $user->name,
                'subject' => 'Password Changed',
                'body' => $mail_body,
            );

            Cmail::send($mail_cofig);

            //Logout and Redirect to login
            Auth::logout();
            Session::flash('info', 'Your password has been changed successfully. Please login with your new password.');
            $this->redirectRoute('admin.login');
        }else{
            $this->dispatch('showToastr', ['type'=>'error', 'message'=>'Somthing went wrong.']);
        }
    }

    public function updateSocialLinks(){
        $this->validate([
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'github_url' => 'nullable|url',
        ]);

        //Get User Details
        $user = User::findOrFail(Auth::id());

        $data = array(
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'youtube_url' => $this->youtube_url,
            'linkedin_url' => $this->linkedin_url,
            'twitter_url' => $this->twitter_url,
            'github_url' => $this->github_url,
        );

        if(!is_null($user->social_links)){
            //update records
            $query = $user->social_links()->update($data);
        }else{
            $data['user_id'] = $user->id;
            $query = UserSocialLink::insert($data);
        }

        if($query){
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Your social links have been updated successfully.']);
        }else{
            $this->dispatch('showToastr', ['type' => 'error', 'message'=>'Somthing went wrong.']);
        }
    }

    public function render()
    {
        return view('livewire.admin.profile',[
            'user' => User::find(Auth::id()),
        ]);
    }
}
