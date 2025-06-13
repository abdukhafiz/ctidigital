<?php

namespace App\Http\Requests\Api\Hotel;

use Illuminate\Foundation\Http\FormRequest;

class BookingHotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'arrivalDate' => ['required', 'date', 'before:departureDate'],
            'departureDate' => ['required', 'date', 'after:arrivalDate'],
            'hotelCode' => ['required', 'string'],
            'promoCode' => ['nullable', 'string'],
            'rooms' => ['required', 'array'],
            'rooms.*.adults' => ['required', 'integer', 'min:1'],
            'rooms.*.children' => ['required', 'integer', 'min:0'],
            'rooms.*.roomCode' => ['required', 'string'],
            'rooms.*.rateCode' => ['required', 'string'],
            'rooms.*.totalPrice' => ['required', 'numeric', 'min:0'],
        ];
    }
}
