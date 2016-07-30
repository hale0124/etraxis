<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Record;

/**
 * Record's state.
 */
class RecordState
{
    /**
     * @var string Name of the state.
     */
    protected $name;

    /**
     * @var RecordField[] Fields of the state.
     */
    protected $fields;

    /**
     * Constructor.
     *
     * @param   string        $name
     * @param   RecordField[] $fields
     */
    public function __construct(string $name, array $fields = [])
    {
        $this->name   = $name;
        $this->fields = $fields;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns all fields which are present in the specified state of the record.
     *
     * New fields could be created (and existing could be removed) after the record was in the state last time,
     * so current set of fields in the state may differ from set of fields which was used initially. As result,
     * we need list of fields from record's current values rather than from appropriate state.
     *
     * @return  RecordField[]
     */
    public function getFields()
    {
        return $this->fields;
    }
}
