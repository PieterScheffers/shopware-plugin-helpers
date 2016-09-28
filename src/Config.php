<?php

namespace pisc\Shopware;

use Shopware_Components_Plugin_Bootstrap;
use Shopware\Models\Config\Element;
use Shopware_Components_Config;
use pisc\Arrr\Ar;

class ShopwareConfig 
{
	protected $em;

	protected $prefix;

	protected $elements;

	protected $translations;	

	public function __construct($entityManager, $prefix, array $elements = [], array $translations = [], $settings = [])
	{
		$this->em = $entityManager;
		$this->prefix = $prefix;
		$this->translations = $translations;

		// if settings is a file path,
		// get the json contents of the file
		if( is_string($settings) )
		{
			$settings = $this->getSettings($settings);
		}

		$this->elements = $this->rewriteElements($elements, $settings);
	}

	public static function getName($name)
	{
		return $this->prefix . "_" . $name;
	}

	public function getValue($shopId, $key, $default = null)
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

		return $default;
	}

    /**
     * Create Shopware Backend Config menu for plugin
     */
	public function createConfig( Shopware_Components_Plugin_Bootstrap $bootstrap )
	{
		$settings = $this->getSettings();

		$elements = $this->getElements($settings);

		$form = $bootstrap->Form();

		foreach( $elements as $key => $element ) 
		{
			// same as $form->setElement() with $element array values as arguments
			call_user_func_array([ $form, 'setElement' ], $element);
		}

		$this->translate($form);
	}

	protected function translate($form)
	{
		$shopRepository = $this->em->getRepository('\Shopware\Models\Shop\Locale');
 	
		$translations = $this->getTranslations();
 
	    // iterate the languages
	    foreach( $translations as $locale => $snippets ) 
	    {
	        $localeModel = $shopRepository->findOneBy([ 'locale' => $locale ]);
	 
	        // not found? continue with next language
	        if( $localeModel === null )
	        {
	            continue;
	        }
	 
	        // iterate all snippets of the current language
	        foreach( $snippets as $element => $snippet ) 
	        {
	            // get the form element by name
	            $elementModel = $form->getElement($element);
	 
	            // not found? continue with next snippet
	            if( $elementModel === null ) 
	            {
	                continue;
	            }
	 
	            // create new translation model
	            $translationModel = new \Shopware\Models\Config\ElementTranslation();
	            $translationModel->setLabel($snippet);
	            $translationModel->setLocale($localeModel);
	 
	            // add the translation to the form element
	            $elementModel->addTranslation($translationModel);
	        }
	    }
	}

	protected function rewriteElements($elements, $settings = [])
	{
		return Ar::map($elements, function($element) {
			$name = $element[1];
			$element[1] = $this->getName($name);
			
			$options = $element[2];
			$options['value'] = $this->getDefault($settings, $name, $options['value']);
			$element[2] = $options;

			return $element;
		});
	}

	public function getElementNames()
	{
		return Ar::map($this->getElements(), function($element) {
			return $element[1];
		});
	}

	/**
	 * Get default settings from a json file,
	 * so you don't have to put in the settings after every reinstall
	 */
	protected function getSettings($path)
	{
		if( file_exists($path) && is_file($path) )
		{
			try
			{
				return json_decode(file_get_contents($path), true);
			}
			catch(Exception $ex)
			{
				return null;
			}
		}

		return null;
	}

	/**
	 * Get a default value from the provided settings
	 */
	protected function getDefault($settings, $key, $default)
	{
		return isset($settings[$key]) ? $settings[$key] : $default;
	}
}