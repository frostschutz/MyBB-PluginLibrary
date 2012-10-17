<?php
/**
 * This file is part of PluginLibrary for MyBB.
 * Copyright (C) 2011 Andreas Klauer <Andreas.Klauer@metamorpher.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

/**
 * DEFINE PLUGINLIBRARY
 *
 *   Define the path to the plugin library, if it isn't defined yet.
 */
if(!defined("PLUGINLIBRARY"))
{
    define("PLUGINLIBRARY", MYBB_ROOT."inc/plugins/pluginlibrary.php");
}

$plugins->add_hook("pre_output_page", "hello_pl_world");
$plugins->add_hook("admin_config_plugins_begin", "hello_pl_edit");

function hello_pl_info()
{
    global $mybb, $plugins_cache;

    $info = array(
        "name"          => "Hello PluginLibrary!",
        "description"   => "A sample plugin for developers that demonstrates the features of the PluginLibrary.",
        "website"       => "http://mods.mybb.com/view/pluginlibrary",
        "author"        => "Andreas Klauer",
        "authorsite"    => "mailto:Andreas.Klauer@metamorpher.de",
        "version"       => "hello_pl.php",
        "guid"          => "",
        "compatibility" => "16*"
        );

    // Display some extra information when installed and active.
    if(hello_pl_is_installed() && $plugins_cache['active']['hello_pl'])
    {
        global $PL;
        $PL or require_once PLUGINLIBRARY;

        /**
         * URL APPEND
         *
         * $PL->url_append($url, $params, $sep, $encode)
         *
         * Append parameters to an URL that may or may not have ?query.
         */

        $editurl = $PL->url_append("index.php?module=config-plugins",
                                   array("hello_pl" => "edit",
                                         "my_post_key" => $mybb->post_code));
        $undourl = $PL->url_append("index.php",
                                   array("module" => "config-plugins",
                                         "hello_pl" => "undo",
                                         "my_post_key" => $mybb->post_code));

        $editurl = "index.php?module=config-plugins&amp;hello_pl=edit&amp;my_post_key=".$mybb->post_code;
        $undourl = "index.php?module=config-plugins&amp;hello_pl=undo&amp;my_post_key=".$mybb->post_code;

        $info["description"] .= "<br /><a href=\"{$editurl}\">Make edits to hello_pl.php</a>.";
        $info["description"] .= "    | <a href=\"{$undourl}\">Undo edits to hello_pl.php</a>.";
    }

    return $info;
}

function hello_pl_is_installed()
{
    global $settings;

    // This plugin creates settings on install. Check if setting exists.
    // Another example would be $db->table_exists() for database tables.
    if(isset($settings['hello_pl_foobar']))
    {
        return true;
    }
}

function hello_pl_install()
{
    /**
     * DEPENDENCY CHECK
     *
     *   If PluginLibrary is missing but required by your plugin,
     *
     *   - use file_exists(PLUGINLIBRARY) to check if it's there
     *   - use flash_message() to give the user a friendly error message,
     *     preferably including a download link to the missing dependency.
     *   - use admin_redirect() to cancel the installation.
     */
    if(!file_exists(PLUGINLIBRARY))
    {
        flash_message("The selected plugin could not be installed because <a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing.", "error");
        admin_redirect("index.php?module=config-plugins");
    }

    /**
     * LOADING
     *
     *   PluginLibrary is not loaded automatically. Load it when required.
     *
     *   - use the global variable $PL to access PluginLibrary functions
     *   - if $PL is not set, use require to load the PluginLibrary
     */
    global $PL;
    $PL or require_once PLUGINLIBRARY;

    /**
     * VERSION CHECK
     *
     *   Only needed if you need at least a specific version of PluginLibrary.
     *
     *   - compare $PL->version to the version number you need.
     *   - same procedure as DEPENDENCY CHECK
     */
    if($PL->version < 11)
    {
        flash_message("The selected plugin could not be installed because <a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is too old.", "error");
        admin_redirect("index.php?module=config-plugins");
    }
}

function hello_pl_uninstall()
{
    global $PL;
    $PL or require_once PLUGINLIBRARY;

    /**
     * SETTINGS DELETE
     *
     *   $PL->settings_delete(name, greedy)
     *
     *   Delete one or more setting groups and their settings.
     */
    $PL->settings_delete("hello_pl"
                         // , true /* optional, multiple groups */
        );

    /**
     * TEMPLATES DELETE
     *
     *   $PL->templates_delete(prefix, greedy)
     *
     *   Delete one or more template groups and their templates.
     */
    $PL->templates_delete("hellopl"
                          // , true /* optional, multiple groups */
        );

    /**
     * STYLESHEET DELETE
     *
     *   $PL->stylesheet_delete(name, greedy)
     *
     *   Delete one or more stylesheets.
     */
    $PL->stylesheet_delete('hellopl', true);
}

