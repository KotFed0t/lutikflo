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
        //TODO translate error messages (зашифровать ошибку по широте и долготе)
        //TODO к проверке даты и вермени еще добавить часы работы магазина
        return [
            'cart' => "required|array",
            'cart.*.product_id' => 'required|integer',
            'cart.*.count' => 'required|integer|min:1',
            'cart.*.flower_count' => 'integer',
            'form_data' => "required|array",
            'form_data.name' => 'string|min:2|max:50',
            'form_data.email' => 'required|email',
            'form_data.is_anonymous_sender' => 'boolean',
            'form_data.is_recipient_current_user' => 'required|boolean',
            'form_data.previously_call_to_recipient' => 'required_if:form_data.is_recipient_current_user,==,false|boolean',
            'form_data.recipient_name' => 'required_if:form_data.is_recipient_current_user,==,false|string|min:2|max:50',
            'form_data.recipient_phone' => 'required_if:form_data.is_recipient_current_user,==,false|regex:/^79\d{9}$/',
            'form_data.delivery_address' => 'required|string',
            'form_data.delivery_address_latitude' => 'required',
            'form_data.delivery_address_longitude' => 'required',
            'form_data.entrance' => 'string|max:20',
            'form_data.floor' => 'string|max:20',
            'form_data.apartment_number' => 'string|max:20',
            'form_data.comment_for_courier' => 'string|max:255',
            'form_data.delivery_date_time' => 'required|date_format:Y-m-d H:i:s|after:2 hours',
            'form_data.client_wishes' => 'string|max:255'
        ];
    }

}
