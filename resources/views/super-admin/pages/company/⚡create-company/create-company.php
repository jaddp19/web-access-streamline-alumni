<?php

use App\Models\Company;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithFileUploads;

    public string $company_name = '';
    public string $company_address = '';
    public string $company_desc = '';
    public $company_logo = null;

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

        if ($this->company_logo) {
            $validated['company_logo'] = $this->company_logo->store('company-logos', 'public');
        }

        Company::create($validated);

        session()->flash('success', 'Company created successfully.');

        return $this->redirectRoute('super-admin.company.view', navigate: true);
    }
};