<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'manufacturer' => $this->manufacturer,
            'price' => (float) $this->price,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', fn() => [
                'slug' => $this->category->slug,
                'name' => $this->category->name,
            ]),
            'specs' => [
                'capacity' => optional($this->batterySpec)->capacity,
                'power_output' => optional($this->solarPanelSpec)->power_output,
                'connector_type' => optional($this->connectorSpec)->connector_type,
            ]
        ];
    }
}
