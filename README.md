# shopware-plugin-helpers
Helper classes for Shopware plugin development


## Install

#### Shopware >= 4.3 plugin
composer.json
```
{
    "require": {
        "pisc/shopware-plugin-helpers": "^0.0.5"
    },
    "autoload": {
        "psr-4": {
            "Shopware\\Plugins\\SwagSloganOfTheDay": "src/"
        }
    }
}
```

Bootstrap.php

```
public function install()
{
	$this->subscribeEvent('Enlight_Controller_Front_DispatchLoopStartup', 'onStartDispatch');
}

public function onStartDispatch(Enlight_Event_EventArgs $args)
{
    $this->registerMyComponents();

    $subscribers = [
    	new Shopware\Plugins\SwagSloganOfTheDay\MySubscriber;
    ];

    foreach ($subscribers as $subscriber) {
        $this->Application()->Events()->addSubscriber($subscriber);
    }
}

public function registerMyComponents()
{
	// instead of:
    // $this->Application()->Loader()->registerNamespace(
    //     'Shopware\Plugins\SwagSloganOfTheDay',
    //     $this->Path() . "/src"
    // );

   	// require composer autoload
    require_once $this->Path() . 'vendor/autoload.php';
}
```

#### Shopware >= 5.2 plugin

composer.json
```
"require": {
    "pisc/shopware-plugin-helpers": "*"
}
```
