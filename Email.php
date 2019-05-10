
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends CI_Controller {

    public function index()
    {
        $to = 'vishnumagictechnolabs@gmail.com';
        $subject = 'This is test ';
        $body = 'best body';
        $this->sendMail($to, $subject, $body);
    }

    public function sendMail($to, $subject, $body)
    {

        $this->load->library('email');
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'bimabazaar2018@gmail.com',
            'smtp_pass' => '@Bima2018',
            'mailtype' => 'html',
            'charset' => 'iso-8859-1'
        );
        $this->email->initialize($config);
        $this->email->set_mailtype("html");
        $this->email->set_newline("\r\n");
        $this->email->from('jashphp@gmail.com', 'BimaBazaar');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($body);
        $this->email->send();
        return TRUE;
    }
}
