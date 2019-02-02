<?php

namespace App\Service;

use Swift_Mailer;
use Symfony\Component\Templating\EngineInterface;

class EmailManager
{
    private $twig;

    /**
     * EmailManager constructor.
     * @param $twig
     */
    public function __construct(EngineInterface $twig)
    {
        $this->twig = $twig;
    }


    /**
     * Envoie des emails aux organisateurs et invités aux rénions
     * en cas d'invitation, de modification ou d'annulation.
     * @param Swift_Mailer $mailer
     * @param $object
     * @param $to
     * @param $view
     * @param $options
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
