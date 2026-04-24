<?php

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public string $name;
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        $this->name = Auth::user()->name;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:users,name,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }

    public function message()
    {
        return [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'name.unique' => 'This name is already taken.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    public function update()
    {
        $this->validate();

        $user = Auth::user();

        // Update name
        $user->name = $this->name;

        // Update password only if provided
        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        session()->flash('success', 'Profile updated successfully.');
        $this->reset('password', 'password_confirmation');
        redirect()->route('alumni.profile');
    }

};
