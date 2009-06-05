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
    public function __construct($instance)
    {
        $this->configuration = $instance->configuration;
    }

    public function action_update($route_id, &$data, $args)
    {
        if (   !isset($_POST['status'])
            || empty($_POST['status']))
        {
            throw new midcom_exception_httperror("Missing status message", 400);
        }

        $message = new net_nemein_avaudu_message();
        $message->text = $_POST['status'];
        $message->language = $this->user->primarylanguage;        
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
        
        $message->create();

        $data[] = net_nemein_avaudu_controllers_timeline::message2status($message);

        // TODO: Do via variants instead
        header('Content-type: application/json');
        echo json_encode($data);
        die();
    }
}
?>