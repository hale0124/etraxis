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
 * Text value.
 *
 * @ORM\Table(name="tbl_text_values",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_text_values", columns={"value_token"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\TextValuesRepository")
 */
class TextValue
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
     * @var string Text value.
     *
     * @ORM\Column(name="text_value", type="text")
     */
    private $value;

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  string
     */
    public function getToken()
    {
        return $this->token;
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
}
