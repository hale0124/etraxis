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
use eTraxis\Repository\StringValuesRepository;

/**
 * String field.
 */
class StringField extends AbstractField
{
    // Constraints.
    const MIN_LENGTH = 1;
    const MAX_LENGTH = 250;

    // Properties.
    protected $repository;

    /**
     * Constructor.
     *
     * @param   Field                  $field
     * @param   StringValuesRepository $repository
     */
    public function __construct(Field $field, StringValuesRepository $repository)
    {
        parent::__construct($field);

        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedKeys()
    {
        return ['maxLength', 'defaultValue'];
    }

    /**
     * Sets maximum allowed length of field values.
     *
     * @param   int $length
     *
     * @return  self
     */
    public function setMaxLength($length)
    {
        if ($length < self::MIN_LENGTH) {
            $length = self::MIN_LENGTH;
        }

        if ($length > self::MAX_LENGTH) {
            $length = self::MAX_LENGTH;
        }

        $this->field->getParameters()->setParameter1($length);

        return $this;
    }

    /**
     * Returns maximum allowed length of field values.
     *
     * @return  int
     */
    public function getMaxLength()
    {
        return $this->field->getParameters()->getParameter1();
    }

    /**
     * Sets default value of the field.
     *
     * @param   string|null $value
     *
     * @return  self
     */
    public function setDefaultValue($value)
    {
        if (mb_strlen($value) > self::MAX_LENGTH) {
            $value = substr($value, 0, self::MAX_LENGTH);
        }

        $id = ($value === null)
            ? null
            : $this->repository->save($value);

        $this->field->getParameters()->setDefaultValue($id);

        return $this;
    }

    /**
     * Returns default value of the field.
     *
     * @return  string|null
     */
    public function getDefaultValue()
    {
        $id = $this->field->getParameters()->getDefaultValue();

        if ($id === null) {
            return null;
        }

        /** @var \eTraxis\Entity\StringValue $value */
        $value = $this->repository->find($id);

        return $value->getValue();
    }
}
