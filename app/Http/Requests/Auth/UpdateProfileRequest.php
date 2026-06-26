<?php

namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

class UpdateProfilRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
         // $userId = $this->user()->id;

        return [
            'name' => ['sometimes','required', 'string', 'max:255'],
            'email' => ['sometimes','required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id)],
            'password' => ['sometimes','nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ];
    }

    public function messages(): array
    {
        
        return [
            'name.required' => 'Le nom est requis.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email.required' => "L'adresse e-mail est requise.",
            'email.string' => "L'adresse e-mail doit être une chaîne de caractères.",
            'email.email' => "L'adresse e-mail doit être une adresse e-mail valide.",
            'email.max' => "L'adresse e-mail ne doit pas dépasser 255 caractères.",
            'email.unique' => "Cette adresse e-mail est déjà utilisée.",
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.mixedCase' => 'Le mot de passe doit contenir au moins une lettre majuscule et une lettre minuscule.',
            'password.numbers' => 'Le mot de passe doit contenir au moins un chiffre.',
            'password.symbols' => 'Le mot de passe doit contenir au moins un symbole.',
        ];
    }

    public function withValidator(ValidatorContract $validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('password') && !Hash::check($this->input('current_password'), $this->user()->password)) {
                $validator->errors()->add('current_password', 'Le mot de passe actuel est incorrect.');
            }
        });
    }
}