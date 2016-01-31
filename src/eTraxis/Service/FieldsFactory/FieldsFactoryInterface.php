<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service\FieldsFactory;

/**
 * Fields factory interface.
 */
interface FieldsFactoryInterface
{
    /**
     * Returns command to create a field.
     *
     * @param   int   $type   Field type.
     * @param   array $values Initial values.
     *
     * @return  \eTraxis\SimpleBus\Fields\CreateFieldBaseCommand|null
     */
    public function getCreateCommand($type, $values = []);

    /**
     * Returns command to update a field.
     *
     * @param   int   $type   Field type.
     * @param   array $values Initial values.
     *
     * @return  \eTraxis\SimpleBus\Fields\UpdateFieldBaseCommand|null
     */
    public function getUpdateCommand($type, $values = []);
}
