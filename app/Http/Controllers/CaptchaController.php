<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mews\Captcha\Captcha;

class CaptchaController extends \Mews\Captcha\CaptchaController
{
  protected $redirectTo = '/home';

  public function refreshCaptcha()
  {
    return response()->json(['captcha' => captcha_img('custom')]);
  }

}
