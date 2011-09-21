<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

class RegisterEventListenersAndSubscribersPass implements CompilerPassInterface
{
    private $container;
    private $documentManagers;
    private $eventManagers;

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('doctrine_phpcr.odm.default_document_manager')) {
            return;
        }

        $this->container = $container;
        $this->documentManagers = $container->getParameter('doctrine_phpcr.document_managers');

        foreach ($container->findTaggedServiceIds('doctrine_phpcr.event_subscriber') as $subscriberId => $instances) {
            $this->registerSubscriber($subscriberId, $instances);
        }

        foreach ($container->findTaggedServiceIds('doctrine_phpcr.event_listener') as $listenerId => $instances) {
            $this->registerListener($listenerId, $instances);
        }
    }

    protected function registerSubscriber($subscriberId, $instances)
    {
        $sessions = array();
        foreach ($instances as $attributes) {
            if (isset($attributes['document_manager'])) {
                $sessions[] = $attributes['document_manager'];
            } else {
                $sessions = array_keys($this->documentManagers);
                break;
            }
        }

        foreach ($sessions as $name) {
            $this->getEventManager($name, $subscriberId)->addMethodCall('addEventSubscriber', array(new Reference($subscriberId)));
        }
    }

    protected function registerListener($listenerId, $instances)
    {
        $sessions = array();
        foreach ($instances as $attributes) {
            if (!isset($attributes['event'])) {
                throw new \InvalidArgumentException(sprintf('Doctrine event listener "%s" must specify the "event" attribute.', $listenerId));
            }

            if (isset($attributes['document_manager'])) {
                $cs = array($attributes['document_manager']);
            } else {
                $cs = array_keys($this->documentManagers);
            }

            foreach ($cs as $session) {
                if (!isset($sessions[$session]) || !is_array($sessions[$session])) {
                    $sessions[$session] = array();
                }
                $sessions[$session][] = $attributes['event'];
            }
        }

        foreach ($sessions as $name => $events) {
            $this->getEventManager($name, $listenerId)->addMethodCall('addEventListener', array(
                array_unique($events),
                new Reference($listenerId),
            ));
        }
    }

    private function getEventManager($name, $listenerId = null)
    {
        if (null === $this->eventManagers) {
            $this->eventManagers = array();
            foreach ($this->documentManagers as $n => $id) {
                $arguments = $this->container->getDefinition($id)->getArguments();
                $this->eventManagers[$n] = $this->container->getDefinition((string) $arguments[2]);
            }
        }

        if (!isset($this->eventManagers[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine session "%s" does not exist but is referenced in the "%s" event listener.', $name, $listenerId));
        }

        return $this->eventManagers[$name];
    }
}
