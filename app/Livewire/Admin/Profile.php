<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Helpers\Cmail;

class Profile extends Component
{
    public $tab = null;
    public $tabname = 'personal_details';
    protected $queryString = ['tab'=>['keep'=>true]];

    public $name, $email, $username, $bio;

    public $current_password, $new_password, $new_password_confirmation;

    protected $listeners = [
        'updateProfile' => '$refresh'
    ];

    public function selectTab($tabname){
        $this->tab = $tabname;
    }

    public function mount(){
        $this->tab = Request('tab') ? Request('tab') : $this->tabname;

        //Populate
        $user = User::find(Auth::id());
        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->bio = $user->bio;
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

    public function render()
    {
        return view('livewire.admin.profile',[
            'user' => User::find(Auth::id()),
        ]);
    }
}
