<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWateringLogRequest extends FormRequest
{
    public const TIMEZONE = 'Asia/Tokyo';

    public const DATETIME_FORMAT = 'Y-m-d\TH:i';

    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('plant'));
    }

    public function rules(): array
    {
        $currentTime = now(self::TIMEZONE)->format(self::DATETIME_FORMAT);

        return [
            'watered_at' => ['bail', 'required', 'date_format:'.self::DATETIME_FORMAT, 'before_or_equal:'.$currentTime],
            'amount_ml' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'memo' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'watered_at.required' => '水やり日時を入力してください。',
            'watered_at.date_format' => '水やり日時を正しく入力してください。',
            'watered_at.before_or_equal' => '水やり日時は現在以前にしてください。',
            'amount_ml.*' => '給水量は1 mL以上の整数で入力してください。',
            'memo.max' => 'メモは1,000文字以内で入力してください。',
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('plants.watering-logs.create', $this->route('plant'));
    }
}
