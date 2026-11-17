<?php

namespace App\Http\Requests;

class UpdatePlantRequest extends StorePlantRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('plant'));
    }

    protected function getRedirectUrl(): string
    {
        return route('plants.edit', $this->route('plant'));
    }
}
