<?php

use App\Models\Batch;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination; // 🔑 enable pagination methods

    #[Computed]
    public function totalBatchesCount()
    {
        return Batch::count();
    }

    #[Computed]
    public function batches()
    {
        return Batch::select('id', 'batch_year', 'created_at')->paginate(5);
    }
};