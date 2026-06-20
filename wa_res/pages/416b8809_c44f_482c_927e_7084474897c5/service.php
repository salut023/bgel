<?php

ob_start();

header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

function wa_json_exit($success, $message = "")
{
    ob_end_clean();
    echo json_encode(["success" => $success, "message" => $message]);
    exit();
}

if (version_compare(PHP_VERSION, '8.1.0') < 0) {
    wa_json_exit(false, "Your version must be at least 8.1 (Current is PHP ".PHP_VERSION.")");
}


$path_php=__DIR__.'/../../static/wa/php/';

include_once($path_php.'/waform.php');


use PHPMailer\PHPMailer\PHPMailer;

$form_uuid = $_POST['form_uuid'] ?? '';
$wa_lang = $_POST['wa_lang'] ?? '';

if (strlen($form_uuid) === 0)
{
    wa_json_exit(false, "Missing form_uuid");
}



$forms = [[
'conf'=>[
'bcc'=>[]
,'cc'=>[]
,'extend_to'=>[]
,'from'=>'bureau@bgelectricite.fr','main_to'=>'contact@bgelectricite.fr','subject'=>'Demande client'
],'inputs'=>[[
'label'=>'','name'=>'field_f8b8e390bd35413198dfd850e04016bf','type_input_text'=>0,'use_email_to_reply'=>0
],[
'label'=>'','name'=>'field_a23755c9fcd64bf4a9bedcc09fac699d','type_input_text'=>0,'use_email_to_reply'=>0
],[
'label'=>'','name'=>'field_f23227ad90564f8b93c23f72e3c080d8','type_input_text'=>1,'use_email_to_reply'=>0
],[
'label'=>'','name'=>'field_b2a9bfec4f3543d9a4c06d86ec011bc1'
]]
,'uuid'=>'cc6e6a00a1fd4f4e94dd9fc61b6bae75'
]];

    $message = new WaMailFormatter($forms,$form_uuid);

    if ($message->isValid() == false)
    {
        wa_json_exit(false, "Form not found");
    }

    $cfg=[
        "config"=>$message->config(),
        "text"=>$message->text(),
        "attachments"=>$message->attachments(),
        "force_email_reply"=>$message->forceEmailToReply(),
        "force_subject"=>$message->forceEmailSubject(),
    ];
    $mail = new WaMailWrapper($cfg );
    $b_success = true;
    $error_string="";
    if ($mail ->send()==false)
    {
        $b_success = false;
        $error_string=$mail->errorString();
    }

wa_json_exit($b_success, $error_string);


