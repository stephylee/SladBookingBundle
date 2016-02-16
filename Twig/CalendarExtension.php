<?php

namespace Slad\BookingBundle\Twig;

use Doctrine\Bundle\DoctrineBundle\Registry;

class CalendarExtension extends \Twig_Extension
{
    /**
     * Entity class
     * @var string
     */
    private $entity;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @param string   $entity
     * @param Registry $doctrine
     */
    public function __construct($entity, Registry $doctrine)
    {
        $this->entity   = $entity;
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('slad_booking_calendar', array($this, 'renderCalendar'), array('is_safe'=>array('html'))),
            new \Twig_SimpleFunction('slad_booking_day', array($this, 'renderDay'), array('is_safe'=>array('html')))
        );
    }

    /**
     * @param $items
     * @param $route 
     * @param  string                    $start
     * @param  int                       $months
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderCalendar($items, $route, $start = 'now', $months = 1)
    {
        if (intval($months) === 0) {
            throw new \InvalidArgumentException('Month number should be integer');
        }
        $now = new \DateTime($start);
        $end = new \DateTime();
        $end->add(new \DateInterval('P'.$months.'M'));

        foreach ($items as $item) {
            $bookings[$item->getId()] = $this->doctrine->getRepository($this->entity)
            ->createQueryBuilder('b')
            ->select('b')
            ->where('b.start >= :now')
            ->orWhere('b.start <= :end')
            ->orWhere('b.end >= :now')
            ->andWhere('b.item = :item')
            ->orderBy('b.start', 'ASC')
            ->setParameters(array(
                'now' => $now,
                'end' => $end,
                'item'=> $item->getId()
            ))
            ->getQuery()
            ->getResult();     
        }

        return $this->environment->render('SladBookingBundle:Calendar:month.html.twig', array(
            'route'     => $route,
            'bookings'  => $bookings,
            'items'     => $items,
            'start'     => $start,
            'months'    => $months
        ));
    }

    /**
     * @param $date
     * @param $item
     * @param $precision        
     */
    public function renderDay($date,$item,$precision)
    {

        $start  = new \DateTime($date);
        $end    = (new \DateTime($date))->modify('+1 day');

        $bookings = $this->doctrine->getRepository($this->entity)
        ->createQueryBuilder('b')
        ->select('b')
        ->where('b.start <= :start and b.end >= :end')
        ->orwhere('b.end >= :start and b.end <= :end')
        ->orwhere('b.start >= :start and b.start <= :end')
        ->andWhere('b.item = :item')
        ->orderBy('b.start', 'ASC')
        ->setParameters(array(
            'start'     => $start,
            'end'       => $end,
            'item'      => $item
        ))
        ->getQuery()
        ->getResult(); 

        return $this->environment->render('SladBookingBundle:Calendar:day.html.twig', array(
            'date'        => new \DateTime($date),
            'item'        => $item,
            'bookings'    => $bookings,
            'precision'   => $precision
        ));
    }
    
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getName()
    {
        return 'slad_booking_bundle_calendar';
    }
}
