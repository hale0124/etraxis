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
 * @ORM\Table(name="text_values",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(columns={"token"})
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
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string Value token.
     *
     * @ORM\Column(name="token", type="string", length=32)
     */
    private $token;

    /**
     * @var string Text value.
     *
     * @ORM\Column(name="value", type="text")
     */
    private $value;

    /**
     * Creates new text value.
     *
     * @param   string $value
     */
    public function __construct(string $value)
    {
        $this->token = md5($value);
        $this->value = $value;
    }

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
     * Property getter.
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->value;
    }
}
