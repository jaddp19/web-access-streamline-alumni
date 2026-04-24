<?php

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AlumniProfile;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public ?int $batch_id = null;
    public string $gender = 'male'; // default
    public string $phone_number_1 = '';
    public ?string $phone_number_2 = null;
    public string $permanent_address = '';
    public string $current_address = '';

    public function mount()
    {
        $profile = AlumniProfile::where('user_id', Auth::id())->first();

        if ($profile) {
            $this->batch_id = $profile->batch_id;
            $this->gender = $profile->gender;
            $this->phone_number_1 = $profile->phone_number_1;
            $this->phone_number_2 = $profile->phone_number_2;
            $this->permanent_address = $profile->permanent_address;
            $this->current_address = $profile->current_address;
        }
    }

    protected function rules()
    {
        return [
            'batch_id' => 'required|exists:batches,id',
            'gender' => 'required|in:male,female',
            'phone_number_1' => 'required|string|max:20',
            'phone_number_2' => 'nullable|string|max:20',
            'permanent_address' => 'required|string|max:255',
            'current_address' => 'required|string|max:255',
        ];
    }

    public function saveProfile()
    {
        $this->validate();

        AlumniProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'batch_id' => $this->batch_id,
                'gender' => $this->gender,
                'phone_number_1' => $this->phone_number_1,
                'phone_number_2' => $this->phone_number_2,
                'permanent_address' => $this->permanent_address,
                'current_address' => $this->current_address,
            ]
        );

        session()->flash('success', 'Alumni profile saved successfully.');
        redirect()->route('alumni.profile');
    }

};
