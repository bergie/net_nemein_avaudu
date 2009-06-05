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
            
            // Save the settings
            $_MIDCOM->context->host->set_parameter('net_nemein_avaudu', 'qaiku_apikey', $data['qaiku_apikey']);
        }
        else
        {    
            $data['qaiku_apikey'] = $_MIDCOM->context->host->get_parameter('net_nemein_avaudu', 'qaiku_apikey');
        }
    }
}
?>