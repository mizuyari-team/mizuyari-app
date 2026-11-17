<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlantRequest extends FormRequest
{
    protected $redirectRoute = 'plants.create';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'species' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'pot_size' => ['nullable', 'in:小,中,大'],
            'daily_base_ml' => ['required', 'integer', 'min:1', 'max:10000'],
            'default_interval_days' => ['sometimes', 'required', 'integer', 'min:1', 'max:365'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '植物名を入力してください。',
            'name.max' => '植物名は255文字以内で入力してください。',
            'species.max' => '種類は255文字以内で入力してください。',
            'location.max' => '置き場所は255文字以内で入力してください。',
            'pot_size.in' => '鉢サイズは小・中・大から選んでください。',
            'daily_base_ml.*' => '基準給水量は1〜10,000 mLの整数で入力してください。',
            'photo.image' => 'JPEGまたはPNGの写真を選んでください。',
            'photo.mimes' => 'JPEGまたはPNGの写真を選んでください。',
            'photo.max' => '写真は5MB以内にしてください。',
            'photo.uploaded' => '写真をアップロードできませんでした。ファイルサイズを確認してください。',
        ];
    }
}
