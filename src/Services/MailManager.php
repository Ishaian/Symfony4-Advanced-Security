<?php
/**
 * Created by PhpStorm.
 * User: Ishaian
 * Date: 14/02/2019
 * Time: 21:48
 */

namespace App\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailManager extends AbstractController
{
    public function MailSender(\Swift_Mailer $mailer,
                               $subject,
                               $sender,
                               $receiver,
                               $content)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($sender)
            ->setTo($receiver)
            ->setBody($content, 'text/html');
        $mailer->send($message);

        return true;
    }

}