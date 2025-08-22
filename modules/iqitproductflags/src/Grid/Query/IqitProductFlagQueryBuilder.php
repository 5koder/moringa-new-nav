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

namespace PrestaShop\Module\IqitProductFlags\Grid\Query;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;

final class IqitProductFlagQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var Context
     */
    private $shopContext;

    /**
     * @var int
    */
    private $languageId;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;


    private const ALLOWED_FILTERS = [
        'title',
        'position',
    ];


    /**
     * IqitProductFlagQueryBuilder constructor.
     *
     * @param Connection $connection
     * @param $dbPrefix
     * @param Context $shopContext
     * @param $languageId
     */
    public function __construct(Connection $connection, $dbPrefix, DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator, Context $shopContext, $languageId)
    {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->shopContext = $shopContext;
        $this->languageId = $languageId;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery();
        $qb->select('pf.*, pfl.title, pfl.link');

        if (!$this->shopContext->isAllShopContext()) {
            $qb->join('pf', $this->dbPrefix . 'iqit_product_flag_shop', 'pfs', 'pfs.id_iqit_product_flag = pf.id_iqit_product_flag')
                ->where('pfs.id_shop in (' . implode(', ', $this->shopContext->getContextListShopID()) . ')')
                ->groupBy('pf.id_iqit_product_flag');
        }


        $qb->innerJoin('pf', $this->dbPrefix . 'iqit_product_flag_lang', 'pfl', 'pfl.id_iqit_product_flag = pf.id_iqit_product_flag')
        ->andWhere('pfl.`id_lang`= :language')
        ->setParameter('language', $this->languageId);

        $this->searchCriteriaApplicator
        ->applySorting($searchCriteria, $qb)
        ->applyPagination($searchCriteria, $qb);
        

        return $qb;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery();
        $qb->select('COUNT(DISTINCT pf.id_iqit_product_flag)');
        if (!$this->shopContext->isAllShopContext()) {
            $qb->join('pf', $this->dbPrefix . 'iqit_product_flag_shop', 'pfs', 'pfs.id_iqit_product_flag = pf.id_iqit_product_flag')
                ->where('pfs.id_shop in (' . implode(', ', $this->shopContext->getContextListShopID()) . ')');
        }

        return $qb;
    }


    /**
     * @return QueryBuilder
     */
    private function getBaseQuery(): QueryBuilder
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'iqit_product_flag', 'pf');
    }
}
