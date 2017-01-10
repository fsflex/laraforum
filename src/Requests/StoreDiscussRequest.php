<?php

namespace FsFlex\LaraForum\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDiscussRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //any user can store new discuss
        if(!Auth::check())
            return false;
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
            'title'=>'required|max:255',
            'channel'=>'exists:channels,id',
            'body'=>'required|max:60000'
        ];
    }
    public function messages()
    {
        return [
            'title.required'=>'A title is required',
            'title.unique'=>'This discuss is exist you can find it in search bar',
            'title.max'=>'It\'s title, you can write more in body',
            'channel.exists'=>'Channel is required',
            'body.required'=>'A question is required',
            'body.max'=>"Please don't store a book in there!",
        ];
    }
}
