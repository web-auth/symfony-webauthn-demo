<?php

/**
 * All credits to Grégoire Pineau (@lyrixx)
 * https://gist.github.com/lyrixx/2ea147609dc632d26e366aef60f61a9f
 */

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload_runtime.php';

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

return function () {
    $k = new class('dev', true) extends Kernel implements CompilerPassInterface {
        use MicroKernelTrait;

        public function process(ContainerBuilder $container)
        {
            $container->getAlias('logger')
                ->setPublic(true);
            $container->getDefinition('monolog.handler.console')
                ->setPublic(true);
        }
    };
    $k->boot();
    $c = $k->getContainer();

    $c->get('monolog.handler.console')
        ->setOutput(new StreamOutput(fopen('php://stdout', 'w'), StreamOutput::VERBOSITY_VERY_VERBOSE));

    $logger = $c->get('logger');
    $em = $c->get('doctrine.orm.entity_manager');

    // Start of playground
};
