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
class ListField
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
     * Sets default item of the field.
     *
     * @param   int|null $key Item's key.
     *
     * @return  self
     */
    public function setDefaultItem($key)
    {
        if ($key !== null) {

            /** @var \eTraxis\Entity\ListItem $item */
            $item = $this->repository->findOneBy([
                'fieldId' => $this->field->getId(),
                'key'     => $key,
            ]);

            $key = ($item ? $item->getKey() : null);
        }

        $this->field->setDefaultValue($key);

        return $this;
    }

    /**
     * Returns default item of the field.
     *
     * @return  int|null Item's key.
     */
    public function getDefaultItem()
    {
        return $this->field->getDefaultValue();
    }

    /**
     * Returns default value of the field.
     *
     * @return  string|null Item's value.
     */
    public function getDefaultValue()
    {
        $key = $this->field->getDefaultValue();

        if ($key === null) {
            return null;
        }

        /** @var \eTraxis\Entity\ListItem $item */
        $item = $this->repository->findOneBy([
            'fieldId' => $this->field->getId(),
            'key'     => $key,
        ]);

        return ($item ? $item->getValue() : null);
    }
}
