<?php
/**
 * @package net_nemein_avaudu
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Avaudu synchronizer class
 *
 * @package net_nemein_avaudu
 */
class net_nemein_avaudu_bin_sync
{
    private $qaiku_apikey = '';
    public function __construct()
    {
        // Here we dig the settings from the database
        $qb = new midgard_query_builder('midgard_parameter');
        $qb->add_constraint('domain', '=', 'net_nemein_avaudu');
        $params = $qb->execute();
        foreach ($params as $param)
        {
            switch ($param->name)
            {
                case 'qaiku_apikey':
                    $this->qaiku_apikey = $param->value;
                    break;
            }
        }
    }

    private function import_contact($user, $source = 'qaiku')
    {
        static $cached_contacts = array();
        if (!isset($cached_contacts[$source]))
        {
            $cached_contacts[$source] = array();
        }
        if (isset($cached_contacts[$source][$user->screen_name]))
        {
            return $cached_contacts[$source][$user->screen_name];
        }
        
        $qb = new midgard_query_builder('net_nemein_avaudu_contact');
        $qb->add_constraint("{$source}nick", '=', $user->screen_name);
        $contacts = $qb->execute();
        if (count($contacts) > 0)
        {
            // We already know this contact
            $cached_contacts[$source][$user->screen_name] = $contacts[0]->id;
            return $cached_contacts[$source][$user->screen_name];
            // TODO: Update?
        }
        
        $contact = new net_nemein_avaudu_contact();
        $contact->name = $user->name;
        $contact->avatar = $user->profile_image_url;

        $nickfield = "{$source}nick";
        $contact->$nickfield = $user->screen_name;
        $idfield = "{$source}id";
        $contact->$idfield = $user->id;
        $contact->create();
        $cached_contacts[$source][$user->screen_name] = $contact->id;
        return $cached_contacts[$source][$user->screen_name];
    }

    public function fetch_qaiku()
    {
        if (!$this->qaiku_apikey)
        {
            return;
        }
        /*
        $qb = new midgard_query_builder('net_nemein_avaudu_message');
        $messages = $qb->execute();
        foreach ($messages as $message)
        {
            $message->delete();
        }
        die("here");
        */

        $qaiku_json = @file_get_contents('http://www.qaiku.com/api/statuses/friends_timeline.json?apikey=' . $this->qaiku_apikey);
        if (!$qaiku_json)
        {
            // TODO: Log
            return;
        }
        
        $qaikus = json_decode($qaiku_json);
        $i = 0;
        foreach ($qaikus as $qaiku)
        {
            $i++;
            if ($i > 10)
            {
                // Safety for Create/Update crashing
                break;
            }

            $qb = new midgard_query_builder('net_nemein_avaudu_message');
            $qb->add_constraint('qaikuid', '=', $qaiku->id);
            $messages = $qb->execute();
            if (count($messages) > 0)
            {
                // We already have this one
                continue;
                // TODO: Should we check for content updates?
            }
            else
            {
                $message = new net_nemein_avaudu_message();
            }
            $message->text = $qaiku->text;
            $message->qaikuid = $qaiku->id;
            $message->language = $qaiku->language;
            $message->source = $qaiku->source;
            
            $message->user = $this->import_contact($qaiku->user, 'qaiku');
            $message->metadata->published = gmdate('c', strtotime($qaiku->created_at));

            if ($qaiku->in_reply_to_status_id)
            {
                $qb = new midgard_query_builder('net_nemein_avaudu_message');
                $qb->add_constraint('qaikuid', '=', $qaiku->in_reply_to_status_id);
                $parents = $qb->execute();
                if (count($parents) > 0)
                {
                    $message->replyto = $parents[0]->id;
                }
            }

            if ($message->guid)
            {
                $message->update();
            }
            else
            {
                $message->create();
            }
        }
    }
    
    public function fetch_twitter()
    {
        // Do nothing for now.
    }
}
/*
// Open connection
$midgard = new midgard_connection();
if (!$midgard->open('midgard'))
{
    die('Could not connect to Midgard database');
}

// Instantiate synchronizer
$sync = new net_nemein_avaudu_bin_sync();

// Run synchronization in a loop that is initialized by App Builder instance launch
while(true)
{
    $sync->fetch_qaiku();

    sleep(60);
}
*/
?>