<?php

use App\Models\Batch;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    protected int $perPage = 10;

    #[Computed]
    public function batches()
    {
        return Batch::query()
            ->select('id', 'batch_name', 'created_at')
            ->orderByDesc('batch_name')
            ->paginate($this->perPage);
    }
};