<?php

use App\Models\Course;
use App\Models\Department;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public string $course_title = '';
    public string $course_desc = '';
    public string $course_code = '';
    public string $course_type = 'non-board';
    public bool $is_active = true;
    public ?int $department_id = null;

    // =========================================================
    //  COMPUTED — plain arrays, safe across cache boundary
    // =========================================================

    #[Computed]
    public function departments(): array
    {
        $cacheKey = 'courses:active-departments:v1';
        $cached   = Cache::get($cacheKey);

        if (! is_array($cached)) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if ($cached === null) {
            $cached = Department::query()
                ->where('is_active', true)
                ->orderBy('dept_name')
                ->get(['id', 'dept_name'])
                ->map(fn ($d) => ['id' => (int) $d->id, 'name' => (string) $d->dept_name])
                ->all();

            Cache::put($cacheKey, $cached, now()->addMinutes(5));
        }

        return $cached;
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'course_title' => [
                'required', 'string', 'min:2', 'max:255',
                Rule::unique('courses', 'course_title'),
            ],
            'course_code' => [
                'required', 'string', 'min:2', 'max:50',
                Rule::unique('courses', 'course_code'),
            ],
            'course_desc' => ['required', 'string', 'max:1000'],
            'course_type' => ['required', Rule::in(['board', 'non-board'])],
            'department_id' => [
                'required', 'integer',
                Rule::exists('departments', 'id'),
            ],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_title.required'  => 'Course name is required.',
            'course_title.min'       => 'Course name must be at least 2 characters.',
            'course_title.max'       => 'Course name cannot exceed 255 characters.',
            'course_title.unique'    => 'Course name must be unique.',
            'course_code.required'   => 'Course code is required.',
            'course_code.min'        => 'Course code must be at least 2 characters.',
            'course_code.max'        => 'Course code cannot exceed 50 characters.',
            'course_code.unique'     => 'Course code must be unique.',
            'course_desc.required'   => 'Please provide a short description.',
            'course_desc.max'        => 'The description may not be greater than 1000 characters.',
            'course_type.required'   => 'Please select a course type.',
            'course_type.in'         => 'The course type is invalid.',
            'department_id.required' => 'Department selection is required.',
            'department_id.exists'   => 'Selected department does not exist.',
        ];
    }

    // =========================================================
    //  CREATE
    // =========================================================

    public function create()
    {
        $validated = $this->validate();

        $courseTitle = $this->sanitize($validated['course_title']);
        $courseCode  = strtoupper($this->sanitize($validated['course_code']));
        $courseDesc  = $this->sanitize($validated['course_desc']);

        try {
            DB::transaction(function () use ($courseTitle, $courseCode, $courseDesc, $validated) {
                Course::create([
                    'course_title'  => $courseTitle,
                    'course_slug'   => $this->uniqueSlug($courseTitle),
                    'course_desc'   => $courseDesc,
                    'course_type'   => $validated['course_type'],
                    'course_code'   => $courseCode,
                    'is_active'     => $validated['is_active'] ?? true,
                    'department_id' => $validated['department_id'],
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                $field = str_contains($e->getMessage(), 'course_code')
                    ? 'course_code'
                    : 'course_title';

                $this->addError(
                    $field,
                    'This value was just taken by another course. Please try again.'
                );
                return;
            }

            report($e);
            session()->flash('error', 'Could not create course. Please try again.');
            return;
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not create course. Please try again.');
            return;
        }

        // Bust related caches.
        Cache::forget('courses:count');
        Cache::forget('dashboard:course-analytics');

        session()->flash('success', 'Course created successfully.');

        return redirect()->route('super-admin.courses.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    /**
     * Generate a slug that's guaranteed unique.
     * Appends -2, -3, … on collision.
     */
    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 2;

        while (Course::where('course_slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    protected function sanitize(mixed $data): mixed
    {
        return is_string($data) ? trim(strip_tags($data)) : $data;
    }
};