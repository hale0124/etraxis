<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus;

/**
 * Command Bus.
 */
interface CommandBusInterface
{
    /**
     * Handles specified command.
     *
     * @param   object $command Data transfer object.
     *
     * @return  mixed|null
     */
    public function handle($command);
}
