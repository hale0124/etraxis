<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Controller\Admin;

use eTraxis\Entity\Template;
use eTraxis\SimpleBus\Templates;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Templates "POST" controller.
 *
 * @Action\Route("/templates", condition="request.isXmlHttpRequest()")
 * @Action\Method("POST")
 */
class TemplatesPostController extends Controller
{
    use ContainerTrait;

    /**
     * Processes submitted form when new template is being created.
     *
     * @Action\Route("/new/{id}", name="admin_new_template", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Project ID.
     *
     * @return  JsonResponse
     */
    public function newAction(Request $request, $id)
    {
        $data = $request->request->get('template');

        $command = new Templates\CreateTemplateCommand($data, ['project' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Processes submitted form when specified template is being edited.
     *
     * @Action\Route("/edit/{id}", name="admin_edit_template", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Template ID.
     *
     * @return  JsonResponse
     */
    public function editAction(Request $request, $id)
    {
        $template = $this->getDoctrine()->getRepository(Template::class)->find($id);

        if (!$template) {
            throw $this->createNotFoundException();
        }

        $data = $request->request->get('template');

        $command = new Templates\UpdateTemplateCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Deletes specified template.
     *
     * @Action\Route("/delete/{id}", name="admin_delete_template", requirements={"id"="\d+"})
     *
     * @param   int $id Template ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction($id)
    {
        $command = new Templates\DeleteTemplateCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Locks specified template.
     *
     * @Action\Route("/lock/{id}", name="admin_lock_template", requirements={"id"="\d+"})
     *
     * @param   int $id Template ID.
     *
     * @return  JsonResponse
     */
    public function lockAction($id)
    {
        $command = new Templates\LockTemplateCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Unlocks specified template.
     *
     * @Action\Route("/unlock/{id}", name="admin_unlock_template", requirements={"id"="\d+"})
     *
     * @param   int $id Template ID.
     *
     * @return  JsonResponse
     */
    public function unlockAction($id)
    {
        $command = new Templates\UnlockTemplateCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Saves permissions of the specified template.
     *
     * @Action\Route("/permissions/{id}/{group}", name="admin_save_template_permissions", requirements={"id"="\d+", "group"="[\-]?\d+"})
     *
     * @param   Request $request
     * @param   int     $id    Template ID.
     * @param   int     $group Group ID or system role.
     *
     * @return  JsonResponse
     */
    public function savePermissionsAction(Request $request, $id, $group)
    {
        $command = new Templates\RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => $group,
            'permissions' => PHP_INT_MAX,
        ]);

        $this->getCommandBus()->handle($command);

        $command = new Templates\AddTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => $group,
            'permissions' => (int) $request->request->get('permissions'),
        ]);

        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
