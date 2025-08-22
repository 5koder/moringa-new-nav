<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace PrestaShop\Module\IqitProductFlags\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShop\Module\IqitProductFlags\Entity\IqitProductFlag;
use PrestaShop\Module\IqitProductFlags\Entity\IqitProductFlagLang;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShopBundle\Entity\Shop;


class IqitProductFlagsController extends FrameworkBundleAdminController
{
    public function index(Request $request): Response
    {
        // content block list in a grid
        $contentBlockGridFactory = $this->get('prestashop.module.iqitproductflags.grid.iqit_product_flag_grid_factory');
        $contentBlockGrid = $contentBlockGridFactory->getGrid(new SearchCriteria([], 'position', 'asc'));

        // configuration form
       // $configurationForm = $this->get('prestashop.module.iqitproductflags.iqit_product_flag_configuration.form_handler')->getForm();
        
        return $this->render('@Modules/iqitproductflags/views/templates/admin/index.html.twig', [
            'title' => 'Content block list',
            'contentBlockGrid' => $this->presentGrid($contentBlockGrid),
            //'configurationForm' => $configurationForm->createView(),
            'help_link' => false,
        ]);
    }

    public function create(Request $request): Response
    {
        $formDataHandler = $this->get('prestashop.module.iqitproductflags.form.identifiable_object.builder.iqit_product_flag_form_builder');
        $form = $formDataHandler->getForm();
        $form->handleRequest($request);

        $formHandler = $this->get('prestashop.module.iqitproductflags.form.identifiable_object.handler.iqit_product_flag_form_handler');
        $result = $formHandler->handle($form);

        if (null !== $result->getIdentifiableObjectId()) {
            $this->addFlash(
                'success',
                $this->trans('Successful creation.', 'Admin.Notifications.Success')
            );
            $this->clearModuleCache();
            return $this->redirectToRoute('iqitproductflags');
        }

        return $this->render('@Modules/iqitproductflags/views/templates/admin/form.html.twig', [
            'entityForm' => $form->createView(),
            'title' => 'Content block creation',
            'help_link' => false,
        ]);
    }

    public function edit(Request $request, int $contentBlockId): Response
    {
        $formBuilder = $this->get('prestashop.module.iqitproductflags.form.identifiable_object.builder.iqit_product_flag_form_builder');
        $form = $formBuilder->getFormFor((int) $contentBlockId);
        $form->handleRequest($request);

        $formHandler = $this->get('prestashop.module.iqitproductflags.form.identifiable_object.handler.iqit_product_flag_form_handler');
        $result = $formHandler->handleFor($contentBlockId, $form);

        if (null !== $result->getIdentifiableObjectId()) {
            $this->addFlash(
                'success',
                $this->trans('Successful edition.', 'Admin.Notifications.Success')
            );
            $this->clearModuleCache();

            return $this->redirectToRoute('iqitproductflags');
        }

        return $this->render('@Modules/iqitproductflags/views/templates/admin/form.html.twig', [
            'entityForm' => $form->createView(),
            'title' => 'Content block edition',
            'help_link' => false,
        ]);
    }

    public function delete(Request $request, int $contentBlockId): Response
    {
        $contentBlock = $this->getDoctrine()
            ->getRepository(IqitProductFlag::class)
            ->find($contentBlockId);

        if (!empty($contentBlock)) {
            $multistoreContext = $this->get('prestashop.adapter.shop.context');
            $entityManager = $this->get('doctrine.orm.entity_manager');
            if ($multistoreContext->isAllShopContext()) {
                $contentBlock->clearShops();
                $entityManager->remove($contentBlock);
            } else {
                $shopList = $this->getDoctrine()
                    ->getRepository(Shop::class)
                    ->findBy(['id' => $multistoreContext->getContextListShopID()]);
                foreach ($shopList as $shop) {
                    $contentBlock->removeShop($shop);
                    $entityManager->flush();
                }
                if (count($contentBlock->getShops()) === 0) {
                    $entityManager->remove($contentBlock);
                }
            }
            $entityManager->flush();
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
            $this->clearModuleCache();

            return $this->redirectToRoute('iqitproductflags');
        }

        $this->addFlash(
            'error',
            sprintf(
                'Cannot find content block %d',
                $contentBlockId
            )
        );

        return $this->redirectToRoute('iqitproductflags');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveConfiguration(Request $request): Response
    {
        $redirectResponse = $this->redirectToRoute('iqitproductflags');

        $form = $this->get('prestashop.module.iqitproductflags.iqit_product_flag_configuration.form_handler')->getForm();
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $redirectResponse;
        }

        $data = $form->getData();
        $saveErrors = $this->get('prestashop.module.iqitproductflags.iqit_product_flag_configuration.form_handler')->save($data);

        if (0 === count($saveErrors)) {
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
            $this->clearModuleCache();
            return $redirectResponse;
        }

        $this->flashErrors($saveErrors);

        return $redirectResponse;
    }



    /**
     * @param Request $request
     * @param int $contentBlockId
     *
     * @return Response
     */
    public function toggleStatus(Request $request, int $contentBlockId): Response
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $contentBlock = $entityManager
            ->getRepository(IqitProductFlag::class)
            ->findOneBy(['id' => $contentBlockId]);

        if (empty($contentBlock)) {
            return $this->json([
                'status' => false,
                'message' => sprintf('Content block %d doesn\'t exist', $contentBlockId)
            ]);
        }

        try {
            $contentBlock->setEnable(!$contentBlock->getEnable());
            $entityManager->flush();
            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
            ];
            $this->clearModuleCache();
        } catch (\Exception $e) {
            $response = [
                'status' => false,
                'message' => sprintf(
                    'There was an error while updating the status of content block %d: %s',
                    $contentBlockId,
                    $e->getMessage()
                ),
            ];
        }

        return $this->json($response);
    }


    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updatePositions(Request $request): Response
    {
        $positionsData = [
            'positions' => $request->request->get('positions', null),
        ];

        $positionDefinition = new PositionDefinition(
            'iqit_product_flag',
            'id_iqit_product_flag',
            'position',
        );

        /** @var PositionUpdateFactory $positionUpdateFactory */
        $positionUpdateFactory = $this->get('prestashop.core.grid.position.position_update_factory');
        try {
            /** @var PositionUpdate $positionUpdate */
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
        } catch (PositionDataException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);

            return $this->redirectToRoute('admin_link_block_list');

        }

        /** @var GridPositionUpdaterInterface $updater */
        $updater = $this->get('prestashop.core.grid.position.doctrine_grid_position_updater');
        try {
            $updater->update($positionUpdate);
              $this->clearModuleCache();
              $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));


        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('iqitproductflags');
    }

    
    /**
     * clearModuleCache
     *
     * @return void
     */
    public function clearModuleCache(){
        
    }
}
