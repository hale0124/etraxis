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
use eTraxis\Entity\DecimalValue;
use eTraxis\Entity\Field;

/**
 * Decimal field.
 */
class DecimalField extends AbstractField
{
    // Constraints.
    const MIN_VALUE = '-9999999999.9999999999';
    const MAX_VALUE = '9999999999.9999999999';

    /** @var \eTraxis\Repository\CustomValuesRepositoryInterface */
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

        if ($repository->getClassName() !== DecimalValue::class) {
            throw new \InvalidArgumentException();
        }

        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedKeys()
    {
        return ['minValue', 'maxValue', 'defaultValue'];
    }

    /**
     * Sets minimum allowed value of the field.
     *
     * @param   string $value
     *
     * @return  self
     */
    public function setMinValue(string $value)
    {
        if (bccomp($value, self::MIN_VALUE) < 0) {
            $value = self::MIN_VALUE;
        }

        if (bccomp($value, self::MAX_VALUE) > 0) {
            $value = self::MAX_VALUE;
        }

        $id = $this->repository->save($value);
        $this->field->getParameters()->setParameter1($id);

        return $this;
    }

    /**
     * Returns minimum allowed value of the field.
     *
     * @return  string
     */
    public function getMinValue()
    {
        /** @var DecimalValue $value */
        $value = $this->repository->find($this->field->getParameters()->getParameter1());

        return $value->getValue();
    }

    /**
     * Sets maximum allowed value of the field.
     *
     * @param   string $value
     *
     * @return  self
     */
    public function setMaxValue(string $value)
    {
        if (bccomp($value, self::MIN_VALUE) < 0) {
            $value = self::MIN_VALUE;
        }

        if (bccomp($value, self::MAX_VALUE) > 0) {
            $value = self::MAX_VALUE;
        }

        $id = $this->repository->save($value);
        $this->field->getParameters()->setParameter2($id);

        return $this;
    }

    /**
     * Returns maximum allowed value of the field.
     *
     * @return  string
     */
    public function getMaxValue()
    {
        /** @var DecimalValue $value */
        $value = $this->repository->find($this->field->getParameters()->getParameter2());

        return $value->getValue();
    }

    /**
     * Sets default value of the field.
     *
     * @param   string|null $value
     *
     * @return  self
     */
    public function setDefaultValue(string $value = null)
    {
        if ($value !== null) {

            if (bccomp($value, self::MIN_VALUE) < 0) {
                $value = self::MIN_VALUE;
            }

            if (bccomp($value, self::MAX_VALUE) > 0) {
                $value = self::MAX_VALUE;
            }
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

        /** @var DecimalValue $value */
        $value = $this->repository->find($id);

        return $value->getValue();
    }
}
