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

namespace OjstrMessage\MessageBundle\Model\Component\Manager;

use OjstrMessage\MessageBundle\Model\Component\Manager\ManagerInterface;
use OjstrMessage\MessageBundle\Model\Component\Manager\BaseManager;

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
class ThreadManager extends BaseManager implements ManagerInterface
{
    /**
     *
     * @access public
     * @return \OjstrMessage\MessageBundle\Entity\Thread
     */
    public function createThread()
    {
        return $this->gateway->createThread();
    }

}
