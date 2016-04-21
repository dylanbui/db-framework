<?php

namespace App\Controller\Email;
use App\Lib\Core\BaseController;
use App\Lib\Email;

class IndexController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
        $this->oView->menuGroup = 'email';
	}

    public function sentEmailAction()
    {
        // Cho phep truy cap KCFINDER
        // Tranh truong hop truy cap thong wa duong link cua iframe
        $_SESSION['KCFINDER'] = array();
        $_SESSION['KCFINDER']['disabled'] = false; // Activate the uploader,

        $this->oView->returnSentMail = null;
        if ($this->oInput->isPost()) {
            $mail = new \PHPMailer(); // create a new object
            $mail->IsSMTP(); // enable SMTP
//            $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465; // or 587

            // Dia chi email chung thuc phai duoc cho phep truy cap tu cac ung dung khac
            // https://support.google.com/accounts/answer/6010255
            $mail->Username = "dylanmobiledev@gmail.com";
//            $mail->Password = '!qa2ws3ed';

            // Khi tao 1 pw voi chung thuc 2-Step Verification, khong can phai chung thuc cho phep truy cap tu cac ung dung khac
            // https://myaccount.google.com/security
            $mail->Password = 'akpoomydoyopmaug';

            // Su dung google no se tu dong lay dia chi smtp
            // Muon thay doi Sent From thi phai Settings -> Accounts -> Send mail as -> Add another email address you own
            $mail->SetFrom("buivantienduc@gmail.com");

            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $this->oInput->post('subject');
            $mail->Body = $this->oInput->post('email_content');
            $mail->AddAddress($this->oInput->post('email'));

            if(!$mail->Send()) {
                $this->oView->returnSentMail = '<td style="font-weight: bold;color: red; ">' . $mail->ErrorInfo;
            } else {
                $this->oView->returnSentMail = '<td style="font-weight: bold;color: green; ">Message has been sent';
            }
        }

        $this->oView->title = 'PHPMailer Service';
        $this->oView->sub_title = 'Use PHPMailer Class';
        $this->renderView('email/index/sent_email');
    }

    public function sentEmailSmtpAction()
    {
        // Cho phep truy cap KCFINDER
        // Tranh truong hop truy cap thong wa duong link cua iframe
        $_SESSION['KCFINDER'] = array();
        $_SESSION['KCFINDER']['disabled'] = false; // Activate the uploader,

        $this->oView->returnSentMail = null;
        if ($this->oInput->isPost()) {

            $mail = new Email();
            $mail->to = $this->oInput->post('email');
            $mail->subject = $this->oInput->post('subject');
            $mail->body = $this->oInput->post('email_content');

            $returnVal = $mail->sendWithSmtpConfig($this->oConfig->config_values['mail']);

            if($returnVal == true) {
                $this->oView->returnSentMail = '<td style="font-weight: bold;color: green; ">Message has been sent';
            } else {
                $this->oView->returnSentMail = '<td style="font-weight: bold;color: red; ">' . $returnVal;
            }
        }

        $this->oView->title = 'SMTP Service';
        $this->oView->sub_title = 'Use Email Class';
        $this->renderView('email/index/sent_email');
    }

	
	
}
