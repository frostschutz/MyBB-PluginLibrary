<?php
/**
 * This file is part of PluginLibrary for MyBB.
 * Copyright (C) 2010 Andreas Klauer <Andreas.Klauer@metamorpher.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

/* --- Plugin API: --- */

function pluginlibrary_info()
{
    return array(
        "name"          => "PluginLibrary",
        "description"   => "A collection of useful functions used by other plugins.",
        "website"       => "https://github.com/frostschutz/PluginLibrary",
        "author"        => "Andreas Klauer",
        "authorsite"    => "mailto:Andreas.Klauer@metamorpher.de",
        "version"       => "1",
        "guid"          => "",
        "compatibility" => "*"
        );
}

function pluginlibrary_is_installed()
{
    // Don't try this at home.
    return false;
}

function pluginlibrary_install()
{
    // Avoid unnecessary activation as a plugin with a friendly success message.
    flash_message("The selected plugin does not have to be activated.", 'success');
    admin_redirect("index.php?module=config-plugins");
}

function pluginlibrary_uninstall()
{
}

function pluginlibrary_activate()
{
}

function pluginlibrary_deactivate()
{
}

/* --- PluginLibrary class: --- */

class PluginLibrary
{
    /**
     * Version number.
     */
    public $version = 1;

    /**
     * Take care of inserting / updating settings.
     * Names and settings must be unique (i.e. use the google_seo_ prefix).
     *
     * @param string Internal group name.
     * @param string Group title that will be shown to the admin.
     * @param string Group description that will show up in the group overview.
     * @param array The list of settings to be added to that group.
     */
    function google_seo_settings($name, $title, $description, $list)
    {
        global $db;

        $query = $db->query("SELECT MAX(disporder) as disporder
                             FROM ".TABLE_PREFIX."settinggroups");
        $row = $db->fetch_array($query);

        $group = array('name' => $name,
                       'title' => $db->escape_string($title),
                       'description' => $db->escape_string($description),
                       'disporder' => $row['disporder']+1);

        if(defined("GOOGLESEO_GENERATE_LANG"))
        {
            echo htmlspecialchars("\$l['setting_group_{$group['name']}'] = \"".addcslashes($title, '\"$')."\";", ENT_COMPAT, "UTF-8")."<br>";
            echo htmlspecialchars("\$l['setting_group_{$group['name']}_desc'] = \"".addcslashes($description, '\"$')."\";", ENT_COMPAT, "UTF-8")."<br>";
        }

        // Create settings group if it does not exist.
        $query = $db->query("SELECT gid
                             FROM ".TABLE_PREFIX."settinggroups
                             WHERE name='$name'");

        if($row = $db->fetch_array($query))
        {
            // It exists, get the gid.
            $gid = $row['gid'];

            // Update title and description.
            $db->update_query("settinggroups",
                              $group,
                              "gid='$gid'");
        }

        else
        {
            // It does not exist, create it and get the gid.
            $db->insert_query("settinggroups",
                              $group);

            $gid = $db->insert_id();
        }

        // Deprecate all the old entries.
        $db->update_query("settings",
                          array("description" => "DELETEMARKER"),
                          "gid='$gid'");

        // Create and/or update settings.
        foreach($list as $key => $value)
        {
            if(defined("GOOGLESEO_GENERATE_LANG"))
            {
                echo htmlspecialchars("\$l['setting_{$key}'] = \"".addcslashes($value['title'], '\"$')."\";", ENT_COMPAT, "UTF-8")."<br>";
                echo htmlspecialchars("\$l['setting_{$key}_desc'] = \"".addcslashes($value['description'], '\"$')."\";", ENT_COMPAT, "UTF-8")."<br>";
            }

            // Set default values for value:
            $value = array_map(array($db, 'escape_string'), $value);

            $disporder += 1;

            $value = array_merge(
                array('optionscode' => 'yesno',
                      'value' => '0',
                      'disporder' => $disporder),
                $value);

            $value['name'] = "$key";
            $value['gid'] = $gid;

            $query = $db->query("SELECT sid FROM ".TABLE_PREFIX."settings
                                 WHERE gid='$gid'
                                 AND name='{$value['name']}'");

            if($row = $db->fetch_array($query))
            {
                // It exists, update it, but keep value intact.
                unset($value['value']);
                $db->update_query("settings",
                                  $value,
                                  "gid='$gid' AND name='{$value['name']}'");
            }

            else
            {
                // It doesn't exist, create it.
                $db->insert_query("settings", $value);
            }
        }

        // Delete deprecated entries.
        $db->delete_query("settings",
                          "gid='$gid' AND description='DELETEMARKER'");

        // Rebuild the settings file.
        rebuild_settings();
    }
}

global $PL;
$PL = new PluginLibrary();

/* --- End of file. --- */
?>
