<?php

require __DIR__.'/vendor/autoload.php';

use mikehaertl\pdftk\Pdf;

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
} else {
    $spamRes = true;
}

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

$postid = $_POST['mmpdffForm_hidden_postid'];

$fields = [];

$tempArray = array();


if( file_exists(plugin_dir_path(__FILE__) . '/pdf/post-pdfs/'. $postid . '-custom.pdf') && $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['mmpdffForm_hidden'] == 'mmpdffForm_hidden' && $spamRes == true) {

    foreach ($_POST as $key => $value) {

        if ($key === 'mmpdffForm_hidden' || $key === 'mmpdffForm_hidden_postid' || $key === 'g-recaptcha-response' || $key === 'h-captcha-response') {

        } else {

            $key = str_replace("_","-", strtolower($key));

            $fields = $fields + [ $key => $value];

        }


    }

    $pdfID =  rand(1,999999);

    $pdfFileUrl = plugin_dir_path(__FILE__) . 'pdf/post-pdfs/'. $postid . '-custom.pdf';

    $pdf1 = new Pdf( $pdfFileUrl);

    $result = $pdf1->fillForm($fields)
    ->needAppearances()
    ->saveAs (plugin_dir_path(__FILE__) . 'pdf/' . $pdfID .'-filled.pdf',true);

    // Always check for errors
    if ($result === false) {
        $error = $pdf1->getError();

        echo $error;
    }

    $file_url = plugin_dir_url(__FILE__) . 'pdf/' . $pdfID .'-filled.pdf';
    
    include( plugin_dir_path( __FILE__ ) . 'template/mail.php');

    header("Location: ". $file_url);

} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['mmpdffForm_hidden'] == 'mmpdffForm_hidden' && $spamRes == true) {

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // set document information
    // $pdf->SetCreator(PDF_CREATOR);
    // $pdf->SetAuthor('Mike');
    // $pdf->SetTitle('Form Submission');
    // $pdf->SetSubject('Form Submission');
    // $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

    // set default header data
    // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 014', PDF_HEADER_STRING);

    // set header and footer fonts
    // $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    // $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    // $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

    // ---------------------------------------------------------

    // IMPORTANT: disable font subsetting to allow users editing the document
    $pdf->setFontSubsetting(false);

    // set font
    $pdf->SetFont('helvetica', '', 10, '', false);

    // add a page
    $pdf->AddPage();

    /*
    It is possible to create text fields, combo boxes, check boxes and buttons.
    Fields are created at the current position and are given a name.
    This name allows to manipulate them via JavaScript in order to perform some validation for instance.
    */

    // set default form properties
    $pdf->setFormDefaultProp(array('lineWidth' => 1, 'borderStyle' => 'solid', 'fillColor' => array(255, 255, 200), 'strokeColor' => array(255, 128, 128)));

    $pdf->SetFont('helvetica', '', 12);

    $pdf->Ln(6);

    $lineNumber = 16;

    foreach ($_POST as $key => $value) {

        // Making sure our hidden values dont show up in the pdf

        if ($key === 'mmpdffForm_hidden' || $key === 'mmpdffForm_hidden_postid' || $key === 'g-recaptcha-response' || $key === 'h-captcha-response') {

        } else {
            
            $key = str_replace('_', ' ', $key);
            $key = str_replace('-', ' ', $key);

            // $pdf->Cell( 35, 5, );

            $pdf->Cell( 35, 5, htmlspecialchars($key) . ":" );
            $pdf->TextField(htmlspecialchars($key),40,5,array(), array('v'=>htmlspecialchars($value) ),85,$lineNumber);

            $lineNumber += 6;
            
            $pdf->Ln(6);
        }

        
    }

    

    // // Gender
    // $pdf->Cell(35, 5, 'Gender:');
    // $pdf->ComboBox('gender', 30, 5, array(array('', '-'), array('M', 'Male'), array('F', 'Female')));
    // $pdf->Ln(6);

    // // Drink
    // $pdf->Cell(35, 5, 'Drink:');
    // //$pdf->RadioButton('drink', 5, array('readonly' => 'true'), array(), 'Water');
    // $pdf->RadioButton('drink', 5, array(), array(), 'Water');
    // $pdf->Cell(35, 5, 'Water');
    // $pdf->Ln(6);
    // $pdf->Cell(35, 5, '');
    // $pdf->RadioButton('drink', 5, array(), array(), 'Beer', true);
    // $pdf->Cell(35, 5, 'Beer');
    // $pdf->Ln(6);
    // $pdf->Cell(35, 5, '');
    // $pdf->RadioButton('drink', 5, array(), array(), 'Wine');
    // $pdf->Cell(35, 5, 'Wine');
    // $pdf->Ln(6);
    // $pdf->Cell(35, 5, '');
    // $pdf->RadioButton('drink', 5, array(), array(), 'Milk');
    // $pdf->Cell(35, 5, 'Milk');
    // $pdf->Ln(10);

    // // Newsletter
    // $pdf->Cell(35, 5, 'Newsletter:');
    // $pdf->CheckBox('newsletter', 5, true, array(), array(), 'OK');

    // $pdf->Ln(10);
    // Address
    // $pdf->Cell(35, 5, 'Address:');
    // $pdf->TextField('address', 60, 18, array('multiline' => true, 'lineWidth' => 0, 'borderStyle' => 'none'), array('v' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'dv' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'));
    // $pdf->Ln(19);

    // Listbox
    // $pdf->Cell(35, 5, 'List:');
    // $pdf->ListBox('listbox', 60, 15, array('', 'item1', 'item2', 'item3', 'item4', 'item5', 'item6', 'item7'), array('multipleSelection' => 'true'));
    // $pdf->Ln(20);

    // E-mail
    // $pdf->Cell(35, 5, 'E-mail:');
    // $pdf->TextField('email', 50, 5);
    // $pdf->Ln(6);

    // Date of the day
    // $pdf->Cell(35, 5, 'Date:');
    // $pdf->TextField('date', 30, 5, array(), array('v' => date('Y-m-d'), 'dv' => date('Y-m-d')));
    // $pdf->Ln(10);

    // $pdf->SetX(50);

    // Button to validate and print
    // $pdf->Button('print', 30, 10, 'Print', 'Print()', array('lineWidth' => 2, 'borderStyle' => 'beveled', 'fillColor' => array(128, 196, 255), 'strokeColor' => array(64, 64, 64)));

    // Reset Button
    // $pdf->Button('reset', 30, 10, 'Reset', array('S' => 'ResetForm'), array('lineWidth' => 2, 'borderStyle' => 'beveled', 'fillColor' => array(128, 196, 255), 'strokeColor' => array(64, 64, 64)));

    // Submit Button
    // $pdf->Button('submit', 30, 10, 'Submit', array('S' => 'SubmitForm', 'F' => 'http://localhost/printvars.php', 'Flags' => array('ExportFormat')), array('lineWidth' => 2, 'borderStyle' => 'beveled', 'fillColor' => array(128, 196, 255), 'strokeColor' => array(64, 64, 64)));

//     $html = <<<EOD
// <h1 style="text-decoration:none;background-color:#CC0000;color:black;">Demonstrating pdf with php</h1>
// <p>In this simple example i show how to generate pdf documents using TCPDF</p>
// EOD;

//     $pdf->writeHTML($html);

    // ---------------------------------------------------------

    //Close and output PDF document
    $file_url = __DIR__ . '/pdf/'. rand(1,999999).'-mmpdff.pdf';
    $pdf->Output( $file_url , 'FD');
    
    include( plugin_dir_path( __FILE__ ) . 'template/mail.php');

    header("Location: /");

} else {
	
	get_header();

	echo '<h3 style="text-align:center;">There has been a error. <br/> Please contact website owner about this error code. <br/> <br/> <strong>' . strtoupper(str_replace('-', ' ', $exchangeT['error-codes'][0])) . '</strong></h3><br/> <br/>';
	echo '<a style="text-align:center;" href="'. $_SERVER['HTTP_REFERER'] .'">' . '<h4>Previous Page</h4>' .'</a><br/> <br/>';
	
	get_footer();
	
}