function hello_pl_activate()
{
    global $PL, $mybb;
    $PL or require_once PLUGINLIBRARY;

    /**
     * SETTINGS
     *
     *   $PL->settings(name, title, description, list)
     *
     *   Create a setting group with any number of settings with $PL->settings()
     *   If the setting group already exists, the settings are updated properly.
     */
    $PL->settings("hello_pl", // group name and settings prefix
                  "Hello PluginLibrary!",
                  "Setting group for the Hello PluginLibrary sample plugin.",
                  array(
                      "foobar" => array(
                          "title" => "Foo Bar",
                          "description" => "The setting name depends on the prefix (hello_pl) and the key (foobar). The name of this setting is hello_pl_foobar.",
                          ),
                      "no" => array(
                          "title" => "Simple Yes/No setting",
                          "description" => "The default is no. The name of this setting is hello_pl_no.",
                          ),
                      "yes" => array(
                          "title" => "Yes/No setting",
                          "description" => "This one is set to yes. The name of this setting is hello_pl_yes.",
                          "value" => 1,
                          ),
                      "text" => array(
                          "title" => "Text setting",
                          "description" => "Give me a word. The name of this setting is hello_pl_text.",
                          "optionscode" => "text",
                          ),
                      "textarea" => array(
                          "title" => "Text area (hello_pl_textarea)",
                          "description" => "Multiple lines. The name of this setting is hello_pl_textarea.",
                          "optionscode" => "textarea",
                          "value" => "line1\nline2",
                          ),
                      )
                  // , true /* optional,  prints a language file */
        );

    /**
     * TEMPLATES
     *
     *   $PL->templates(prefix, title, list)
     *
     *   Create a template group with any number of templates with $PL->templates()
     *   If the template group already exists, the templates are updated properly.
     *   Templates edited by users will show up in MyBB's 'Find Updated Templates'
     *
     *   Note: prefix must not contain _ for templates.
     */
    $PL->templates("hellopl", // template prefix, must not contain _
                   "Hello Pluginlibrary!", // you can also use "<lang:your_language_variable>" here
                   array(
                       "" => "<p>This is the <b>hellopl</b> template...</p>",
                       "example" => "<p>...and this is the <i>hellopl_example</i> template.</p>",
                       "other" => "Another template using {\$hellopl} and {\$hellopl_example}.",
                       )
        );

    /**
     * STYLESHEET
     *
     *   $PL->stylesheet(name, styles, attachedto)
     *
     *   Create, update and activate a stylesheet. Prefix the name with
     *   something unique to your plugin to avoid collisions.
     */
    $css = "#pluginlibrary { background: #026CB1 url(images/thead_bg.gif); color: white; }";
    $PL->stylesheet('hellopl_test', $css);
    $PL->stylesheet('hellopl_sendpm',
                    array(
                        '#pluginlibrary' => array(
                            'text-align' => 'center',
                            'font-style' => 'italic',
                            'border' => 'dashed red 5px',
                            ),
                        ),
                    array('private.php' => 'send'));
}

function hello_pl_deactivate()
{
    global $PL;
    $PL or require_once PLUGINLIBRARY;

    /**
     * CACHE DELETE
     *
     *   $PL->cache_delete(name, greedy)
     *
     *   Delete one or more caches.
     */
    $PL->cache_delete("hello_pl"
                      // , true /* optional, multiple caches */
        );

    /**
     * STYLESHEET DEACTIVATE
     *
     *   $PL->stylesheet_deactivate(name, greedy)
     *
     *   Deactivate your stylesheet so your plugin's styles won't be loaded
     *   until your plugin is activated again.
     */
    $PL->stylesheet_deactivate('hellopl', true);
}

function hello_pl_edit()
{
    global $mybb;

    // Only perform edits if we were given the correct post key.
    if($mybb->input['my_post_key'] != $mybb->post_code)
    {
        return;
    }

    global $PL;
    $PL or require_once PLUGINLIBRARY;

    /**
     * EDIT CORE
     *
     *   $PL->edit_core(name, file, search, apply)
     *
     *   Make or update one or more changes to a core file.
     *   Edits the file directly or, lacking permissions, returns a string.
     */
    if($mybb->input['hello_pl'] == 'edit')
    {
        $result = $PL->edit_core("hello_pl", "inc/plugins/hello_pl.php",
                                 array('search' => array("\"name\"", "=>", "\"Hello PluginLibrary!\"", ","),
                                       'replace' => "\"name\"=>\"Hello EditCore!\","),
                                 true // optional, try to apply the change
                                 // , $debug // optional, obtain debug info about the edits
            );
    }

    else if($mybb->input['hello_pl'] == 'undo')
    {
        /**
         * UNDO EDIT CORE
         *
         *   $PL->edit_core(name, file)
         *
         *   If you want to undo your changes, leave out the search.
         *   This undoes your changes (updates your edits to change nothing).
         */
        $result = $PL->edit_core("hello_pl", "inc/plugins/hello_pl.php",
                                 array(), // leave search empty, i.e. no edits
                                 true // optional, try to apply the change
            );
    }

    else
    {
        // bad input parameter
        return;
    }

    if($result === true)
    {
        // redirect with success
        flash_message("The file inc/plugins/hello_pl.php was modified successfully.", "success");
        admin_redirect("index.php?module=config-plugins");
    }

    else
    {
        // redirect with failure (could offer the result string for download instead)
        flash_message("The file inc/plugins/hello_pl.php could not be edited. Are the CHMOD settings correct?", "error");
        admin_redirect("index.php?module=config-plugins");
    }
}

function hello_pl_world($page)
{
    global $templates;

    eval("\$example = \"".$templates->get("hellopl_example")."\";");

    $page = str_replace("<div id=\"content\">", "<div id=\"content\"><div id=\"pluginlibrary\">Hello PluginLibrary!<p>This is a sample PluginLibrary Plugin (which can be disabled!) that displays this message on all pages.</p>{$example}</div>", $page);
    return $page;
}

?>
