<?php

namespace Slad\BookingBundle\Tests\Fixtures\Data\ORM;

namespace Slad\BookingBundle\Tests\Fixtures\Data\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Slad\BookingBundle\Tests\Fixtures\ORM\Entity\Booking;

class LoadBookingData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $booking = new Booking();
        $item = $manager->getRepository('Slad\BookingBundle\Tests\Fixtures\ORM\Entity\BookableItem')
            ->findOneBy(array());

        $booking->setItem($item);
        $booking->setStart(new \DateTime('2014-05-01'));
        $booking->setEnd(new \DateTime('2014-05-09'));

        $manager->persist($booking);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
