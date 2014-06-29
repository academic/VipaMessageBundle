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

namespace OjstrMessage\MessageBundle\Form\Handler\User;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface ;

use OjstrMessage\MessageBundle\Form\Handler\BaseFormHandler;
use OjstrMessage\MessageBundle\Model\FrontModel\ModelInterface;
use OjstrMessage\MessageBundle\Entity\Message;

use OjstrMessage\MessageBundle\Component\Server\MessageServer;
use OjstrMessage\MessageBundle\Component\FloodControl;
use OjstrMessage\MessageBundle\Component\Dispatcher\MessageEvents;
use OjstrMessage\MessageBundle\Component\Dispatcher\Event\UserMessageFloodEvent;

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
class MessageReplyFormHandler extends BaseFormHandler
{
    /**
     *
     * @access protected
     * @var \OjstrMessage\MessageBundle\Form\Type\MessageFormType $messageFormType
     */
    protected $messageFormType;

    /**
     *
     * @access protected
     * @var \OjstrMessage\MessageBundle\Model\FrontModel\ModelInterface $model
     */
    protected $model;

    /**
     *
     * @access protected
     * @var \Symfony\Component\Security\Core\User\UserInterface $sender
     */
    protected $sender;

    /**
     *
     * @access protected
     * @var \Symfony\Component\Security\Core\User\UserInterface $recipient
     */
    protected $recipient;

    /**
     *
     * @access protected
     * @var \OjstrMessage\MessageBundle\Entity\Message $regardingMessage
     */
    protected $regardingMessage;

    /**
     *
     * @access protected
     * @var \OjstrMessage\MessageBundle\Component\FloodControl $floodControl
     */
    protected $floodControl;

    /**
     *
     * @access protected
     * @var \OjstrMessage\MessageBundle\Component\Server\MessageServer $messageServer
     */
    protected $messageServer;

    /**
     *
     * @access public
     * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \Symfony\Component\Form\FormFactory                        $factory
     * @param \OjstrMessage\MessageBundle\Form\Type\MessageFormType       $messageFormType
     * @param \OjstrMessage\MessageBundle\Model\FrontModel\ModelInterface $model
     * @param |OjstrMessage\MessageBundle\Component\FloodControl          $floodControl
     * @param \OjstrMessage\MessageBundle\Component\Server\MessageServer  $messageServer
     */
    public function __construct(EventDispatcherInterface  $dispatcher, FormFactory $factory, $messageFormType, ModelInterface $model, FloodControl $floodControl, MessageServer $messageServer)
    {
        $this->dispatcher = $dispatcher;
        $this->factory = $factory;
        $this->messageFormType = $messageFormType;
        $this->model = $model;
        $this->floodControl = $floodControl;
        $this->messageServer = $messageServer;
    }

    /**
     *
     * @access public
     * @param  \Symfony\Component\Security\Core\User\UserInterface        $sender
     * @return \OjstrMessage\MessageBundle\Form\Handler\MessageFormHandler
     */
    public function setSender(UserInterface $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     *
     * @access public
     * @param  \Symfony\Component\Security\Core\User\UserInterface        $sender
     * @return \OjstrMessage\MessageBundle\Form\Handler\MessageFormHandler
     */
    public function setRecipient(UserInterface $recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     *
     * @access public
     * @param  \OjstrMessage\MessageBundle\Entity\Message                  $regardingMessage
     * @return \OjstrMessage\MessageBundle\Form\Handler\MessageFormHandler
     */
    public function setInResponseToMessage(Message $regardingMessage)
    {
        $this->regardingMessage = $regardingMessage;

        return $this;
    }

    /**
     *
     * @access public
     * @return bool
     */
    public function process()
    {
        $this->getForm();

        if ($this->floodControl->isFlooded()) {
            $this->dispatcher->dispatch(MessageEvents::USER_MESSAGE_CREATE_REPLY_FLOODED, new UserMessageFloodEvent($this->request));

            return false;
        }

        $this->floodControl->incrementCounter();

        if ($this->request->getMethod() == 'POST') {
            $this->form->bind($this->request);

            if ($this->form->isValid()) {
                $message = $this->form->getData();
                $message->setSentFromUser($this->sender);
                $message->setCreatedDate(new \DateTime());
                $isFlagged = $this->form->get('is_flagged')->getData();

                if ($this->getSubmitAction($this->request) == 'send') {
                    return $this->onSuccess($message, $isFlagged);
                }
            }
        }

        return false;
    }

    /**
     *
     * @access protected
     * @return string
     */
    public function getQuotedSubject($subject)
    {
        if (strlen($subject) > 0) {
            $subject = 'Re: ' . $subject;
        } else {
            $subject = '';
        }

        return $subject;
    }

    /**
     *
     * @access protected
     * @return string
     */
    public function getQuotedBody($body, $sendTo)
    {
        if (strlen($body) > 0) {
            if (!strlen($sendTo) > 0) {
                $sendTo = 'Guest';
            }

            $message = '[QUOTE="' . $sendTo . '"]' . $body . '[/QUOTE]';
        } else {
            $message = '';
        }

        return $message;
    }

    /**
     *
     * @access public
     * @return Form
     */
    public function getForm()
    {
        if (null == $this->form) {
            $defaultValues = array();

            if (is_object($this->regardingMessage) && $this->regardingMessage instanceof Message) {
                if (is_object($this->recipient) && $this->recipient instanceof UserInterface) {
                    $defaultValues['send_to'] = $this->recipient->getUsername();
                } else {
                    if ($this->regardingMessage->getSentFromUser()) {
                        $defaultValues['send_to'] = $this->regardingMessage->getSentFromUser()->getUsername();
                    } else {
                        $defaultValues['send_to'] = '';
                    }
                }

                $defaultValues['subject'] = $this->getQuotedSubject($this->regardingMessage->getSubject());
                $defaultValues['body'] = $this->getQuotedBody($this->regardingMessage->getBody(), $defaultValues['send_to']);
            } else {
                if (is_object($this->recipient) && $this->recipient instanceof UserInterface) {
                    $defaultValues['send_to'] = $this->recipient->getUsername();
                }
            }

            $this->form = $this->factory->create($this->messageFormType, null, $defaultValues);
        }

        return $this->form;
    }

    /**
     *
     * @access protected
     * @param  \OjstrMessage\MessageBundle\Entity\Message                  $message
     * @return \OjstrMessage\MessageBundle\Model\FrontModel\ModelInterface
     */
    protected function onSuccess(Message $message, $isFlagged)
    {
        return $this->messageServer->sendMessage($this->request, $message, $isFlagged);
    }
}
