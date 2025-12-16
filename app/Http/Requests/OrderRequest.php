<?php

namespace App\Http\Requests;

use App\Enums\SideEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class OrderRequest extends FormRequest
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
            'symbol' => 'required|in:BTC,ETH',
            'side'   => ['required',Rule::enum(SideEnum::class)],
            'price'  => 'required|numeric|min:0.01',
            'amount' => 'required|numeric|min:0.00000001',
        ];
    }

    public function messages()
    {
        return [
            'side.' . Enum::class => 'The side must be either buy or sell.',
        ];
    }
}
