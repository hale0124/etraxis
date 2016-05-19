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

use eTraxis\Entity\ListItem;
use eTraxis\Form\ListItemForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * List items "GET" controller.
 *
 * @Action\Route("/listitems", condition="request.isXmlHttpRequest()")
 * @Action\Method("GET")
 */
class ListItemsGetController extends Controller
{
    /**
     * Renders dialog to create new list item.
     *
     * @Action\Route("/new/{id}", name="admin_dlg_new_listitem", requirements={"id"="\d+"})
     *
     * @param   int $id Field ID.
     *
     * @return  Response
     */
    public function newAction(int $id): Response
    {
        $form = $this->createForm(ListItemForm::class, null, [
            'action' => $this->generateUrl('admin_new_listitem', ['id' => $id]),
        ]);

        return $this->render('admin/fields/dlg_listitem.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified list item.
     *
     * @Action\Route("/edit/{id}/{key}", name="admin_dlg_edit_listitem", requirements={"id"="\d+", "key"="\d+"})
     * @Action\ParamConverter("item", options={"mapping": {"field": "id", "key": "key"}})
     *
     * @param   ListItem $item
     *
     * @return  Response
     */
    public function editAction(ListItem $item): Response
    {
        $form = $this->createForm(ListItemForm::class, $item, [
            'action' => $this->generateUrl('admin_edit_listitem', [
                'id'  => $item->getField()->getId(),
                'key' => $item->getKey(),
            ]),
        ]);

        return $this->render('admin/fields/dlg_listitem.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
