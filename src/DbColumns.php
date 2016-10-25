<?php 

namespace pisc\Shopware;

class DbColumns
{
	protected $db;
	protected $em;

	public function __construct($db, $entityManager)
	{
		$this->db = $db;
		$this->em = $entityManager;
	}

    public function columnExists($table, $column)
    {
        $table = str_replace(" ", "", $table);

        $sql = "SHOW COLUMNS FROM `{$table}` LIKE :column";

        $row = $this->db->executeQuery($sql, [ ':column' => $column ])->fetch();

        return $row['Field'] == $column;
    }

    public function addAttribute($table, $prefix, $column, $type, $nullable)
    {
    	if( !$this->columnExists($table, "{$prefix}_{$column}") )
    	{
    		$this->em->addAttribute($table, $prefix, $column, $type, $nullable);
    	}
    }

    public function removeAttribute($table, $prefix, $column)
    {
    	if( $this->columnExists($table, "{$prefix}_{$column}") )
    	{
    		$this->em->removeAttribute($table, $prefix, $column);
    	}
    }

    public function regenerateModelcache($table)
    {
		// delete model cache
		$metaDataCacheDoctrine = $this->em->getConfiguration()->getMetadataCacheImpl();
		$metaDataCacheDoctrine->deleteAll();
		
		// regenerate model cache
		$this->em->generateAttributeModels([ 's_order_attributes' ]);
    }
}