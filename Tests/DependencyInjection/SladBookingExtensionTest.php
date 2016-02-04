<?php
/**
 * Created by PhpStorm.
 * User: melifaro
 * Date: 4/26/14
 * Time: 7:39 PM
 */

namespace Slad\BookingBundle\Tests\DependencyInjection;
use Slad\BookingBundle\DependencyInjection\SladBookingExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * @covers Slad\BookingBundle\DependencyInjection\Configuration
 * @covers Slad\BookingBundle\DependencyInjection\SladBookingExtension
 */
class SladBookingExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $containerBuilder;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWithoutRequiredParameter()
    {
        $loader = new SladBookingExtension();

        $config = array();

        $loader->load($config, new ContainerBuilder());
    }

    public function testLoadWithRequiredParameters()
    {
        $loader = new SladBookingExtension();
        $config = array('slad_booking' => array(
            'entity_class'=>'Vendor\Bundle\Entity\Class')
        );

        $loader->load($config, new ContainerBuilder());
    }

    public function testContainerHasNeccessaryServices()
    {
        $this->loadConfiguration();

        $entityClass = $this->containerBuilder->getParameter('slad_booking.entity_class');
        $services = $this->containerBuilder->getServiceIds();

        $this->assertEquals('Vendor\Bundle\Entity\Class', $entityClass);
        $this->assertContains('booker', $services);
        $this->assertContains('booking_calendar', $services);

    }

    protected function loadConfiguration()
    {
        $this->containerBuilder = new ContainerBuilder();
        $loader = new SladBookingExtension();
        $config = array('slad_booking' => array(
            'entity_class'=>'Vendor\Bundle\Entity\Class')
        );

        $loader->load($config, $this->containerBuilder);

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $this->containerBuilder);
    }
}
