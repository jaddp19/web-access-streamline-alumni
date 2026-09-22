<?php

use App\Models\Batch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public ?int $batch_year = null;

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
                Rule::unique('batches', 'batch_name'),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'batch_year.required'  => 'Please enter a batch year.',
            'batch_year.integer'   => 'Batch year must be a valid number.',
            'batch_year.min'       => 'Batch year must be later than 1900.',
            'batch_year.max'       => 'Batch year cannot be in the future.',
            'batch_year.unique'    => 'This batch year already exists.',
        ];
    }

    // =========================================================
    //  CREATE
    // =========================================================

    public function create()
    {
        $validated = $this->validate();

        try {
            Batch::create([
                'batch_name' => (string) $validated['batch_year'],
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                $this->addError('batch_year', 'This batch year was just created. Please try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not create batch. Please try again.');
            return;
        }

        // Bust any caches that list batches (dashboard selector, counts, etc.).
        Cache::forget('dept:ph_count'); // in case any dashboard uses batch counts

        session()->flash('success', "Batch {$validated['batch_year']} created successfully.");

        $this->reset('batch_year');
    }
};