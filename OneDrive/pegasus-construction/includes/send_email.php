<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // reCAPTCHA verification
  $recaptchaResponse = $_POST['g-recaptcha-response'];
  $secretKey = 'YOUR_RECAPTCHA_SECRET_KEY';  // Replace with your reCAPTCHA secret key
  $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
  $data = array(
    'secret' => $secretKey,
    'response' => $recaptchaResponse
  );
  $options = array(
    'http' => array(
      'header' => "Content-type: application/x-www-form-urlencoded\r\n",
      'method' => 'POST',
      'content' => http_build_query($data)
    )
  );
  $context = stream_context_create($options);
  $recaptchaResult = file_get_contents($verifyUrl, false, $context);
  $recaptchaResult = json_decode($recaptchaResult);

  if (!$recaptchaResult->success || $recaptchaResult->score < 0.5) {
    // CAPTCHA validation failed or score is below the threshold
    echo 'CAPTCHA validation failed. Please try again.';
    exit;
  }

  // CAPTCHA validation passed, continue processing the form data

  $name = $_POST['name'];
  $email = $_POST['email'];
  $message = $_POST['message'];

  // Gmail configuration
  $to = 'phillip.ransburg@gmail.com';  // Your Gmail address
  $subject = 'New Contact Form Submission';
  $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";

  // Send email
  $headers = "From: $email";
  if (mail($to, $subject, $body, $headers)) {
    echo 'Email sent successfully!';
  } else {
    echo 'Failed to send email. Please try again.';
  }
}
?>
