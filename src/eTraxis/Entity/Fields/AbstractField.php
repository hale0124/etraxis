<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Fields;

use eTraxis\Entity\Field;

/**
 * Abstract facade for specific field type.
 */
abstract class AbstractField implements \ArrayAccess
{
    /** @var Field */
    protected $field;

    /**
     * Constructor.
     *
     * @param   Field $field
     */
    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * Returns list of supported array keys.
     *
     * @return  array
     */
    abstract protected function getSupportedKeys();

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return in_array($offset, $this->getSupportedKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $method = 'get' . ucfirst($offset);

        return $this->$method();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $method = 'set' . ucfirst($offset);

        $this->$method($value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->offsetSet($offset, null);
    }
}
