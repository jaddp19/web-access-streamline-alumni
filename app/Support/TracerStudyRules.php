<?php

namespace App\Support;

use App\Models\Batch;
use App\Models\UserProfile;
use Closure;
use Illuminate\Support\Facades\Auth;
use Propaganistas\LaravelPhone\Rules\Phone;

class TracerStudyRules
{
    /** Fallback earliest date when no batch is selected. */
    public const BOARD_EXAM_MIN_DATE = '2015-01-01';

    /**
     * Rules for a single step.
     *
     * @param  array{taken?:?string, rate?:string, batch_id?:int|string|null}  $ctx
     */
    public static function step(int $step, array $ctx = []): array
    {
        return match ($step) {
            1 => self::stepOne(),
            2 => self::stepTwo($ctx),
            3 => self::stepThree($ctx),   // ← now receives ctx
            4 => self::stepFour(),
            default => [],
        };
    }

    /**
     * Merged rules for the final submit.
     *
     * @param  array{taken?:?string, rate?:string, batch_id?:int|string|null}  $ctx
     */
    public static function all(array $ctx = []): array
    {
        return array_merge(
            self::stepOne(),
            self::stepTwo($ctx),
            self::stepThree($ctx),        // ← now receives ctx
            self::stepFour(),
        );
    }

    /**
     * All custom error messages.
     */
    public static function messages(): array
    {
        return [
            'gender.required'                        => 'Please select your sex.',
            'contact_number_1.phone'                 => 'Please enter a valid mobile number.',
            'contact_number_2.phone'                 => 'Please enter a valid alternate number.',
            'contact_number_1.required'              => 'Mobile number is required.',
            'street_address.required'                => 'Street address is required.',
            'address_type.required'                  => 'Please select whether you reside in the Philippines or abroad.',
            'regionCode.required'                    => 'Please select your region.',
            'provinceCode.required'                  => 'Please select your province.',
            'cityCode.required'                      => 'Please select your city/municipality.',
            'barangayCode.required'                  => 'Please select your barangay.',
            'intl_country.required_if'               => 'Please enter your country.',
            'intl_city.required_if'                  => 'Please enter your city.',
            'consentGiven.accepted'                  => 'You must agree to the Privacy Policy and Terms and Conditions before continuing.',
            'civil_status.required'                  => 'Please select your civil status.',
            'course_id.required'                     => 'Please select your program.',
            'batch_id.required'                      => 'Please select your year graduated.',

            // Board exam
            'board_taken.date'                       => 'Board exam date must be a valid date.',
            'board_taken.before_or_equal'            => 'Board exam date cannot be in the future.',
            'board_taken.required'                   => 'Please provide the board exam date — a rating was entered.',
            'board_taken.required_with'              => 'Please provide the board exam date — a rating was entered.',

            'board_rate.numeric'                     => 'Board rating must be a number.',
            'board_rate.min'                         => 'Board rating cannot be less than 0.',
            'board_rate.max'                         => 'Board rating cannot be more than 100.',
            'board_rate.required'                    => 'Please provide the board rating — a date was entered.',
            'board_rate.required_with'               => 'Please provide the board rating — a date was entered.',

            'employment_status.required'             => 'Please select your employment status.',
            'current_job_position.required_if'       => 'Job position is required.',
            'company_id.required_if'                 => 'Please select a company.',
            'date_hired.required_if'                 => 'Please enter the date you were hired.',
            'date_hired.before_or_equal'             => 'The hire date cannot be in the future.',
            'date_hired.date'                        => 'The hire date must be a valid date.',
            'employed_related_to_degree.required_if' => 'Please answer if your job is related to your degree.',
            'employment_type.required_if'            => 'Please select type of employment.',
            'organization_type.required_if'          => 'Please select type of organization.',
            'employment_area.required_if'            => 'Please select employment area.',
            'abroad_country.required_if'             => 'Please specify the country.',
            'months_to_first_job.required_if'        => 'Please select how long it took to get your first job.',
            'is_pursued_further_studies.required'    => 'Please answer the further studies question.',
            'level_of_study.required_if'             => 'Please select the level of study.',
        ];
    }

    // =========================================================
    //  STEP DEFINITIONS
    // =========================================================

    protected static function stepOne(): array
    {
        return [
            'gender'           => 'required|in:Male,Female,Other',
            'contact_number_1' => [
                'required',
                'string',
                'max:20',
                new Phone(),
                self::uniquePhoneValidator(isAlternate: false),
            ],
            'contact_number_2' => [
                'nullable',
                'string',
                'max:20',
                new Phone(),
                'different:contact_number_1',
                self::uniquePhoneValidator(isAlternate: true),
            ],
            'street_address' => 'required|string|max:500',
            'address_type'   => 'required|in:philippines,abroad',

            'regionCode'   => 'required_if:address_type,philippines|nullable|string',
            'provinceCode' => 'required_if:address_type,philippines|nullable|string',
            'cityCode'     => 'required_if:address_type,philippines|nullable|string',
            'barangayCode' => 'required_if:address_type,philippines|nullable|string',

            'intl_country' => 'required_if:address_type,abroad|nullable|string|max:255',
            'intl_state'   => 'nullable|string|max:255',
            'intl_city'    => 'required_if:address_type,abroad|nullable|string|max:255',

            'consentGiven' => 'accepted',
        ];
    }

