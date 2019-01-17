<div class="captcha-container">
  <div class="form-group row" style="margin-left:0;margin-right:0;">
    <label for="captcha" class="col-md-12 col-form-label pl-0 required">Captcha</label>
    <input type="text" id="captcha"
           class="col-md-4 required form-control{{ $errors->has('captcha') ? ' is-invalid' : '' }}"
           name="captcha" data-field placeholder="Enter Captcha" required>
    <div class="col-md-6">
      <span class="col-md-6 captcha-img">{!! captcha_img('custom') !!}</span>
    </div>
    <button type="button" class="btn btn-outline-secondary btn-refresh col-md-2" onclick="captchaRefresh()">Refresh</button>
    {{--@if ($errors->has('captcha'))--}}
      <div class="error-container col-md-12" style="display: none;">
        <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('captcha') }}</strong></span>
      </div>
    {{--@endif--}}
  </div>
</div>