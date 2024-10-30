<?php


// get domain name root
$host = $_SERVER['HTTP_HOST'];
preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
$domain = "{$matches[0]}";


//setup email settings
$to = get_post_meta( $_POST['mmpdffForm_hidden_postid'] , '_mm_pdf_form_to', true);
$from = 'wordpress@' . $domain;
$name = get_bloginfo('name');
$attachment = $file_url;
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: multipart/mixed; charset=iso-8859-1' . "\r\n";
$headers = array('Content-Type: text/html; charset=UTF-8');
$headers .= 'From: ' . $name . ' <' . $from . '>' . "\r\n";
$subject = 'New Email';

$msg = '';

$data = [];

foreach ($_POST as $key => $value) {

    //get input values

    if ($key === 'mmpdffForm_hidden' || $key === 'mmpdffForm_hidden_postid' || $key === 'g-recaptcha-response' || $key === 'h-captcha-response') {
    } else {
		
		$key = str_replace('_', ' ', $key);

        $msg .= htmlspecialchars($key) . ":<br/><br/> " . htmlspecialchars($value) . "<br/><br/>";

        $temp = [htmlspecialchars($key) => htmlspecialchars($value)];

        array_push($data, $temp);
    }
}

$msg = sanitize_text_field($msg);

$mail_attachment = array($attachment);

$spamRes;

if(get_option('pdff-captcha-select') === 'hCaptcha'){

    $SECRET_KEY = get_option('pdff-secret-key');    
    $VERIFY_URL = "https://hcaptcha.com/siteverify";

    $token = $_POST['h-captcha-response'];
            
    $myArr = array( 'secret' => $SECRET_KEY, 'response' => $token );

    $response = httpPost($VERIFY_URL, $myArr);

    $exchangeT = json_decode($response, true);

    if($exchangeT['success'] == 1){
        $spamRes = true;
    }else{
        $spamRes = false;
    }
} else if(get_option('pdff-captcha-select') === 'reCAPTCHA'){

    $SECRET_KEY = get_option('pdff-secret-key');    
    $VERIFY_URL = "https://www.google.com/recaptcha/api/siteverify";

    $token = $_POST['g-recaptcha-response'];
            
    $myArr = array( 'secret' => $SECRET_KEY, 'response' => $token );

    $response = httpPost($VERIFY_URL, $myArr);

    $exchangeT = json_decode($response, true);

    if($exchangeT['success'] == 1){
        $spamRes = true;
    }else{
        $spamRes = false;
    }
}

if($spamRes == true){

    // send via wordpress wp_mail function

    wp_mail($to, $subject, $msg, $headers, $mail_attachment);

}
