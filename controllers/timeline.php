<?php
/**
 * @package net_nemein_avaudu
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Avaudu timeline actions
 *
 * @package net_nemein_avaudu
 */
class net_nemein_avaudu_controllers_timeline
{
    public function __construct($instance)
    {
        $this->configuration = $instance->configuration;
    }

    /**
     * Convert a MgdSchema message object to the format our JSON handlers like
     *
     * @return array
     */
    public function message2status(net_nemein_avaudu_message $message, $add_user = true)
    {
        static $cached_messages = array();

        $status = array();
        $status['created_at'] = $message->metadata->published;
        $status['id'] = $message->guid;
        $status['text'] = $message->message;
        $status['source'] = $message->source;
        $status['lang'] = $message->language;

        $status['in_reply_to_status_id'] = '';
        if ($message->replyto)
        {
            if (!isset($cached_messages[$message->replyto]))
            {
                $cached_messages[$message->replyto] = new net_nemein_avaudu_message($message->replyto);
            }
            
            if (   $cached_messages[$message->replyto]
                && $cached_messages[$message->replyto]->guid)
            {
                $status['in_reply_to_status_id'] = $cached_messages[$message->replyto]->guid;
            }
        }
        
        if ($add_user)
        {
            $status['user'] = $this->account2user($message->metadata->creator, false);
        }
        
        return $status;
    }

    public function action_stream($route_id, &$data, $args)
    {
        // We do nothing here
    }

    public function action_stream_latest($route_id, &$data, $args)
    {
        $qb = new midgard_query_builder('net_nemein_avaudu_message');
        $qb->set_limit((int) $args['number']);
        $qb->add_order('metadata.published', 'DESC');
        $messages = $qb->execute();
        
        $data['statuses'] = array();
        foreach ($messages as $message)
        {
            $data['statuses'][] = $this->message2status($message);
        }
        
        // TODO: New MidCOM does this way more gracefully
        header('Content-type: application/json');
        echo json_encode($data);
        die();
    }

    public function action_stream_page($route_id, &$data, $args)
    {
        $qb = new midgard_query_builder('net_nemein_avaudu_message');
        $qb->set_limit((int) $this->configuration->get('messages_per_page'));
        $qb->set_offset((int) $args['page']);
        $qb->add_order('metadata.published', 'DESC');
        $messages = $qb->execute();
        
        $data['statuses'] = array();
        foreach ($messages as $message)
        {
            $data['statuses'][] = $this->message2status($message);
        }
        
        // TODO: New MidCOM does this way more gracefully
        header('Content-type: application/json');
        echo json_encode($data);
        die();
    }
}
?>