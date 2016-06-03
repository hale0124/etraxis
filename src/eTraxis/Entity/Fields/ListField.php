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

use Doctrine\Common\Persistence\ObjectRepository;
use eTraxis\Entity\Field;
use eTraxis\Entity\ListItem;

/**
 * List field.
 */
class ListField extends AbstractField
{
    // Constraints.
    const MIN_ITEM_VALUE  = 1;
    const MAX_ITEM_LENGTH = 50;

    /** @var ObjectRepository */
    protected $repository;

    /**
     * Constructor.
     *
     * @param   Field            $field
     * @param   ObjectRepository $repository
     */
    public function __construct(Field $field, ObjectRepository $repository)
    {
        parent::__construct($field);

        if ($repository->getClassName() !== ListItem::class) {
            throw new \InvalidArgumentException();
        }

        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedKeys()
    {
        return ['defaultValue', 'defaultText'];
    }

    /**
     * Returns all list items of the field.
     *
     * @return  ListItem[]
     */
    public function getItems()
    {
        return $this->repository->findBy(['field' => $this->field], ['value' => 'ASC']);
    }

    /**
     * Sets default item of the field by item's value.
     *
     * @param   int|null $value Item's value.
     *
     * @return  self
     */
    public function setDefaultValue(int $value = null)
    {
        if ($value !== null) {

            /** @var ListItem $item */
            $item = $this->repository->findOneBy([
                'field' => $this->field,
                'value' => $value,
            ]);

            $value = ($item ? $item->getValue() : null);
        }

        $this->field->getParameters()->setDefaultValue($value);

        return $this;
    }

    /**
     * Returns default item's value of the field.
     *
     * @return  int|null Item's value.
     */
    public function getDefaultValue()
    {
        return $this->field->getParameters()->getDefaultValue();
    }

    /**
     * Sets default item of the field by item's text.
     *
     * @param   string|null $text Item's text.
     *
     * @return  self
     */
    public function setDefaultText(string $text = null)
    {
        if ($text !== null) {

            /** @var ListItem $item */
            $item = $this->repository->findOneBy([
                'field' => $this->field,
                'text'  => $text,
            ]);

            $key = ($item ? $item->getValue() : null);
        }
        else {
            $key = null;
        }

        $this->field->getParameters()->setDefaultValue($key);

        return $this;
    }

    /**
     * Returns default item's text of the field.
     *
     * @return  string|null Item's text.
     */
    public function getDefaultText()
    {
        $value = $this->field->getParameters()->getDefaultValue();

        if ($value === null) {
            return null;
        }

        /** @var ListItem $item */
        $item = $this->repository->findOneBy([
            'field' => $this->field,
            'value' => $value,
        ]);

        return $item ? $item->getText() : null;
    }
}
