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
 * Exception which contains HTTP status code and should be converted to HTTP Response object.
 */
class ResponseException extends \Exception
{
    public function __construct($message, $code = Response::HTTP_INTERNAL_SERVER_ERROR, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
