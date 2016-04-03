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
use eTraxis\Repository\ListItemsRepository;

/**
 * List field.
 */
class ListField extends AbstractField
{
    // Constraints.
    const MIN_ITEM_VALUE  = 1;
    const MAX_ITEM_LENGTH = 50;

    // Properties.
    protected $field;
    protected $repository;

    /**
     * Constructor.
     *
     * @param   Field               $field
     * @param   ListItemsRepository $repository
     */
    public function __construct(Field $field, ListItemsRepository $repository)
    {
        $this->field      = $field;
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

            /** @var \eTraxis\Entity\ListItem $item */
            $item = $this->repository->findOneBy([
                'fieldId' => $this->field->getId(),
                'key'     => $key,
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

            /** @var \eTraxis\Entity\ListItem $item */
            $item = $this->repository->findOneBy([
                'fieldId' => $this->field->getId(),
                'value'   => $value,
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

        /** @var \eTraxis\Entity\ListItem $item */
        $item = $this->repository->findOneBy([
            'fieldId' => $this->field->getId(),
            'key'     => $key,
        ]);

        return $item ? $item->getValue() : null;
    }
}
