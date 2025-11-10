<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'q' => ['nullable','string','max:200'],
            'category' => ['nullable','in:batteries,solar-panels,connectors'],
            'manufacturer' => ['nullable','array'],
            'manufacturer.*' => ['string','max:100'],
            'price_min' => ['nullable','numeric','gte:0'],
            'price_max' => ['nullable','numeric','gte:price_min'],
            // category‑specific
            'capacity_min' => ['nullable','numeric'],
            'capacity_max' => ['nullable','numeric','gte:capacity_min'],
            'power_min' => ['nullable','integer'],
            'power_max' => ['nullable','integer','gte:power_min'],
            'connector_type' => ['nullable','array'],
            'connector_type.*' => ['string','max:50'],
            // paging
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:100'],
        ];
    }
}
