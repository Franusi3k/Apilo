<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOrderRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'generalData' => 'required|string',

            'notes' => 'nullable|string',

            'file' => 'required|file|mimes:csv,txt,xlsx|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'generalData.required' => 'Brak danych ogólnych zamówienia.',
            'file.required' => 'Brak załączonego pliku.',
            'file.mimes' => 'Nieprawidłowy format pliku. Dozwolone: CSV, TXT, XLSX.',
            'file.max' => 'Plik jest zbyt duży (max 10MB).',
        ];
    }
}
