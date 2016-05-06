<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service\Mailer;

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
    protected $sender_address;
    protected $sender_name;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface  $logger         Debug logger.
     * @param   Twig_Environment $twig           Templates renderer.
     * @param   Swift_Mailer     $mailer         Mailer service.
     * @param   string           $sender_address Sender address.
     * @param   string           $sender_name    Sender name.
     */
    public function __construct(
        LoggerInterface  $logger,
        Twig_Environment $twig,
        Swift_Mailer     $mailer,
        string           $sender_address,
        string           $sender_name)
    {
        $this->logger         = $logger;
        $this->twig           = $twig;
        $this->mailer         = $mailer;
        $this->sender_address = $sender_address;
        $this->sender_name    = $sender_name;
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $address, string $name, string $subject, string $template, array $args = []): bool
    {
        $this->logger->info('Send email', [$address, $name, $subject]);

        $body = $this->twig->render($template, $args);

        $message = \Swift_Message::newInstance($subject, $body, 'text/html')
            ->setFrom([$this->sender_address => $this->sender_name])
            ->setTo($address, $name)
        ;

        return $this->mailer->send($message) !== 0;
    }
}
