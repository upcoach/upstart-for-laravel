<?php

namespace Upcoach\UpstartForLaravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event' => ['required', 'string'],
            'event_timestamp' => ['required', 'integer'],
            'data' => ['required'],
            'Upcoach-Signature' => ['required', 'starts_with:t='],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'Upcoach-Signature' => $this->header('Upcoach-Signature'),
        ]);
    }
}
