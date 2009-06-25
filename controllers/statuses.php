<?php
/**
 * @package net_nemein_avaudu
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Avaudu status handling
 *
 * @package net_nemein_avaudu
 */
class net_nemein_avaudu_controllers_statuses
{
    public function __construct(midcom_core_component_interface $instance)
    {
        $this->configuration = $instance->configuration;
    }

    public function post_update($args)
    {
        if (   !isset($_POST['status'])
            || empty($_POST['status']))
        {
            throw new midcom_exception_httperror("Missing status message", 400);
        }

        $message = new net_nemein_avaudu_message();
        $message->text = $_POST['status'];
        if (isset($_POST['lang']))
        {
            $message->language = $_POST['lang'];
        }
        
        if (isset($_POST['in_reply_to_status_id']))
        {
            $parent = new net_nemein_avaudu_message($_POST['in_reply_to_status_id']);
            if (   !$parent
                || !$parent->guid)
            {
                throw new midcom_exception_notfound("Status message {$_POST['in_reply_to_status_id']} not found");
            }
            $message->replyto = $parent->id;
        }

        $user_guid = $_MIDCOM->context->host->get_parameter('net_nemein_avaudu', 'user');
        if (!$user_guid)
        {
            throw new midcom_exception_notfound("No user found, check your settings");
        }

        $user = new net_nemein_avaudu_contact($user_guid);
        if (!$user->guid)
        {
            // Invalid user setting
            $user_guid = $_MIDCOM->context->host->set_parameter('net_nemein_avaudu', 'user', '');
            throw new midcom_exception_notfound("No user found, check your settings");
        }
        $message->user = $user->id;
        
        $message->metadata->published->modify('now');
        $message->source = 'Avaudu';
                
        $message->create();

        $this->data[] = net_nemein_avaudu_controllers_timeline::message2status($message);
    }
}
?>