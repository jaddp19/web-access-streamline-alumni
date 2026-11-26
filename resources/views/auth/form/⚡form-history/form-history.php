<?php

use App\Models\FormHistory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-form')] class extends Component
{
    #[Computed]
    public function histories()
    {
        return FormHistory::query()
            ->where('user_id', Auth::id())
            ->with('approvedBy:id,first_name,middle_name,last_name,name')
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->get();
    }
};
