<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

class TestStaticCollection extends AbstractStaticCollection
{
    public static function getCollection()
    {
        return [
            'b_ok'     => 'button.ok',
            'b_cancel' => 'button.cancel',
            'b_yes'    => 'button.yes',
            'b_no'     => 'button.no',
        ];
    }
}
