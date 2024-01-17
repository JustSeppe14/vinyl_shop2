<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;

class UsersBasic extends Component
{
    use WithPagination;
    public $search;
    public $orderBy = 'name';
    public $orderAsc = true;
    public $perPage = 6;

    public $loading = 'Please wait...';

    #[Validate([
        'editUser.name' => 'required|min:2|max:30|unique:users,name',
    ], as: [
        'editUser.name' => 'name for this user',
    ])]
    #[Validate([
        'editUser.email' => 'required|min:2|max:30|unique:users,email',
    ], as: [
        'editUser.email' => 'email for this user'
    ])]
    public $editUser = ['id'=>null,'name'=>null, 'email'=>null];

    public function resetValues(){
        $this->reset('editUser');
        $this->resetErrorBag();
    }

    public function edit(User $user)
    {
        $this->editUser = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    public function updateName(User $user)
    {
        $this->editUser['name'] = trim($this->editUser['name']);
        if (strtolower($this->editUser['name']) === strtolower($user->name)){
            $this->resetValues();
            return;
        }

        $this->validateOnly('editUser.name');
        $user->update([
            'name' => trim($this->editUser['name']),
        ]);

        $this->resetValues();

    }
    public function updateMail(User $user)
    {
        $this->editUser['email'] = trim($this->editUser['email']);

        if (strtolower($this->editUser['email']) === strtolower($user->email)){
            $this->resetValues();
            return;
        }

        $this->validateOnly('editUser.email');

        $user->update([
            'email' => trim($this->editUser['email'])
        ]);
        $this->resetValues();

    }

    public function resort($column)
    {
        $this->orderBy === $column ?
            $this->orderAsc = !$this->orderAsc :
            $this->orderAsc = true;
        $this->orderBy = $column;
    }

    #[On('delete-user')]
    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }

    #[Layout('layouts.vinylshop',['title'=>'Users (basic)','description'=>'Manage users (basic)',])]
    public function render()
    {
        $users = User::orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
            ->searchUser($this->search)
            ->paginate($this->perPage) ;
        return view('livewire.admin.users-basic',compact('users'));
    }
}
