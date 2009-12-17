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
        midgardmvc_core::get_instance()->authorization->require_user();
        $person = midgardmvc_core::get_instance()->authentication->get_person();
        $contact = new net_nemein_avaudu_contact($person->guid);

        // Read current settings from host
        $data['qaiku_apikey'] = $person->get_parameter('net_nemein_avaudu', 'qaiku_apikey');

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
                    $person->set_parameter('net_nemein_avaudu', 'user', $contacts[0]->guid);
                }
                else
                {
                    // Populate "Me" into database
                    $contact->qaikunick = $qaiku_user->screen_name;
                    $contact->name = $qaiku_user->name;
                    $contact->avatar = $qaiku_user->profile_image_url;
                    $contact->qaikuid = $qaiku_user->id;
                    $contact->update();
                }
            }
            
            // Save the settings
            $person->set_parameter('net_nemein_avaudu', 'qaiku_apikey', $data['qaiku_apikey']);
        }
    }
    
    public function action_sync()
    {     
        $sync = new net_nemein_avaudu_sync();
        $sync->fetch_qaiku();
    }
}
?>
