<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Record;

/**
 * Record's field with current value.
 */
class RecordField
{
    /**
     * @var string Name of the field.
     */
    protected $name;

    /**
     * @var string Type of the field.
     */
    protected $type;

    /**
     * @var mixed Current value.
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param   string $name
     * @param   string $type
     * @param   mixed  $value
     */
    public function __construct(string $name, string $type, $value = null)
    {
        $this->name  = $name;
        $this->type  = $type;
        $this->value = $value;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Property getter.
     *
     * @return  mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
