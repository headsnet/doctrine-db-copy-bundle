<?php
declare(strict_types=1);

namespace Headsnet\DoctrineDbCopyBundle;

use Headsnet\DoctrineDbCopyBundle\Console\CopyDbCommand;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class HeadsnetDoctrineDbCopyBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('connection')
                    ->defaultValue('doctrine.dbal.default_connection')
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->services()
            ->set('headsnet.copy_db_command', CopyDbCommand::class)
            ->args([service($config['connection'])])
            ->tag('console.command')
        ;
    }
}
