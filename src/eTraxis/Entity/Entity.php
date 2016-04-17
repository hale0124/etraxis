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

use Doctrine\ORM\EntityManagerInterface;

/**
 * Base entity class with EntityManager injected.
 */
class Entity
{
    /** @var EntityManagerInterface */
    protected $manager;

    /**
     * Dependency Injection setter.
     *
     * @param   EntityManagerInterface $manager
     *
     * @return  self
     */
    public function setEntityManager(EntityManagerInterface $manager)
    {
        $this->manager = $manager;

        return $this;
    }
}
