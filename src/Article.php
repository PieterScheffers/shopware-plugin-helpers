<?php

namespace pisc\Shopware;

class Article
{	
	protected $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Returns the url foreach articleid / ordernumber
	 * @param  [type] $shopId [description]
	 * @return [type]         [description]
	 */
	public function getFullUrls($shopId = null)
	{
		/**
		 * SELECT 
		 * ad.articleID, 
		 * ad.ordernumber, 
		 * CONCAT(COALESCE(sm.host, s.host), COALESCE(s.base_url, ''), '/', u.path) as full_url, 
		 * u.subshopID, 
		 * COALESCE(sm.id, s.id) AS main_shop_id 
		 * FROM s_articles a
		 * JOIN s_articles_details ad ON ad.id = a.main_detail_id
		 * JOIN s_core_rewrite_urls u ON u.org_path = CONCAT('sViewport=detail&sArticle=', a.id) AND u.main = 1
		 * JOIN s_core_shops s ON u.subshopID = s.id
		 * LEFT JOIN s_core_shops sm ON s.main_id = sm.id
		 */
		$sql = "SELECT " .
			"ad.articleID, " .
			"ad.ordernumber, ".
			"CONCAT(COALESCE(sm.host, s.host), COALESCE(s.base_url, ''), '/', u.path) as full_url, " .
			"u.subshopID " .
			"FROM s_articles a " .
			"JOIN s_articles_details ad ON ad.id = a.main_detail_id " .
			"JOIN s_core_rewrite_urls u ON u.org_path = CONCAT('sViewport=detail&sArticle=', a.id) AND u.main = 1 " .
			"JOIN s_core_shops s ON u.subshopID = s.id " .
			"LEFT JOIN s_core_shops sm ON s.main_id = sm.id";
		$params = [];

		if( !is_null($shopId) )
		{
			$sql .= "WHERE s.id = :shop_id";
			$params[":shop_id"] = $shopId;
		}

		$query = $this->db->executeQuery($sql, $params);
		return $query->fetchAll();
	}
}