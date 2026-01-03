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
            'generalData' => ['required', 'array'],
            'generalData.client' => ['required', 'string', 'max:255'],
            'generalData.phone' => ['required', 'string', 'max:20'],
            'generalData.vat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'generalData.discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'generalData.deliveryMethod' => ['required', 'string'],
            'generalData.taxNumber' => ['required', 'string', 'max:32'],
            'notes' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'generalData.required' => 'Brak danych ogólnych zamówienia.',
            'generalData.array' => 'Nieprawidłowy format danych ogólnych zamówienia.',

            'generalData.client.required' => 'Pole „Klient” jest wymagane.',
            'generalData.client.string' => 'Pole „Klient” musi być tekstem.',
            'generalData.client.max' => 'Pole „Klient” nie może przekraczać 255 znaków.',

            'generalData.phone.required' => 'Pole „Telefon” jest wymagane.',
            'generalData.phone.string' => 'Pole „Telefon” musi być tekstem.',
            'generalData.phone.max' => 'Pole „Telefon” nie może przekraczać 20 znaków.',

            'generalData.vat.numeric' => 'Pole „VAT” musi być liczbą.',
            'generalData.vat.min' => 'Pole „VAT” nie może być mniejsze niż 0%.',
            'generalData.vat.max' => 'Pole „VAT” nie może być większe niż 100%.',

            'generalData.discount.numeric' => 'Pole „Rabat” musi być liczbą.',
            'generalData.discount.min' => 'Pole „Rabat” nie może być mniejszy niż 0%.',
            'generalData.discount.max' => 'Pole „Rabat” nie może być większy niż 100%.',

            'generalData.deliveryMethod.required' => 'Wybierz sposób dostawy.',
            'generalData.deliveryMethod.string' => 'Nieprawidłowa wartość pola „Sposób dostawy”.',
            'generalData.deliveryMethod.in' => 'Nieobsługiwany sposób dostawy. Dozwolone: Eurohermes, RohligSuus.',

            'generalData.taxNumber.required' => 'Pole „NIP” jest wymagane.',
            'generalData.taxNumber.string' => 'Pole „NIP” musi być tekstem.',
            'generalData.taxNumber.max' => 'Pole „NIP” nie może przekraczać 32 znaków.',

            'notes.string' => 'Pole „Uwagi” musi być tekstem.',

            'file.required' => 'Dodaj plik z zamówieniem.',
            'file.file' => 'Nieprawidłowy plik.',
            'file.mimes' => 'Nieprawidłowy format pliku. Dozwolone formaty: CSV, TXT.',
            'file.max' => 'Plik jest zbyt duży. Maksymalny rozmiar to 10 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $generalData = $this->input('generalData');

        if (is_string($generalData)) {
            $decoded = json_decode($generalData, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge(['generalData' => $decoded]);
            } else {
                $this->merge(['generalData' => null]);
            }
        }
    }
}
