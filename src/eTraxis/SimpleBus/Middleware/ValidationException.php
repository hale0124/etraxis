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

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Exception raised during last command validation.
 * All validation errors are available as a hash-array (keys are names of items being validated).
 * Contains HTTP status code and can be used in HTTP Response object.
 */
class ValidationException extends BadRequestHttpException
{
    protected $messages = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $messages, $code = 0, \Exception $previous = null)
    {
        $this->messages = $messages;

        parent::__construct(count($messages) ? reset($this->messages) : '', $previous, $code);
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
