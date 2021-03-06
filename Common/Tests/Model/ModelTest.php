<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Common\Tests\Model;

use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Validator\Validation;
use Xabbuh\XApi\Common\Serializer\Event\ActorEventSubscriber;
use Xabbuh\XApi\Common\Serializer\Event\DocumentDataWrapper;
use Xabbuh\XApi\Common\Serializer\Event\ObjectEventSubscriber;
use Xabbuh\XApi\Common\Serializer\Event\SetSerializedTypeEventSubscriber;
use Xabbuh\XApi\Common\Serializer\Handler\DocumentDataUnwrapper;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \JMS\Serializer\Serializer
     */
    protected $serializer;

    /**
     * @var \JMS\Serializer\EventDispatcher\EventSubscriberInterface[]
     */
    protected $subscribers = array();

    /**
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;

    protected function setUp()
    {
        $builder = SerializerBuilder::create();
        $builder->addMetadataDir(
            __DIR__.'/../../metadata/serializer',
            'Xabbuh\XApi\Common\Model'
        );
        $builder->configureListeners(
            function (EventDispatcher $dispatcher) {
                $dispatcher->addSubscriber(new ActorEventSubscriber());
                $dispatcher->addSubscriber(new DocumentDataWrapper());
                $dispatcher->addSubscriber(new ObjectEventSubscriber());
                $dispatcher->addSubscriber(new SetSerializedTypeEventSubscriber());
            }
        );
        $builder->configureHandlers(
            function (HandlerRegistryInterface $registry) {
                $registry->registerSubscribingHandler(new DocumentDataUnwrapper());
            }
        );
        $this->serializer = $builder->build();

        $this->validator = Validation::createValidatorBuilder()
            ->addXmlMapping(__DIR__.'/../../metadata/validator/Activity.xml')
            ->addXmlMapping(__DIR__.'/../../metadata/validator/Agent.xml')
            ->addXmlMapping(__DIR__.'/../../metadata/validator/Group.xml')
            ->addXmlMapping(__DIR__.'/../../metadata/validator/Statement.xml')
            ->addXmlMapping(__DIR__.'/../../metadata/validator/StatementReference.xml')
            ->getValidator();
    }

    protected function loadFixture($name)
    {
        return file_get_contents(__DIR__.'/fixtures/'.$name.'.json');
    }

    protected function serialize($data)
    {
        return $this->serializer->serialize($data, 'json');
    }

    protected function deserialize($fixture)
    {
        $type = str_replace(array('Tests\\', 'Test'), '', get_class($this));

        return $this->serializer->deserialize($fixture, $type, 'json');
    }

    protected function serializeAndValidateData($expected, $data)
    {
        $actual = $this->serialize($data);
        $this->assertEquals(json_decode($expected), json_decode($actual));
    }

    protected function loadAndDeserialize($name)
    {
        return $this->deserialize($this->loadFixture($name));
    }
}
