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

namespace OjstrMessage\MessageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

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
class OjstrMessageMessageBundle extends Bundle
{
    /**
     *
     * @access public
     */
    public function boot()
    {
        $twig = $this->container->get('twig');

        $twig->addGlobal(
            'ojstr_message_message',
            array(
                'seo' => array(
                    'title_length' => $this->container->getParameter('ojstr_message_message.seo.title_length'),
                ),
                'folder' => array(
                    'show' => array(
                        'layout_template' => $this->container->getParameter('ojstr_message_message.folder.show.layout_template'),
                        'subject_truncate' => $this->container->getParameter('ojstr_message_message.folder.show.subject_truncate'),
                        'sent_datetime_format' => $this->container->getParameter('ojstr_message_message.folder.show.sent_datetime_format'),
                    ),
                ),
                'message' => array(
                    'show' => array(
                        'layout_template' => $this->container->getParameter('ojstr_message_message.message.show.layout_template'),
                        'sent_datetime_format' => $this->container->getParameter('ojstr_message_message.message.show.sent_datetime_format'),
                    ),
                    'compose' => array(
                        'layout_template' => $this->container->getParameter('ojstr_message_message.message.compose.layout_template'),
                        'form_theme' => $this->container->getParameter('ojstr_message_message.message.compose.form_theme'),
                    ),
                ),
            )
        ); // End Twig Globals.
    }
}
