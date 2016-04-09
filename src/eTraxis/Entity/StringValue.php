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

/**
 * String value.
 *
 * @ORM\Table(name="tbl_string_values",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_string_values", columns={"value_token"})
 *            },
 *            indexes={
 *                @ORM\Index(name="ix_svl_id_val", columns={"value_id", "string_value"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\StringValuesRepository")
 */
class StringValue
{
    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="value_id", type="integer")
     */
    private $id;

    /**
     * @var string Value token.
     *
     * @ORM\Column(name="value_token", type="string", length=32)
     */
    private $token;

    /**
     * @var string String value.
     *
     * @ORM\Column(name="string_value", type="string", length=250)
     */
    private $value;

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Property setter.
     *
     * @param   string $value
     *
     * @return  self
     */
    public function setValue($value)
    {
        $this->token = md5($value);
        $this->value = $value;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->value;
    }
}
