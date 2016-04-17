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
        return ['defaultKey', 'defaultValue'];
    }

    /**
     * Sets default item of the field by item's key.
     *
     * @param   int|null $key Item's key.
     *
     * @return  self
     */
    public function setDefaultKey($key)
    {
        if ($key !== null) {

            /** @var ListItem $item */
            $item = $this->repository->findOneBy([
                'field' => $this->field,
                'key'   => $key,
            ]);

            $key = ($item ? $item->getKey() : null);
        }

        $this->field->getParameters()->setDefaultValue($key);

        return $this;
    }

    /**
     * Returns default item's key of the field.
     *
     * @return  int|null Item's key.
     */
    public function getDefaultKey()
    {
        return $this->field->getParameters()->getDefaultValue();
    }

    /**
     * Sets default item of the field by item's value.
     *
     * @param   string|null $value Item's value.
     *
     * @return  self
     */
    public function setDefaultValue($value)
    {
        if ($value !== null) {

            /** @var ListItem $item */
            $item = $this->repository->findOneBy([
                'field' => $this->field,
                'value' => $value,
            ]);

            $key = ($item ? $item->getKey() : null);
        }
        else {
            $key = null;
        }

        $this->field->getParameters()->setDefaultValue($key);

        return $this;
    }

    /**
     * Returns default item's value of the field.
     *
     * @return  string|null Item's value.
     */
    public function getDefaultValue()
    {
        $key = $this->field->getParameters()->getDefaultValue();

        if ($key === null) {
            return null;
        }

        /** @var ListItem $item */
        $item = $this->repository->findOneBy([
            'field' => $this->field,
            'key'   => $key,
        ]);

        return $item ? $item->getValue() : null;
    }
}
