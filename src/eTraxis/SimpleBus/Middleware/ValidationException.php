<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Middleware;

use Symfony\Component\HttpFoundation\Response;

/**
 * Exception raised during last command validation.
 * All validation errors are available as a hash-array (keys are names of items being validated).
 * Contains HTTP status code and can be used in HTTP Response object.
 */
class ValidationException extends \Exception
{
    protected $messages = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $messages, $code = Response::HTTP_BAD_REQUEST, \Exception $previous = null)
    {
        $this->messages = $messages;

        parent::__construct(count($messages) ? reset($this->messages) : '', $code, $previous);
    }

    /**
     * Returns list of errors associated with the exception.
     *
     * @return  array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
