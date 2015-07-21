<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * The exception means that last command contains list of errors which should be converted to HTTP JsonResponse object.
 */
class CommandException extends \Exception
{
    /**
     * {@inheritDoc}
     */
    public function __construct($message, $code = Response::HTTP_BAD_REQUEST, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
