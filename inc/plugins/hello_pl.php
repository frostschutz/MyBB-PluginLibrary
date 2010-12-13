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

function hello_pl_info()
{
    return array(
        "name"          => "Hello PluginLibrary!",
        "description"   => "A sample plugin for developers that demonstrates the features of the PluginLibrary.",
        "website"       => "https://github.com/frostschutz/PluginLibrary",
        "author"        => "Andreas Klauer",
        "authorsite"    => "mailto:Andreas.Klauer@metamorpher.de",
        "version"       => "hello_pl.php",
        "guid"          => "",
        "compatibility" => "*"
        );
}

function hello_pl_is_installed()
{
}

function hello_pl_install()
{
    /**
     * DEPENDENCY CHECK
     *
     *   If PluginLibrary is missing but required by your plugin,
     *
     *   - use flash_message() to give the user a friendly error message,
     *     preferably including a download link to the missing dependency.
     *   - use admin_redirect() to cancel the installation.
     *
     */
    if(!file_exists(MYBB_ROOT."inc/plugins/pluginlibrary.php"))
    {
        flash_message("The selected plugin could not be installed because <a href=\"https://github.com/frostschutz/PluginLibrary\">PluginLibrary</a> is missing.", "error");
        admin_redirect("index.php?module=config-plugins");
    }

    /**
     * LOADING
     *
     *   PluginLibrary is not loaded automatically. Load it when required.
     *
     *   - use require_once to load PluginLibrary
     *   - use the global variable $PL to access PluginLibrary functions
     *
     */
    require_once MYBB_ROOT."inc/plugins/pluginlibrary.php";
    global $PL;

    /**
     * VERSION CHECK
     *
     *   Only needed if you need at least a specific version of PluginLibrary.
     *
     *   - compare $PL->version to the version number you need.
     *   - same procedure as DEPENDENCY CHECK
     */
    if($PL->version < 1)
    {
        flash_message("The selected plugin could not be installed because <a href=\"https://github.com/frostschutz/PluginLibrary\">PluginLibrary</a> is too old.", "error");
        admin_redirect("index.php?module=config-plugins");
    }
}

function hello_pl_uninstall()
{
    require_once MYBB_ROOT."inc/plugins/pluginlibrary.php";
    global $PL;

    /**
     * DELETE SETTINGS
     *
     * $PL->delete_settings(name, greedy)
     *
     * Delete one or more setting groups and their settings.
     *
     */
    $PL->delete_settings("hello_pl"
                         // , true /* optional, multiple groups */
        );
}

function hello_pl_activate()
{
    require_once MYBB_ROOT."inc/plugins/pluginlibrary.php";
    global $PL;

    /**
     * SETTINGS
     *
     * $PL->settings(name, title, description, list)
     *
     * Create a setting group with any number of settings with $PL->settings()
     * If the setting group already exists, the settings are updated properly.
     *
     */
    $PL->settings("hello_pl",
                  "Hello PluginLibrary!",
                  "Setting group for the Hello PluginLibrary sample plugin.",
                  array(
                      "yesno_no" => array(
                          "title" => "Simple Yes/No setting",
                          "description" => "The default is no.",
                          ),
                      "yesno_yes" => array(
                          "title" => "Yes/No setting",
                          "description" => "This one is set to yes.",
                          "value" => 1,
                          ),
                      "text" => array(
                          "title" => "Text setting",
                          "description" => "Give me a word.",
                          "optionscode" => "text",
                          ),
                      "textarea" => array(
                          "title" => "Text area",
                          "description" => "Multiple lines.",
                          "optionscode" => "textarea",
                          "value" => "line1\nline2",
                          ),
                      )
                  // , true /* optional,  prints a language file */
        );
}

function hello_pl_deactivate()
{
}

?>
