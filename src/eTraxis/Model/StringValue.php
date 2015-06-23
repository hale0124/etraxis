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

namespace eTraxis\Model;

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
 * @ORM\Entity
 */
class StringValue
{
    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="value_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * Get 'id'.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set 'token'.
     *
     * @param   string $token
     *
     * @return  self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get 'token'.
     *
     * @return  string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set 'value'.
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
     * Get 'value'.
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->value;
    }
}
