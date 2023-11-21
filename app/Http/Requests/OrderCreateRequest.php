<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
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
        //TODO к проверке даты и вермени еще добавить часы работы магазина
        return [
            'cart' => "required|array",
            'cart.*.product_id' => 'required|integer',
            'cart.*.count' => 'required|integer|min:1',
            'cart.*.flower_count' => 'integer',
            'form_data' => "required|array",
            'form_data.name' => 'nullable|string|min:2|max:50',
            'form_data.email' => 'nullable|email|unique:users,email',
            'form_data.is_anonymous_sender' => 'boolean',
            'form_data.is_recipient_current_user' => 'required|boolean',
            'form_data.previously_call_to_recipient' => 'required_if:form_data.is_recipient_current_user,==,false|boolean',
            'form_data.recipient_name' => 'nullable|string|max:50',
            'form_data.recipient_phone' => 'nullable|regex:/^79\d{9}$/',
            'form_data.delivery_address' => 'required|string',
            'form_data.delivery_address_latitude' => 'required',
            'form_data.delivery_address_longitude' => 'required',
            'form_data.entrance' => 'nullable|string|max:20',
            'form_data.floor' => 'nullable|string|max:20',
            'form_data.apartment_number' => 'nullable|string|max:20',
            'form_data.comment_for_courier' => 'nullable|string|max:255',
            'form_data.delivery_date_time' => 'nullable|date_format:Y-m-d H:i|after:'. now()->addMinutes(89)->toDateTimeString(),
            'form_data.client_wishes' => 'nullable|string|max:255'
        ];
    }

}