    /**
     * @param  array{taken?:?string, rate?:string, batch_id?:int|string|null}  $ctx
     */
    protected static function stepTwo(array $ctx = []): array
    {
        $batchId   = $ctx['batch_id'] ?? null;
        $minDate   = self::resolveGraduationMinDate($batchId);
        $batchYear = $minDate ? (int) substr($minDate, 0, 4) : null;

        return [
            'civil_status' => 'required|in:single,married,widowed,separated,single-parent',
            'course_id'    => 'required|exists:courses,id',
            'batch_id'     => 'required|exists:batches,id',

            'board_taken' => [
                'nullable',
                'date',
                'before_or_equal:today',
                'required_with:board_rate',
                function ($attribute, $value, $fail) use ($minDate, $batchYear) {
                    if (! $value || ! $minDate) {
                        return;
                    }

                    if ($value < $minDate) {
                        $message = $batchYear
                            ? "Board exam date cannot be earlier than your graduation year ({$batchYear})."
                            : 'Board exam date is out of the allowed range.';

                        $fail($message);
                    }
                },
            ],
            'board_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                'required_with:board_taken',
            ],
        ];
    }

    /**
     * @param  array{taken?:?string, rate?:string, batch_id?:int|string|null}  $ctx
     */
    protected static function stepThree(array $ctx = []): array
    {
        $batchId   = $ctx['batch_id'] ?? null;
        $minDate   = self::resolveGraduationMinDate($batchId);
        $batchYear = $minDate ? (int) substr($minDate, 0, 4) : null;

        return [
            'employment_status'    => 'required|in:employed,unemployed,self-employed,other',
            'current_job_position' => 'required_if:employment_status,employed|nullable|string|max:255',
            'company_id'           => 'required_if:employment_status,employed|nullable|exists:companies,id',

            'date_hired' => [
                'required_if:employment_status,employed',
                'nullable',
                'date',
                'before_or_equal:today',
                function ($attribute, $value, $fail) use ($minDate, $batchYear) {
                    if (! $value || ! $minDate) {
                        return;
                    }

                    if ($value < $minDate) {
                        $message = $batchYear
                            ? "The hire date cannot be earlier than your graduation year ({$batchYear})."
                            : 'The hire date is out of the allowed range.';

                        $fail($message);
                    }
                },
            ],

            'employed_related_to_degree' => 'required_if:employment_status,employed|nullable|in:yes,no,partially-related',
            'employment_type'            => 'required_if:employment_status,employed|nullable|in:full-time,part-time,contractual-project-based,freelance,other',
            'organization_type'          => 'required_if:employment_status,employed|nullable|in:private-company,government-agency,non-government-organization,educational-institution,self-employed-business,other',
            'employment_area'            => 'required_if:employment_status,employed|nullable|in:philippines,abroad',
            'abroad_country'             => 'required_if:employment_area,abroad|nullable|string|max:255',
            'months_to_first_job'        => 'required_if:employment_status,employed|nullable|in:1-3-months,4-6-months,more-than-6-months,more-than-1-year,not-yet-employed',
        ];
    }

    protected static function stepFour(): array
    {
        return [
            'is_pursued_further_studies' => 'required|boolean',
            'level_of_study'             => 'required_if:is_pursued_further_studies,true|nullable|in:Certificate,Bachelor,Master,Post Doctorate',
        ];
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    /**
     * Resolve the earliest allowed date from the alumni's batch year.
     * Used as the floor for both `board_taken` and `date_hired`.
     *
     * Batch 2026 → "2026-01-01"
     * Batch 2020 → "2020-01-01"
     * No batch / non-numeric batch → BOARD_EXAM_MIN_DATE
     */
    protected static function resolveGraduationMinDate(int|string|null $batchId): string
    {
        if (! $batchId) {
            return self::BOARD_EXAM_MIN_DATE;
        }

        $batchName = Batch::whereKey($batchId)->value('batch_name');

        if (! $batchName || ! is_numeric($batchName)) {
            return self::BOARD_EXAM_MIN_DATE;
        }

        return ((int) $batchName) . '-01-01';
    }

    /**
     * Cross-column phone uniqueness (checks both contact_number_1 and _2).
     */
    protected static function uniquePhoneValidator(bool $isAlternate): Closure
    {
        return function ($attribute, $value, $fail) use ($isAlternate) {
            if (! $value) {
                return;
            }

            $exists = UserProfile::where(function ($q) use ($value) {
                $q->where('contact_number_1', $value)
                  ->orWhere('contact_number_2', $value);
            })
                ->where('user_id', '!=', Auth::id())
                ->exists();

            if ($exists) {
                $fail($isAlternate
                    ? 'This alternate number is already registered to another account.'
                    : 'This mobile number is already registered to another account.');
            }
        };
    }
}