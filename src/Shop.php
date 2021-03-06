<?php

namespace pisc\Shopware;

use Doctrine\Common\Collections\Criteria;
use pisc\Arrr\Ar;
use pisc\Shopware\Category;
use Shopware\Components\Model\ModelEntity;

class Shop
{
	protected $em;

	protected $shopwareCategory;

	public function __construct($entityManager)
	{
		$this->em = $entityManager;
		$this->shopwareCategory = new Category($entityManager);
	}

	public function getShopRepository()
	{
		return $this->em->getRepository("Shopware\Models\Shop\Shop");
	}

	public function getElementRepository()
	{
		return $this->em->getRepository("Shopware\Models\Config\Element");
	}

	public function iterateAll(callable $cb)
	{
		// http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/batch-processing.html

		$batchSize = 20;
		$i = 0;

		$dql = "SELECT s FROM Shopware\Models\Shop\Shop s";

		$q = $this->em->createQuery($dql);

		foreach( $q->iterate() as $row )
		{
		    $shop = $row[0];
		    call_user_func($cb, $shop);

		    if( ($i % $batchSize) === 0 )
		    {
		        $this->em->flush(); // Executes all updates.
		        $this->em->clear(); // Detaches all objects from Doctrine!
		    }

		    ++$i;
		}

		$this->em->flush();
	}

	public static function getAll()
	{
		return $this->getShopRepository()->findAll();
	}

	public function getById($id)
	{
		return $this->getShopRepository()->findOneBy([ "id" => $id ]);
	}

	public function getCategories($shop)
	{
		$category = $shop->getCategory();
		return $this->shopwareCategory->getAllChildren($category);
	}

	public function getArticles($shop)
	{
		return Ar::reduce($this->getCategories($shop), function($arr, $category) {
			return array_merge($arr, $category->getArticles()->toArray());
		}, []);
	}

	public function getMainShop()
	{
		return $this->getShopRepository()->findOneBy([ "main" => null ]);

		// return Shopware()->Shop()->getMain();
	}

	public function getConfigValue($shopId, $key, $default = null)
	{
		if( $shopId instanceof ModelEntity ) $shopId = $shop->getId();

		if( !empty($shopId) )
		{
			$element = $this->getElementRepository()->findOneBy([ "name" => $key ]);

			if( !empty($element) )
			{
				$values = $element->getValues()->toArray();

				$valueModel = Ar::detect($values, function($value) use ($shopId) {
					return $value->getShop()->getId() == $shopId;
				});

				if( $valueModel )
				{
					return $valueModel->getValue();
				}

				return $element->getValue();
			}
		}

		return $default;
	}
}
