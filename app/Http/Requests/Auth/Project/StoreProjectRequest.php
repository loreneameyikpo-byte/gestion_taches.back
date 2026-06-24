<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
     /**
     * L'autorisation fine (propriétaire ou non) est gérée par ProjectPolicy
     * via $this->authorize() dans le contrôleur ; ici on vérifie seulement
     * que l'utilisateur est authentifié, ce que fait déjà le middleware
     * "auth:sanctum" de la route.
     */ 
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
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du projet est requis.',
            'name.string' => 'Le nom du projet doit être une chaîne de caractères.',
            'name.max' => 'Le nom du projet ne peut pas dépasser 255 caractères.',
            'description.string' => 'La description du projet doit être une chaîne de caractères.',
            'description.max' => 'La description du projet ne peut pas dépasser 1000 caractères.',
            'due_date.date' => 'La date d\'échéance doit être une date valide.',
            'due_date.after_or_equal' => 'La date d\'échéance doit être aujourd\'hui ou dans le futur.',
        ];
    }
}