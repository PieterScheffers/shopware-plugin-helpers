<?php

namespace pisc\Shopware;

use Doctrine\Common\Collections\Criteria;
use pisc\Arrr\Ar;

class Shop
{
	protected $em;

	public function __construct()
	{
		$this->em = Shopware()->Models();
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

	public function getById($id)
	{
		return $this->getShopRepository()->findOneBy([ "id" => $id ]);
	}

	public function getConfigValue($shopId, $key)
	{
		if( $shopId instanceof Shopware\Models\Shop\Shop ) $shopId = $shop->getId();

		if( !empty($shopId) )
		{
			$element = $this->getElementRepository()->findOneBy([ "name" => $key ]);

			if( !empty($element) )
			{
				$values = $element->getValues()->toArray();

				$valueModel = Ar::detect(function($value) use ($shopId) {
					return $value->shop === $shopId;
				});

				if( $valueModel )
				{
					return $valueModel->getValue();
				}

				return $element->getValue();
			}
		}
	}

	public function test()
	{
		echo "test";
	}
}