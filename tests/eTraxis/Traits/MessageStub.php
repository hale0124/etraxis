<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Traits;

use Symfony\Component\Validator\Constraints as Assert;

class MessageStub
{
    use MessageTrait;

    /**
     * @Assert\Range(min = "1", max = "100")
     */
    public $property = 1;

    /**
     * @Assert\Length(max = "10")
     */
    public $name;
}
