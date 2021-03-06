<?php

namespace Oro\Bundle\EntityExtendBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExtensionPass implements CompilerPassInterface
{
    const GENERATOR_NAME = 'oro_entity_extend.entity_generator';
    const GENERATOR_TAG  = 'oro_entity_extend.generator_extension';

    const DUMPER_NAME = 'oro_entity_extend.tools.dumper';
    const DUMPER_TAG  = 'oro_entity_extend.dumper_extension';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::GENERATOR_NAME)) {
            return;
        }

        $linkedServices = [
            self::GENERATOR_NAME => self::GENERATOR_TAG,
            self::DUMPER_NAME    => self::DUMPER_TAG,
        ];

        foreach ($linkedServices as $serviceName => $extensionName) {
            $serviceDefinition = $container->getDefinition($serviceName);
            $taggedServices = $container->findTaggedServiceIds($extensionName);

            foreach ($taggedServices as $id => $tagAttributes) {
                $params = [new Reference($id)];
                if (!empty($tagAttributes['priority'])) {
                    $params[] = (int) $tagAttributes['priority'];
                }

                $serviceDefinition->addMethodCall('addExtension', $params);
            }
        }
    }
}
