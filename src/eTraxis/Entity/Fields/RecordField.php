<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Fields;

use eTraxis\Entity\Field;

/**
 * Record field.
 */
class RecordField
{
    // Properties.
    protected $field;

    /**
     * Constructor.
     *
     * @param   Field $field
     */
    public function __construct(Field $field)
    {
        $this->field = $field;
    }
}
