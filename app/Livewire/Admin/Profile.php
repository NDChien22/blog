<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public $tab = null;
    public $tabname = 'personal_details';
    protected $queryString = ['tab'=>['keep'=>true]];

    public $name, $email, $username, $bio;

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

    public function render()
    {
        return view('livewire.admin.profile',[
            'user' => User::find(Auth::id()),
        ]);
    }
}
