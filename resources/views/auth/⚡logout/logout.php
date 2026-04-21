<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public function logout()
    {
        Auth::logout();

        // Invalidate session
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // Clear any stored intended URL
        request()->session()->forget('url.intended');

        // Redirect to login page
        return redirect()->route('home');
    }
};