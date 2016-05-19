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
 * Template's deprecated features.
 *
 * @deprecated 4.1.0
 * @ORM\Embeddable
 */
class TemplateDeprecated
{
    /**
     * @ORM\Column(name="guest_access", type="integer")
     */
    private $guestAccess;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->guestAccess = 0;
    }
}
