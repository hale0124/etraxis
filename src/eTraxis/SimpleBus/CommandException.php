<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus;

use Symfony\Component\HttpFoundation\Response;

/**
 * Exception in last command handling (after validation).
 * Contains HTTP status code and can be used in HTTP Response object.
 */
class CommandException extends \Exception
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message, $code = Response::HTTP_BAD_REQUEST, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
