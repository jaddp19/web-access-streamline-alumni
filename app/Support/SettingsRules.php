<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Propaganistas\LaravelPhone\Rules\Phone;

class SettingsRules
{
    // =========================================================
    //  BASIC PROFILE (name + email only)
    // =========================================================

    public static function profile(): array
    {
        return [
            'first_name'  => self::nameRules(required: true),
            'middle_name' => self::nameRules(required: false),
            'last_name'   => self::nameRules(required: true),
            'email'       => self::emailRules(),
        ];
    }

    // =========================================================
    //  FULL PROFILE (name + email + details + avatar + address)
    // =========================================================

    public static function profileDetails(): array
    {
        return array_merge(self::profile(), [
            'avatarFile'       => 'nullable|image|max:2048',
            'gender'           => 'required|in:male,female,other',
            'contact_number_1' => ['required', 'string', 'max:20', new Phone()],
            'contact_number_2' => ['nullable', 'string', 'max:20', new Phone(), 'different:contact_number_1'],
            'batch_id'         => 'required|exists:batches,id',

            'regionCode'   => 'required|string',
            'provinceCode' => 'required|string',
            'cityCode'     => 'required|string',
            'barangayCode' => 'required|string',
            'street_address' => 'required|string|max:500',
        ]);
    }

    // =========================================================
    //  PASSWORD / PREFERENCES
    // =========================================================

    public static function password(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password'     => [
                'required',
                'string',
                'confirmed',
                'different:current_password',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }

    public static function preferences(): array
    {
        return [
            'emailNotifications' => 'required|boolean',
            'eventNotifications' => 'required|boolean',
            'profileVisible'     => 'required|boolean',
        ];
    }

    // =========================================================
    //  SHARED MESSAGES (all sections)
    // =========================================================

    public static function messages(): array
    {
        return [
            // ---- Names ----
            'first_name.required' => 'Please enter your first name.',
            'first_name.max'      => 'Your first name cannot exceed 255 characters.',
            'first_name.regex'    => 'Your first name can only contain letters, spaces, and basic punctuation (. - \' ,).',

            'middle_name.max'     => 'Your middle name cannot exceed 255 characters.',
            'middle_name.regex'   => 'Your middle name can only contain letters, spaces, and basic punctuation (. - \' ,).',

            'last_name.required'  => 'Please enter your last name.',
            'last_name.max'       => 'Your last name cannot exceed 255 characters.',
            'last_name.regex'     => 'Your last name can only contain letters, spaces, and basic punctuation (. - \' ,).',

            // ---- Email ----
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address (e.g. juan@gmail.com).',
            'email.unique'   => 'This email is already registered to another account.',
            'email.max'      => 'Your email cannot exceed 255 characters.',

            // ---- Avatar ----
            'avatarFile.image' => 'Avatar must be an image file.',
            'avatarFile.max'   => 'Avatar cannot exceed 2MB.',

            // ---- Profile details ----
            'gender.required'           => 'Please select your gender.',
            'contact_number_1.required' => 'Mobile number is required.',
            'contact_number_1.phone'    => 'Please enter a valid mobile number.',
            'contact_number_2.phone'    => 'Please enter a valid alternate number.',
            'contact_number_2.different'=> 'Alternate number must be different from your primary number.',
            'batch_id.required'         => 'Please select your batch.',
            'batch_id.exists'           => 'The selected batch is invalid.',

            'regionCode.required'     => 'Please select your region.',
            'provinceCode.required'   => 'Please select your province.',
            'cityCode.required'       => 'Please select your city/municipality.',
            'barangayCode.required'   => 'Please select your barangay.',
            'street_address.required' => 'Street address is required.',
            'street_address.max'      => 'Street address cannot exceed 500 characters.',

            // ---- Password ----
            'current_password.required' => 'Please enter your current password.',
            'new_password.required'     => 'Please enter a new password.',
            'new_password.confirmed'    => 'New password and confirmation do not match.',
            'new_password.different'    => 'Your new password must be different from your current password.',
            'new_password.min'          => 'Your new password must be at least 8 characters.',
            'new_password.letters'      => 'Your new password must contain at least one letter.',
            'new_password.mixed'        => 'Your new password must contain both uppercase and lowercase letters.',
            'new_password.numbers'      => 'Your new password must contain at least one number.',
            'new_password.symbols'      => 'Your new password must contain at least one symbol (e.g. !@#$%).',

            // ---- Preferences ----
            'emailNotifications.required' => 'Email notifications setting is required.',
            'emailNotifications.boolean'  => 'Email notifications must be on or off.',
            'eventNotifications.required' => 'Event alerts setting is required.',
            'eventNotifications.boolean'  => 'Event alerts must be on or off.',
            'profileVisible.required'     => 'Profile visibility setting is required.',
            'profileVisible.boolean'      => 'Profile visibility must be on or off.',
        ];
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected static function nameRules(bool $required): array
    {
        $base = ['string', 'max:255', 'regex:/^[\p{L}\p{M}\s\.\-\'\,]+$/u'];

        return array_merge($required ? ['required'] : ['nullable'], $base);
    }

    protected static function emailRules(): array
    {
        return [
            'required',
            'string',
            'email:rfc,dns',
            'max:255',
            'unique:users,email,' . Auth::id(),
        ];
    }
}