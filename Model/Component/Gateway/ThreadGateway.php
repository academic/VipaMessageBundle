<?php

/*
 * This file is part of the OjstrMessage MessageBundle
 *
 * (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OjstrMessage\MessageBundle\Model\Component\Gateway;

use Doctrine\ORM\QueryBuilder;

use OjstrMessage\MessageBundle\Model\Component\Gateway\GatewayInterface;
use OjstrMessage\MessageBundle\Model\Component\Gateway\BaseGateway;

use OjstrMessage\MessageBundle\Entity\Thread;

/**
 *
 * @category OjstrMessage
 * @package  MessageBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * 
 *
 */
class ThreadGateway extends BaseGateway implements GatewayInterface
{
    /**
     *
     * @access private
     * @var string $queryAlias
     */
    protected $queryAlias = 't';

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder                   $qb
     * @param  Array                                        $parameters
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findThread(QueryBuilder $qb = null, $parameters = null)
    {
        if (null == $qb) {
            $qb = $this->createSelectQuery();
        }

        return $this->one($qb, $parameters);
    }

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder                   $qb
     * @param  Array                                        $parameters
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findThreads(QueryBuilder $qb = null, $parameters = null)
    {
        if (null == $qb) {
            $qb = $this->createSelectQuery();
        }

        return $this->all($qb, $parameters);
    }

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder $qb
     * @param  Array                      $parameters
     * @return int
     */
    public function countThreads(QueryBuilder $qb = null, $parameters = null)
    {
        if (null == $qb) {
            $qb = $this->createCountQuery();
        }

        if (null == $parameters) {
            $parameters = array();
        }

        $qb->setParameters($parameters);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return 0;
        }
    }

    /**
     *
     * @access public
     * @param  \OjstrMessage\MessageBundle\Entity\Thread                            $thread
     * @return \OjstrMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function saveThread(Thread $thread)
    {
        $this->persist($thread)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \OjstrMessage\MessageBundle\Entity\Thread                            $thread
     * @return \OjstrMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function updateThread(Thread $thread)
    {
        $this->persist($thread)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \OjstrMessage\MessageBundle\Entity\Thread                            $thread
     * @return \OjstrMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function deleteThread(Thread $thread)
    {
        $this->remove($thread)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @return \OjstrMessage\MessageBundle\Entity\Thread
     */
    public function createThread()
    {
        return new $this->entityClass();
    }
}
