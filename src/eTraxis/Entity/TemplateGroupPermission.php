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

use Doctrine\ORM\Mapping as ORM;
use eTraxis\Dictionary\TemplatePermission;

/**
 * Template/Group permission.
 *
 * @ORM\Table(name="template_group_permissions")
 * @ORM\Entity
 */
class TemplateGroupPermission
{
    /**
     * @var Template Template.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="groupPermissions")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $template;

    /**
     * @var Group Group.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $group;

    /**
     * @var string Permission granted to the group for this template.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="permission", type="string", length=20)
     */
    private $permission;

    /**
     * Constructor.
     *
     * @param   Template $template
     * @param   Group    $group
     * @param   string   $permission
     */
    public function __construct(Template $template, Group $group, string $permission)
    {
        $this->template = $template;
        $this->group    = $group;

        if (TemplatePermission::has($permission)) {
            $this->permission = $permission;
        }
    }

    /**
     * Property getter.
     *
     * @return  Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Property getter.
     *
     * @return  Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
