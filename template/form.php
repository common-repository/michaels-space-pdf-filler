<?php

$slug = get_post_field('post_name', get_post());

$html = '';

if(get_option('pdff-captcha-select') == 'hCaptcha'){

    $html .= '<script src="https://hcaptcha.com/1/api.js" async defer></script>';

} else if(get_option('pdff-captcha-select') == 'reCAPTCHA'){

    $html .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
}

$html .= '<form method="post" action="' . get_site_url() . '/index.php?mmpdff_api=1" class="mmpdffForm mmpdffForm_' . $id . '">';

$html .= '<input type="hidden" name="mmpdffForm_hidden" value="mmpdffForm_hidden">';

$html .= '<input type="hidden" name="mmpdffForm_hidden_postid" value="'. $id .'">';


$input_number = get_post_meta( $id, '_mm_pdf_form', true);

        $form_data = get_post_meta( $id, 'mm_pdf_form_data', true);

        $form_data = json_decode($form_data, true);

        if($form_data != '' || $form_data != null){



        for ($i = 0; $i < count($form_data); $i++) {

            // print_r( $form_data[$i]);


            if($form_data[$i]['type'] == "date"){


                if(isset($form_data[$i]['label'])){

                    $html .= '<label';

                    $html .= ' for="' . $form_data[$i]['name'].'">';

                    $html .= $form_data[$i]['label'].'</label>';   
                }

                $html .= '<input class="mmpdffForm_mmpdffFormLabel mmpdffForm_mmpdffFormLabel_'. $i . '" type="date" id="' . $form_data[$i]['name'].'" name="' . $form_data[$i]['name'].'">';

            }


            if($form_data[$i]['type'] == "paragraph"){

                $html .= '<'. $form_data[$i]['subtype'] . '>';

                if(isset($form_data[$i]['className'])){
                    $html .=  ' class="mmpdffForm_mmpdffFormLabel mmpdffForm_mmpdffFormLabel_'. $i. ' '.  $form_data[$i]['className'] .'">';
                }

                $html .= $form_data[$i]['label'] .'</'. $form_data[$i]['subtype'] .'>';

            }

            if($form_data[$i]['type'] == "header"){

                $html .= '<'. $form_data[$i]['subtype'] .'>';

                if(isset($form_data[$i]['className'])){
                    $html .=  ' class="mmpdffForm_mmpdffFormLabel mmpdffForm_mmpdffFormLabel_'. $i . ' ' . $form_data[$i]['className'] .'">';
                }

                $html .= $form_data[$i]['label'] .'</'. $form_data[$i]['subtype'] .'>';
            }

            if($form_data[$i]['type'] == "text"){

                if(isset($form_data[$i]['label']) ){
                    $html .=  '<label>' . $form_data[$i]['label'];
                }

                $html .=  '<input ';

                if( isset($form_data[$i]['value'])){
                    $html .=  'value="'.  $form_data[$i]['value'] .'"';
                }

                if(isset($form_data[$i]['name'])){
                    $html .=  'name="'.  $form_data[$i]['name'] .'"';
                }

                if(isset($form_data[$i]['className'])){
                    $html .=  'class="mmpdffForm_mmpdffFormLabel mmpdffForm_mmpdffFormLabel_'. $i . ' '.  $form_data[$i]['className'] .'"';
                }else{

                    $html .=  'class="mmpdffForm_mmpdffFormLabel mmpdffForm_mmpdffFormLabel_'. $i . '"';
                
                }

                if(isset($form_data[$i]['placeholder'])){
                    $html .=  'placeholder="'.  $form_data[$i]['placeholder'] .'"';
                }

                if(isset($form_data[$i]['maxlength'])){
                    $html .=  'maxlength="'.  $form_data[$i]['maxlength'] .'"';
                }
                
                $html .=  'type="text"/>';

                if(isset($form_data[$i]['label']) ){
                    $html .=   '</label>';
                }

                if(isset($form_data[$i]['description'])){
                    $html .=  '<br><span>'. $form_data[$i]['description'] .'</span>';
                }


               
            }

            if($form_data[$i]['type'] == "textarea"){

                if(isset($form_data[$i]['label']) ){
                    $html .=  '<label>' . $form_data[$i]['label'];
                }

                $html .= '<textarea id="w3review" name="'.  $form_data[$i]['name'] .'" rows="4" cols="50"';

                if(isset($form_data[$i]['className'])){
                    $html .=  'class="mmpdffForm_mmpdffFormLabel mmpdffForm_mmpdffFormLabel_'. $i . ' '.  $form_data[$i]['className'] .'">';
                }else{

                    $html .=  'class="mmpdffForm_mmpdffFormLabel mmpdffForm_mmpdffFormLabel_'. $i . '">';
                
                }
                $html .= '</textarea>';

                if(isset($form_data[$i]['label']) ){
                    $html .=  '</label>';
                }

            }

            
            $html .=  '<br>';

        }
    }     
        

for ($i = 0; $i < $input_number; $i++) {

    $field_name = get_post_meta($id, '_mm_pdf_form_' . $i, true);

    $html .=  '<label class="mmpdffForm_mmpdffFormLabel mmpdffForm_mmpdffFormLabel_' . $i . '">' . $field_name . ':';
    $html .= '<input class="mmpdffForm_mmpdffFormInput mmpdffForm_mmpdffFormInput_' . $i . '" type="text" name="' . $field_name . '"> </label>';
}

// $html .= '</br>';

if(get_option('pdff-captcha-select') == 'hCaptcha'){
    $html .= '<div class="h-captcha" data-sitekey="'. get_option('pdff-site-key') .'"></div>';
}else if(get_option('pdff-captcha-select') == 'reCAPTCHA'){
    $html .= '<div class="g-recaptcha" data-sitekey="'. get_option('pdff-site-key') .'"></div>';
}

// $html .= '</br>';

$html .= '<input class="mmpdffForm_mmpdffFormSubmit mmpdffForm_mmpdffFormSubmit_' . $id . '"type="submit" value="Submit">';

$html .= '</form>';

function httpPost($url, $data)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['mmpdffForm_hidden'] == 'mmpdffForm_hidden') {


    try {
			
        echo '<h3 class="mmpdffForm_ppdffFormSuccess" style="text-align:center;">Email sent</h3>';

        include( plugin_dir_path( __FILE__ ) . 'mail.php');

    } catch (Exception $e) {
        echo '<h3 class="mmpdffForm_mmpdffFormError">' .  $e->getMessage() . "</h3>";
    }
}