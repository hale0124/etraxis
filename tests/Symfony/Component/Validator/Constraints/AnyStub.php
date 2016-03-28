<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace Symfony\Component\Validator\Constraints;

class AnyStub
{
    /**
     * @NotNull()
     * @Any({
     *     @LessThanOrEqual(value = "-100"),
     *     @GreaterThanOrEqual(value = "100")
     * })
     */
    public $id;
}
