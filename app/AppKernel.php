<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {

        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new AppBundle\AppBundle(),
            new Atico\Bundle\SpreadsheetTranslatorBundle\SpreadsheetTranslatorBundle(),
        ];

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $path = sprintf(
            '%s/config/%2$s/config_%2$s.yml',
            __DIR__,
            $this->getEnvironment()
        );
        $loader->load($path);
    }
}
