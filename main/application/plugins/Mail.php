<?php

class Application_Plugin_Mail extends Zend_Controller_Plugin_Abstract
{
    public static function sendMail($to, $text, $subject, $from = 'P-Fleet')
    {
        $mailObject = new Zend_Mail('utf-8');
        $mailObject->setBodyHtml($text);
        $mailObject->setFrom($from);
        $mailObject->setSubject($subject);

        if (is_string($to)) {
            $to = [$to => $to];
        }
        foreach ($to as $email => $name) {
            $mailObject->addTo($email, $name);
        }
        //        $report              = new Zend_Mime_Part( file_get_contents( $pathToReport . $reportFileName ) );
        //        $report->type        = 'application/pdf';
        //        $report->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
        //        $report->encoding    = Zend_Mime::ENCODING_BASE64;
        //        $report->filename    = $reportFileName;
        //        $mailObject->addAttachment( $report );
        $result = true;
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $mailObject->send();
                $result = true;
                break;
            } catch (Zend_Mail_Transport_Exception) {
                $result = false;
            }
        }

        return $result;
    }
}
