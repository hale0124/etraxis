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
 * Field's deprecated features.
 *
 * @deprecated 4.1.0
 * @ORM\Embeddable
 */
class FieldDeprecated
{
    /**
     * @ORM\Column(name="guest_access", type="integer")
     */
    private $guestAccess;

    /**
     * @ORM\Column(name="add_separator", type="integer")
     */
    private $addSeparator;

    /**
     * @ORM\Column(name="show_in_emails", type="integer")
     */
    private $showInEmails;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->guestAccess  = 0;
        $this->addSeparator = 0;
        $this->showInEmails = 0;
    }
}
