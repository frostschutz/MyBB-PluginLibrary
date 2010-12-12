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

/* --- End of file. --- */
?>
