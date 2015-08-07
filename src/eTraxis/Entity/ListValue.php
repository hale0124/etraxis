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

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * List value.
 *
 * @ORM\Table(name="tbl_list_values",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_list_values", columns={"field_id", "str_value"})
 *            },
 *            indexes={
 *                @ORM\Index(name="ix_lvl_id_val", columns={"field_id", "int_value", "str_value"})
 *            })
 * @ORM\Entity
 */
class ListValue
{
    /**
     * @var int Field ID.
     *
     * @ORM\Column(name="field_id", type="integer")
     * @ORM\Id
     */
    private $fieldId;

    /**
     * @var int Value key.
     *
     * @ORM\Column(name="int_value", type="integer")
     * @ORM\Id
     */
    private $key;

    /**
     * @var string String value.
     *
     * @ORM\Column(name="str_value", type="string", length=50)
     */
    private $value;

    /**
     * @var Field Field.
     *
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="field_id", onDelete="CASCADE")
     */
    private $field;

    /**
     * Standard setter.
     *
     * @param   int $fieldId
     *
     * @return  self
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * Standard setter.
     *
     * @param   int $key
     *
     * @return  self
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Standard setter.
     *
     * @param   string $value
     *
     * @return  self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Standard setter.
     *
     * @param   Field $field
     *
     * @return  self
     */
    public function setField(Field $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Field
     */
    public function getField()
    {
        return $this->field;
    }
}
