<?php

namespace App\Service;

use Swift_Mailer;
use Symfony\Bundle\TwigBundle\TwigEngine;


class EmailManager
{
    private $twig;

    /**
     * EmailManager constructor.
     * @param $twig
     */
    public function __construct(TwigEngine $twig)
    {
        $this->twig = $twig;
    }


    /**
     * @param Swift_Mailer $mailer
     * @param $object
     * @param $to
     * @param $view
     * @param $options
     * @throws \Twig\Error\Error
     */
    public function sendEmail(Swift_Mailer $mailer, $object, $to, $view, $options)
    {
        $message = (new \Swift_Message($object))
            ->setFrom('margouillat.reunion.it@gmail.com')
            ->setTo($to)
            ->setBody($this->twig->render($view, $options), 'text/html');
        $mailer->send($message);
    }
}
