<?php
/**
 * @package net_nemein_avaudu
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

require('markdown.php');

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
    static function message2status(net_nemein_avaudu_message $message, $add_user = true)
    {
        static $cached_messages = array();

        $status = array();
        $status['created_at'] = $message->metadata->published->format('c');
        $status['id'] = $message->guid;
        $status['text'] = $message->text;
        $status['text_html'] = Markdown($message->text);
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
            $status['user'] = net_nemein_avaudu_controllers_timeline::account2user($message->user, false);
        }
        
        return $status;
    }

    /**
     * Convert a Avaudu contact object to the format used by the API
     * @return array
     */
    static function account2user($user_id, $add_status = true)
    {
        static $cached_accounts = array();

        if (!$user_id)
        {
            return false;
        }

        if (isset($cached_accounts[$user_id]))
        {
            return $cached_accounts[$user_id];
        }
        
        $cached_accounts[$user_id] = array();
        $contact = new net_nemein_avaudu_contact($user_id);
        if (   !$contact
            || !$contact->guid)
        {
            return $cached_accounts[$user_id];
        }

        $cached_accounts[$user_id]['id'] = $contact->guid;
        $cached_accounts[$user_id]['name'] = "{$contact->name}";
        
        if ($contact->qaikunick)
        {
            $cached_accounts[$user_id]['screen_name'] = $contact->qaikunick;
        }
        else
        {
            $cached_accounts[$user_id]['screen_name'] = $contact->twitternick;
        }
        
        if (   !$contact->avatar
            || substr($contact->avatar, 0, 1) == '/')
        {
            $contact->avatar = '/midcom-static/net_nemein_avaudu/img/face-monkey.png';
        }
        $cached_accounts[$user_id]['profile_image_url'] = $contact->avatar;
        
        return $cached_accounts[$user_id];
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

        foreach ($messages as $message)
        {
            $data[] = net_nemein_avaudu_controllers_timeline::message2status($message);
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
            $data['statuses'][] = net_nemein_avaudu_controllers_timeline::message2status($message);
        }
        
        // TODO: New MidCOM does this way more gracefully
        header('Content-type: application/json');
        echo json_encode($data);
        die();
    }
}
?>