<?php

use App\Models\Course;
use App\Models\Department;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public Course $course;
    public string $course_title = '';
    public string $course_desc = '';
    public string $course_code = '';
    public string $course_type = 'non-board';
    public bool $is_active = true;
    public ?int $department_id = null;

    protected function rules()
    {
        return [
            'course_title'  => 'required|string|max:255|unique:courses,course_title,' . $this->course->id,
            'course_code'   => 'required|string|max:50|unique:courses,course_code,' . $this->course->id,
            'course_desc'   => 'required|string|max:1000',
            'course_type'   => 'required|in:board,non-board',
            'is_active'     => 'boolean',
            'department_id' => 'required|exists:departments,id',
        ];
    }

    public function messages()
    {
        return [
            'course_title.required'  => 'Course name is required.',
            'course_title.max'       => 'Course name cannot exceed 255 characters.',
            'course_title.unique'    => 'Course name must be unique.',
            'course_code.required'   => 'Course code is required.',
            'course_code.max'        => 'Course code cannot exceed 50 characters.',
            'course_code.unique'     => 'Course code must be unique.',
            'course_desc.required'   => 'Please provide a short description.',
            'course_type.required'   => 'Please select a course type.',
            'department_id.required' => 'Department selection is required.',
            'department_id.exists'   => 'Selected department does not exist.',
        ];
    }

    public function mount(Course $course)
    {
        $this->course         = $course;
        $this->course_title   = $course->course_title;
        $this->course_code    = $course->course_code;
        $this->course_desc    = $course->course_desc;
        $this->course_type    = $course->course_type;
        $this->is_active      = $course->is_active;
        $this->department_id  = $course->department_id;
    }

    public function update()
    {
        $validated = $this->validate();

        $this->course->update([
            'course_title'  => $this->sanitizeData($validated['course_title']),
            'course_slug'   => Str::slug($validated['course_title']),
            'course_desc'   => $this->sanitizeData($validated['course_desc']),
            'course_code'   => $this->sanitizeData($validated['course_code']),
            'course_type'   => $validated['course_type'],
            'is_active'     => $validated['is_active'],
            'department_id' => $validated['department_id'],
        ]);

        session()->flash('success', 'Course updated successfully.');
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
        return Department::select('id', 'dept_name')->where('is_active', true)->orderBy('dept_name')->get();
    }
};
