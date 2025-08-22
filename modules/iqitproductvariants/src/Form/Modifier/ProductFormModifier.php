<?php

declare(strict_types=1);

namespace PrestaShop\Module\IqitProductVariants\Form\Modifier;

use PrestaShopBundle\Form\FormBuilderModifier;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Context;
use Module;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use PrestaShopBundle\Translation\TranslatorInterface;
use PrestaShopBundle\Form\Admin\Type\ProductSearchType;
use Symfony\Component\Routing\RouterInterface;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Sell\Product\Description\RelatedProductType;

use PrestaShop\Module\IqitProductVariants\Form\Product\ProductFormDataHandler;

final class ProductFormModifier
{
    /**
     * @var FormBuilderModifier
     */
    private $formBuilderModifier;
    private $shopContext;
    private $translator;

      /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $employeeIsoCode;

    /**
     * @param FormBuilderModifier $formBuilderModifier
     */
    public function __construct(
        FormBuilderModifier $formBuilderModifier, Context $shopContext, TranslatorInterface $translator,
        RouterInterface $router,  string $employeeIsoCode
    ) {
        $this->formBuilderModifier = $formBuilderModifier;
        $this->shopContext = $shopContext;
        $this->translator = $translator;
        $this->router = $router;
        $this->employeeIsoCode = $employeeIsoCode;
    }

    /**
     * @param int|null $productId
     * @param FormBuilderInterface $productFormBuilder
     */
    public function modify(
        int $productId,
        FormBuilderInterface $productFormBuilder,
        array $data 
    ): void {

       

        $descTabFormBuilder = $productFormBuilder->get('description');
        $this->formBuilderModifier->addAfter(
            $descTabFormBuilder, // the tab
            'related_products', // the input/form from which to insert after/before
            'variants', // your field name
            EntitySearchInputType::class, [
                'label' => $this->translator->trans('Variants',  [], 'Modules.Iqitproductvariants.Admin'),
                'label_tag_name' => 'h3',
                'entry_type' => RelatedProductType::class,
                'entry_options' => [
                    'block_prefix' => 'related_product',
                ],
                'remote_url' => $this->router->generate('admin_products_search_products_for_association', [
                    'languageCode' => $this->employeeIsoCode,
                    'query' => '__QUERY__',
                ]),
                'min_length' => 3,
                'filtered_identities' => $productId > 0 ? [$productId] : [],
                'placeholder' => $this->translator->trans('Search product',  [], 'Modules.Iqitproductvariants.Admin'),
                'data' => $data,
            ],
            $data
        );
    }
}




