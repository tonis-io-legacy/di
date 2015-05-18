<?php
 
namespace Tonis\Di\Finder;

use Symfony\Component\Finder\Finder;

class ComponentFinder extends Finder
{
    public function __construct()
    {
        parent::__construct();
        
        $this->name('*.php')
             ->contains('Tonis\\Di\\Annotation')
             ->files();
    }
}
