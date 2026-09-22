<?php

use App\Models\Batch;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public Batch $batch;
    public ?int $batch_year = null;

    public function mount(Batch $batch): void
    {
        $this->batch      = $batch;
        $this->batch_year = (int) $batch->batch_name;
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'batch_year' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('batches', 'batch_name')->ignore($this->batch->id),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'batch_year.required' => 'Please enter a batch year.',
            'batch_year.integer'  => 'Batch year must be a valid number.',
            'batch_year.min'      => 'Batch year must be later than 1900.',
            'batch_year.max'      => 'Batch year cannot be in the future.',
            'batch_year.unique'   => 'This batch year already exists.',
        ];
    }

    // =========================================================
    //  UPDATE
    // =========================================================

    public function update()
    {
        $validated = $this->validate();

        try {
            $this->batch->update([
                'batch_name' => (string) $validated['batch_year'],
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                $this->addError('batch_year', 'This batch year was just taken. Please try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not update batch. Please try again.');
            return;
        }

        session()->flash('success', "Batch {$validated['batch_year']} updated successfully.");

        return redirect()->route('super-admin.batch.view');
    }
};