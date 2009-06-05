<?php
/**
 * @package net_nemein_avaudu
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Avaudu settings
 *
 * @package net_nemein_avaudu
 */
class net_nemein_avaudu_controllers_settings
{
    public function __construct($instance)
    {
        $this->configuration = $instance->configuration;
    }

    public function action_edit($route_id, &$data, $args)
    {
        // Read current settings from host
        $data['qaiku_apikey'] = $_MIDCOM->context->host->get_parameter('net_nemein_avaudu', 'qaiku_apikey');

        // Update things as we go
        if (isset($_POST['qaiku_apikey']))
        {
            // Test the API key
            $qaiku_json = @file_get_contents('http://www.qaiku.com/api/statuses/user_timeline.json?apikey=' . $_POST['qaiku_apikey']);
            if (!$qaiku_json)
            {
                // Qaiku didn't accept the API key
                // TODO: UImessage
                throw new Exception("Invalid Qaiku API key");
            }
            
            $data['qaiku_apikey'] = $_POST['qaiku_apikey'];
            
            // Get user info from Qaiku
            $qaikus = json_decode($qaiku_json);
            if (isset($qaikus[0]))
            {
                $qaiku_user = $qaikus[0]->user;
                
                $qb = new midgard_query_builder('net_nemein_avaudu_contact');
                $qb->add_constraint('qaikunick', '=', $qaiku_user->screen_name);
                $contacts = $qb->execute();
                if (count($contacts) > 0)
                {
                    $_MIDCOM->context->host->set_parameter('net_nemein_avaudu', 'user', $contacts[0]->guid);
                }
                else
                {
                    // "Me" isn't in database yet, create
                    $contact = new net_nemein_avaudu_contact();
                    $contact->qaikunick = $qaiku_user->screen_name;
                    $contact->name = $qaiku_user->name;
                    $contact->avatar = $qaiku_user->profile_image_url;
                    $contact->qaikuid = $qaiku_user->id;
                    $contact->create();
                    $_MIDCOM->context->host->set_parameter('net_nemein_avaudu', 'user', $contact->guid);
                }
            }
            
            // Save the settings
            $_MIDCOM->context->host->set_parameter('net_nemein_avaudu', 'qaiku_apikey', $data['qaiku_apikey']);
        }

        // Run sync here. Ugly but necessary until we have php-cli in the bundle        
        $sync = new net_nemein_avaudu_bin_sync();
        $sync->fetch_qaiku();
    }
}
?>