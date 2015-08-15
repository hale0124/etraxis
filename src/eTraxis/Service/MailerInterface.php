<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

/**
 * Mailer interface.
 */
interface MailerInterface
{
    /**
     * Sends email as specified.
     *
     * If multiple recipients need to receive the message an array should be used.
     * Example: array('receiver@domain.org', 'other@domain.org' => 'A name')
     *
     * @param   string       $from_address Sender address.
     * @param   string       $from_name    Sender name.
     * @param   string|array $recipients   Recipient address(es).
     * @param   string       $subject      Email subject.
     * @param   string       $template     Path to Twig template of the email body.
     * @param   array        $args         Twig template parameters.
     *
     * @return  int The number of recipients who were accepted for delivery.
     */
    public function send($from_address, $from_name, $recipients, $subject, $template, $args = []);
}
