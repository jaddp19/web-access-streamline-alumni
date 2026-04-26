<?php

use App\Models\DegreeProgram;
use App\Models\Department;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public int $degree_program_id;
    public string $program_name = '';
    public ?int $department_id = null;

    protected function rules()
    {
        return [
            'program_name'  => 'required|string|max:255|unique:degree_programs,program_name,' . $this->degree_program_id,
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

    public function mount(DegreeProgram $program)
    {
        $this->degree_program_id = $program->id;
        $this->program_name      = $program->program_name;
        $this->department_id     = $program->department_id;
    }

    public function update()
    {
        $validated = $this->validate();

        $validated['program_name'] = $this->sanitizeData($validated['program_name']);

        DegreeProgram::where('id', $this->degree_program_id)->update([
            'program_name'  => $validated['program_name'],
            'department_id' => $validated['department_id'],
        ]);

        session()->flash('success', 'Degree Program updated successfully.');
        return redirect()->route('super-admin.courses.view');
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
