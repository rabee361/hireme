<p>Hello,</p>

<p>Your verification code is: <strong>{{ $otp->code }}</strong></p>

<p>This code will expire at {{ $otp->expires_at?->toDateTimeString() }}.</p>

<p>If you did not request this code, you can ignore this email.</p>