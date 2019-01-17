<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
      'user.name'     => 'required|string|max:250',
      'user.email'    => 'required|email|max:250',
      'text'          => 'required|string|max:2048',
      'captcha'       => 'required|captcha',
    ];
  }

  public function messages()
  {
    return [
      'user.name'  => 'Forgot type name!',
      'user.email' => 'Field email must be valid email address.',
      'text'       => 'Field text can\'t be empty.',
      'captcha'    => 'Inserted captcha is incorrect.'
    ];
  }
}
