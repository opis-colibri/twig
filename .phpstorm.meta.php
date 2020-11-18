<?php
namespace PHPSTORM_META {

    override(\Opis\Colibri\Core\ItemCollector::collect(0), map([
        'twig-extensions' => \Opis\Colibri\Serializable\ClassList::class,
        'twig-filters' => \Opis\Colibri\Modules\Twig\Collector\TwigContainer::class,
        'twig-functions' => \Opis\Colibri\Modules\Twig\Collector\TwigContainer::class,
    ]));

    override(\Opis\Colibri\collect(0), map([
        'twig-extensions' => \Opis\Colibri\Serializable\ClassList::class,
        'twig-filters' => \Opis\Colibri\Modules\Twig\Collector\TwigContainer::class,
        'twig-functions' => \Opis\Colibri\Modules\Twig\Collector\TwigContainer::class,
    ]));
}