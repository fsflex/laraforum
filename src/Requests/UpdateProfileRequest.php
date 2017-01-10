<?php

namespace FsFlex\LaraForum\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'country'=>'required|exists:countries,id',
            'is_available_for_hire'=>'nullable',
            'website'=>'nullable|url',
            'twitter_username'=>'nullable|regex:/^(?=.{8,32}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/i',
            'github_username'=>'nullable|regex:/^(?=.{8,32}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/i',
            'place_of_employment'=>'nullable|max:255',
            'job_title'=>'nullable|max:255',
            'hometown'=>'nullable|max:255',
            'avatar'=>'nullable|url',
        ];
    }

}
