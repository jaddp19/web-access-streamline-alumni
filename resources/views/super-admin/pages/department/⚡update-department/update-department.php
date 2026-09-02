<?php

use App\Models\Department;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithFileUploads;

    public Department $department;
    public string $dept_name = '';
    public string $dept_code = '';
    public string $dept_desc = '';
    public bool $is_active = true;
    public $dept_logo;

    public function mount(Department $department)
    {
        $this->department = $department;
        $this->dept_name  = $department->dept_name;
        $this->dept_code  = $department->dept_code;
        $this->dept_desc  = $department->dept_desc;
        $this->is_active  = $department->is_active;
    }

    protected function rules()
    {
        return [
            'dept_name' => 'required|string|min:3|max:255|unique:departments,dept_name,' . $this->department->id,
            'dept_code' => 'required|string|max:20|unique:departments,dept_code,' . $this->department->id,
            'dept_desc' => 'required|string|max:1000',
            'dept_logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'dept_name.required' => 'The department name is required.',
            'dept_name.min'      => 'The department name must be at least 3 characters.',
            'dept_name.max'      => 'The department name may not be greater than 255 characters.',
            'dept_name.unique'   => 'That department name is already taken.',
            'dept_code.required' => 'A department code is required.',
            'dept_code.unique'   => 'That department code is already taken.',
            'dept_desc.required' => 'Please provide a short description.',
            'dept_logo.image'    => 'The logo must be an image (JPG, PNG).',
            'dept_logo.max'      => 'The logo must not be larger than 2MB.',
        ];
    }

    public function update()
    {
        $validated = $this->validate();

        $data = [
            'dept_name' => trim(strip_tags($validated['dept_name'])),
            'dept_slug' => Str::slug($validated['dept_name']),
            'dept_code' => strtoupper(trim(strip_tags($validated['dept_code']))),
            'dept_desc' => trim(strip_tags($validated['dept_desc'])),
            'is_active' => $validated['is_active'],
        ];

        if ($this->dept_logo) {
            // Remove the old logo so orphaned files don't pile up in storage.
            if ($this->department->dept_logo) {
                Storage::disk('public')->delete($this->department->dept_logo);
            }

            $data['dept_logo'] = $this->dept_logo->store('department-logos', 'public');
        }

        $this->department->update($data);

        session()->flash('success', 'Department updated successfully.');
        return redirect()->route('super-admin.department.view');
    }
};
