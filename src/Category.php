<?php

namespace pisc\Shopware;

class Category
{
	protected $em;

	public function __construct($entityManager)
	{
		$this->em = $entityManager;
	}

	public function getCategoryRepository()
	{
		return $this->em->getRepository("Shopware\Models\Category\Category");
	}

	public function getById($id)
	{
		return $this->getCategoryRepository()->findOneBy(["id" => $id]);
	}

	/**
	 * Get children of category
	 * EXCLUDING parents
	 * @param  Shopware\Models\Category\Category  $category
	 * @return Array                                         Array of categories
	 */
	public function getLeafChildren($category)
	{
		$children = $category->getChildren()->toArray();

		if( count($children) > 0 )
		{
			return array_reduce($children, function($arr, $category) {
				return array_merge($arr, $this->getLeafChildren($category));
			}, []);
		}

		return [ $category ];
	}

	/**
	 * Get children of category
	 * INCLUDING parents
	 * @param  Shopware\Models\Category\Category  $category
	 * @return Array                                         Array of categories
	 */
	public function getAllChildren($category)
	{
		$children = $category->getChildren()->toArray();

		if( count($children) > 0 )
		{
			return array_reduce($children, function($arr, $category) {
				return array_merge($arr, $this->getAllChildren($category));
			}, [ $category ]);
		}

		return [ $category ];
	}
}