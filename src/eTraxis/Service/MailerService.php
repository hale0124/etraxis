<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Twig_Environment;

/**
 * Shortcut service for standard mailer.
 */
class MailerService implements MailerInterface
{
    protected $logger;
    protected $twig;
    protected $mailer;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface  $logger Debug logger.
     * @param   Twig_Environment $twig   Templates renderer.
     * @param   Swift_Mailer     $mailer Mailer service.
     */
    public function __construct(LoggerInterface $logger, Twig_Environment $twig, Swift_Mailer $mailer)
    {
        $this->logger = $logger;
        $this->twig   = $twig;
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function send($from_address, $from_name, $recipients, $subject, $template, $args = [])
    {
        $this->logger->info('Send email', [$from_address, $from_name, $subject]);
        $this->logger->info('Recipients', is_array($recipients) ? $recipients : [$recipients]);

        $body = $this->twig->render($template, $args);

        $message = \Swift_Message::newInstance($subject, $body, 'text/html')
            ->setFrom($from_address, $from_name)
            ->setTo($recipients)
        ;

        return $this->mailer->send($message);
    }
}
