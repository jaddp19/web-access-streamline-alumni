<?php

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Batch;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public Batch $batch;
    public ?int $batch_year = null;

    public function mount(Batch $batch)
    {
        $this->batch = $batch;
        $this->batch_year = (int) $batch->batch_name;
    }

    protected function rules()
    {
        return [
            'batch_year' => 'required|integer|min:1900|max:' . date('Y') .
                '|unique:batches,batch_name,' . $this->batch->id,
        ];
    }

    protected function messages()
    {
        return [
            'batch_year.required' => 'Please enter a batch year.',
            'batch_year.integer' => 'Batch year must be a valid number.',
            'batch_year.min' => 'Batch year must be later than 1900.',
            'batch_year.max' => 'Batch year cannot be in the future.',
            'batch_year.unique' => 'This batch year already exists.',
        ];
    }

    public function update()
    {
        $this->validate();

        $this->batch->update([
            'batch_name' => (string) $this->batch_year,
        ]);

        session()->flash('success', "Batch {$this->batch_year} updated successfully.");
        redirect()->route('super-admin.batch.view');
    }
};
