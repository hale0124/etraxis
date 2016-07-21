<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Middleware;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Exception raised during last message validation.
 *
 * All validation errors are available as a hash-array (keys are names of items being validated).
 * Contains HTTP status code and can be used in HTTP Response object.
 */
class ValidationException extends BadRequestHttpException implements \Countable, \Iterator
{
    protected $messages = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $messages, int $code = 0, \Exception $previous = null)
    {
        $this->messages = $messages;

        parent::__construct(reset($this->messages) ?: null, $previous, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->messages);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->messages);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->messages);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->messages);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return key($this->messages) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->messages);
    }

    /**
     * Returns list of errors associated with the exception.
     *
     * @return  array
     */
    public function toArray()
    {
        return $this->messages;
    }
}