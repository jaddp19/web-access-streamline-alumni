<?php

use App\Models\DegreeProgram;
use App\Models\Department;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public string $program_name = '';
    public ?int $department_id = null;

    protected function rules()
    {
        return [
            'program_name'  => 'required|string|max:255|unique:degree_programs,program_name',
            'department_id' => 'required|exists:departments,id',
        ];
    }

    public function messages()
    {
        return [
            'program_name.required' => 'Course name is required.',
            'program_name.string'   => 'Course name must be a string.',
            'program_name.max'      => 'Course name cannot exceed 255 characters.',
            'program_name.unique'   => 'Course name must be unique.',
            'department_id.required' => 'Department selection is required.',
            'department_id.exists'   => 'Selected department does not exist.',
        ];
    }

    public function create()
    {
        $validated = $this->validate();

        $validated['program_name'] = $this->sanitizeData($validated['program_name']);

        DegreeProgram::create([
            'program_name'  => $validated['program_name'],
            'department_id' => $validated['department_id'],
        ]);

        session()->flash('success', 'Degree Program created successfully.');
        // reset form
        $this->reset(['program_name', 'department_id']);
        redirect()->route('super-admin.courses.view');
    }

        protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }

    #[Computed]
    public function departments()
    {
        return Department::select('id', 'department_name')->get();
    }

};
