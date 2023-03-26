<?php

namespace Upcoach\UpstartForLaravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'app' => ['required'],
            'organization' => ['required'],
            'token' => ['required'],
            'timestamp' => ['required', 'integer'],
            'Upcoach-Signature' => ['required', 'starts_with:t='],
            'Upcoach-Refresh-Token' => ['boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'Upcoach-Signature' => $this->header('Upcoach-Signature'),
            'Upcoach-Refresh-Token' => $this->header('Upcoach-Refresh-Token', 'false'),
        ]);
    }
}
