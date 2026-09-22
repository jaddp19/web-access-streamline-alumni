<?php

use App\Models\Department;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithFileUploads;

    public string $dept_name = '';
    public string $dept_code = '';
    public string $dept_desc = '';
    public bool $is_active = true;
    public $dept_logo;

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'dept_name' => [
                'required', 'string', 'min:3', 'max:255',
                Rule::unique('departments', 'dept_name'),
            ],
            'dept_code' => [
                'required', 'string', 'max:20',
                Rule::unique('departments', 'dept_code'),
            ],
            'dept_desc' => ['required', 'string', 'max:1000'],
            'dept_logo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:min_width=64,min_height=64,max_width=2000,max_height=2000',
            ],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'dept_name.required' => 'The department name is required.',
            'dept_name.min'      => 'The department name must be at least 3 characters.',
            'dept_name.max'      => 'The department name may not be greater than 255 characters.',
            'dept_name.unique'   => 'That department name is already taken.',
            'dept_code.required' => 'A department code is required.',
            'dept_code.unique'   => 'That department code is already taken.',
            'dept_code.max'      => 'The department code may not be greater than 20 characters.',
            'dept_desc.required' => 'Please provide a short description.',
            'dept_desc.max'      => 'The description may not be greater than 1000 characters.',
            'dept_logo.required' => 'Please upload a department logo.',
            'dept_logo.image'    => 'The logo must be an image.',
            'dept_logo.mimes'    => 'The logo must be a JPG, PNG, or WEBP file.',
            'dept_logo.max'      => 'The logo must not be larger than 2MB.',
            'dept_logo.dimensions' => 'The logo must be between 64×64 and 2000×2000 pixels.',
        ];
    }

    // =========================================================
    //  CREATE
    // =========================================================

    public function create()
    {
        $validated = $this->validate();

        $deptName = trim(strip_tags($validated['dept_name']));
        $deptCode = strtoupper(trim(strip_tags($validated['dept_code'])));
        $deptDesc = trim(strip_tags($validated['dept_desc']));

        $logoPath = null;

        try {
            $logoPath = $this->dept_logo->store('department-logos', 'public');

            Department::create([
                'dept_name' => $deptName,
                'dept_slug' => $this->uniqueSlug($deptName),
                'dept_code' => $deptCode,
                'dept_desc' => $deptDesc,
                'dept_logo' => $logoPath,
                'is_active' => $validated['is_active'] ?? true,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Clean up the uploaded file if the DB insert failed.
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            if ($e->getCode() === '23000') {
                $field = str_contains($e->getMessage(), 'dept_code') ? 'dept_code' : 'dept_name';
                $this->addError($field, 'This value was just taken by another record. Please try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not create department. Please try again.');
            return;
        } catch (\Throwable $e) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            report($e);
            session()->flash('error', 'Could not create department. Please try again.');
            return;
        }

        // Bust related caches so lists everywhere are fresh.
        Cache::forget('dept:count');
        Cache::forget('assign:active-departments:v2');
        Cache::forget('dept:ph_count');

        session()->flash('success', 'Department created successfully.');

        return redirect()->route('super-admin.department.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    /**
     * Generate a slug that's guaranteed unique.
     * Appends -2, -3, … if the base slug already exists.
     */
    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 2;

        while (Department::where('dept_slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /** Live preview helper for the logo input. */
    public function updatedDeptLogo(): void
    {
        $this->validateOnly('dept_logo');
    }
};