<?php
/**
 * @package net_nemein_avaudu
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

// Open connection
$midgard = new midgard_connection();
if (!$midgard->open('midgard'))
{
    die('Could not connect to Midgard database');
}

// Instantiate synchronizer
$sync = new net_nemein_avaudu_sync();

// Run synchronization in a loop that is initialized by App Builder instance launch
while(true)
{
    $sync->fetch_qaiku();

    sleep(60);
}
?>