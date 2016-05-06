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

/**
 * Mailer interface.
 */
interface MailerInterface
{
    /**
     * Sends an email to specified recipient.
     *
     * @param   string $address  Recipient address.
     * @param   string $name     Recipient name.
     * @param   string $subject  Email subject.
     * @param   string $template Path to Twig template of the email body.
     * @param   array  $args     Twig template parameters.
     *
     * @return  bool Whether the email was accepted for delivery.
     */
    public function send(string $address, string $name, string $subject, string $template, array $args = []): bool;
}
