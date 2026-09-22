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

    public Department $department;

    public string $dept_name = '';
    public string $dept_code = '';
    public string $dept_desc = '';
    public bool $is_active = true;
    public $dept_logo;

    public function mount(Department $department): void
    {
        $this->department = $department;
        $this->dept_name  = $department->dept_name;
        $this->dept_code  = $department->dept_code;
        $this->dept_desc  = $department->dept_desc;
        $this->is_active  = (bool) $department->is_active;
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'dept_name' => [
                'required', 'string', 'min:3', 'max:255',
                Rule::unique('departments', 'dept_name')->ignore($this->department->id),
            ],
            'dept_code' => [
                'required', 'string', 'max:20',
                Rule::unique('departments', 'dept_code')->ignore($this->department->id),
            ],
            'dept_desc' => ['required', 'string', 'max:1000'],
            'dept_logo' => [
                'nullable',
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
            'dept_logo.image'    => 'The logo must be an image.',
            'dept_logo.mimes'    => 'The logo must be a JPG, PNG, or WEBP file.',
            'dept_logo.max'      => 'The logo must not be larger than 2MB.',
            'dept_logo.dimensions' => 'The logo must be between 64×64 and 2000×2000 pixels.',
        ];
    }

    /** Validate the logo the moment it's picked so errors surface early. */
    public function updatedDeptLogo(): void
    {
        $this->validateOnly('dept_logo');
    }

    // =========================================================
    //  UPDATE
    // =========================================================

    public function update()
    {
        $validated = $this->validate();

        $deptName = trim(strip_tags($validated['dept_name']));
        $deptCode = strtoupper(trim(strip_tags($validated['dept_code'])));
        $deptDesc = trim(strip_tags($validated['dept_desc']));

        $oldLogoPath = $this->department->dept_logo;
        $newLogoPath = null;

        $data = [
            'dept_name' => $deptName,
            'dept_slug' => $this->uniqueSlug($deptName),
            'dept_code' => $deptCode,
            'dept_desc' => $deptDesc,
            'is_active' => $validated['is_active'] ?? false,
        ];

        try {
            if ($this->dept_logo) {
                $newLogoPath = $this->dept_logo->store('department-logos', 'public');
                $data['dept_logo'] = $newLogoPath;
            }

            $this->department->update($data);
        } catch (\Illuminate\Database\QueryException $e) {
            // Roll back any uploaded file on failure.
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            if ($e->getCode() === '23000') {
                $field = str_contains($e->getMessage(), 'dept_code') ? 'dept_code' : 'dept_name';
                $this->addError($field, 'This value was just taken by another record. Please try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not update department. Please try again.');
            return;
        } catch (\Throwable $e) {
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            report($e);
            session()->flash('error', 'Could not update department. Please try again.');
            return;
        }

        // Delete old logo AFTER successful DB commit — only if we actually changed it.
        if ($newLogoPath && $oldLogoPath && ! $this->isSharedLogo($oldLogoPath)) {
            Storage::disk('public')->delete($oldLogoPath);
        }

        // Bust related caches.
        Cache::forget('assign:active-departments:v2');
        Cache::forget('dept:ph_count');

        session()->flash('success', 'Department updated successfully.');

        return redirect()->route('super-admin.department.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function uniqueSlug(string $name): string
    {
        // If the name hasn't changed, keep the current slug.
        if ($name === $this->department->dept_name) {
            return $this->department->dept_slug ?? Str::slug($name);
        }

        $base = Str::slug($name);
        $slug = $base;
        $i    = 2;

        while (
            Department::where('dept_slug', $slug)
                ->where('id', '!=', $this->department->id)
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Shared logos (external URLs, /imgs/ assets, CSAV default) must never
     * be deleted from the storage disk.
     */
    protected function isSharedLogo(?string $path): bool
    {
        if (blank($path)) return true;
        if (filter_var($path, FILTER_VALIDATE_URL)) return true;
        if (str_starts_with($path, '/imgs/')) return true;
        if ($path === 'CSAV-LOGO') return true;

        // Still referenced by another department? Don't touch it.
        return Department::query()
            ->where('dept_logo', $path)
            ->where('id', '!=', $this->department->id)
            ->exists();
    }
};