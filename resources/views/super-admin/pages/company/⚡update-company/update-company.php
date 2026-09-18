<?php

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithFileUploads;

    public Company $company;

    public string $company_name = '';
    public string $company_address = '';
    public string $company_desc = '';
    public $company_logo = null;
    public bool $remove_logo = false;

    public function mount(Company $company): void
    {
        $this->company          = $company;
        $this->company_name     = $company->company_name;
        $this->company_address  = $company->company_address ?? '';
        $this->company_desc     = $company->company_desc ?? '';
    }

    protected function rules(): array
    {
        return [
            'company_name'    => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_desc'    => ['nullable', 'string', 'max:2000'],
            'company_logo'    => ['nullable', 'image', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'company_logo.image'    => 'The logo must be a valid image file.',
            'company_logo.max'      => 'The logo must not exceed 2MB.',
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        // Handle logo change
        if ($this->company_logo) {
            // New file uploaded → delete old, store new
            if ($this->company->company_logo) {
                Storage::disk('public')->delete($this->company->company_logo);
            }
            $validated['company_logo'] = $this->company_logo->store('company-logos', 'public');
        } elseif ($this->remove_logo && $this->company->company_logo) {
            // User clicked "Remove" → delete and null it
            Storage::disk('public')->delete($this->company->company_logo);
            $validated['company_logo'] = null;
        } else {
            // Nothing changed → keep old value out of the update payload
            unset($validated['company_logo']);
        }

        $this->company->update($validated);

        session()->flash('success', 'Company updated successfully.');

        return $this->redirectRoute('super-admin.company.view');
    }
};