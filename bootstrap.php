<?php

use Doctrine\ORM\Tools\Setup,
    Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Application,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface;

class DoctrineBootstrap
{
    public static function getEntityManager()
    {
        $metaDataConfig = Setup::createYAMLMetadataConfiguration(array(__DIR__."/bin/Mapping"), $isDevMode = true);
        require __DIR__ . '/app/config/config.php';
        $conn = $config['db'];

        return $entityManager = EntityManager::create($conn, $metaDataConfig);
    }
}
