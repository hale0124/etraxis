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

namespace eTraxis\Collection;

/**
 * Static collection of themes.
 */
class Theme extends AbstractStaticCollection
{
    /**
     * {@inheritdoc}
     */
    public static function getCollection()
    {
        return [
            'allblacks' => 'All Blacks',
            'azure'     => 'Azure',
            'emerald'   => 'Emerald',
            'humanity'  => 'Humanity',
            'mars'      => 'Mars',
            'nexada'    => 'Nexada',
        ];
    }
}
