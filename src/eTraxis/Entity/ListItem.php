<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;
use eTraxis\Dictionary\FieldType;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * List item.
 *
 * @ORM\Table(name="list_items",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(columns={"field_id", "item_value"}),
 *                @ORM\UniqueConstraint(columns={"field_id", "item_text"})
 *            })
 * @ORM\Entity
 * @Assert\UniqueEntity(fields={"field", "value"}, message="listitem.conflict.value")
 * @Assert\UniqueEntity(fields={"field", "text"}, message="listitem.conflict.text")
 */
class ListItem
{
    // Constraints.
    const MAX_TEXT = 50;

    /**
     * @var Field Field.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $field;

    /**
     * @var int Value of the item.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="item_value", type="integer")
     */
    private $value;

    /**
     * @var string Text of the item.
     *
     * @ORM\Column(name="item_text", type="string", length=50)
     */
    private $text;

    /**
     * Adds new item to specified field of "List" type.
     *
     * @param   Field $field
     */
    public function __construct(Field $field)
    {
        if ($field->getType() === FieldType::LIST) {
            $this->field = $field;
        }
    }

    /**
     * Property getter.
     *
     * @return  Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Property setter.
     *
     * @param   int $value
     *
     * @return  self
     */
    public function setValue(int $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Property setter.
     *
     * @param   string $text
     *
     * @return  self
     */
    public function setText(string $text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getText()
    {
        return $this->text;
    }
}
