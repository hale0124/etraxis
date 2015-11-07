<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

use eTraxis\Entity\Template;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

/**
 * Voter for "Template" objects.
 */
class TemplateVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return ['eTraxis\Entity\Template'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        /** @var Template $object */
        switch ($attribute) {

            default:
                return false;
        }
    }
}
