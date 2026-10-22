<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AuthenticatePreventivePicRequest extends ClaimOperatorReportRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'destination' => ['required', Rule::in(\App\Support\PreventiveCategory::picDestinations())],
        ]);
    }
}
