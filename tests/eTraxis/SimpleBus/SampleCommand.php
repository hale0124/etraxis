<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------


namespace eTraxis\SimpleBus;

/**
 * @property-read integer $property
 */
class SampleCommand
{
    use CommandTrait;

    protected $property;

    public function __construct($property = null)
    {
        $this->property = $property;
    }
}
