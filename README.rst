=================================
PluginLibrary BETA 0 for MyBB 1.6
=================================

----------------------------
Documentation for Developers
----------------------------

*PluginLibrary* is not a stand-alone plugin, but rather a library of
useful functions that can be used by plugins and plugin developers.
For example, it can help you manage settings of your plugin, or apply
edits to core files. The list of *PluginLibrary*'s features is
expected to grow over time and contributions are welcome.

*PluginLibrary* is Open Source Software, licensed under the
GNU Lesser General Public License, Version 3. This is the same
license as MyBB itself uses, so if you can use MyBB, you should
also be able to use *PluginLibrary*.

*PluginLibrary* Copyright (C) 2011 Andreas Klauer (Andreas.Klauer@metamorpher.de)

.. contents::

Function Reference
==================

Settings
########

settings
--------

**Description**:

  *void* **settings** (*string* $name, *string* $title, *string* $description, *array* $list, *bool* $makelang=false)

  This function creates a setting group and a list of settings.
  If the setting group already exists, the settings inside this group
  will be updated to their new names and descriptions, while keeping
  their custom values intact. Settings that no longer exist will be
  removed. Optionally, it also generates a language file for your settings.

**Parameters**:

  **name**
    The name of your plugin, which will also be used as prefix for your setting groups and settings.

  **title**
    The title of the setting group.

  **description**
    The description of the setting group.

  **list**
    An array of settings. Each key is a setting name (which will be prefixed by your plugin name),
    and each value is a setting array with a title, description, and (optionally) optionscode, value.

  **makelang** (optional)
    If set, instead of creating the setting group and settings, a language file will be printed,
    ready for inclusion in your plugin distribution.

**Return value**:

  This function does not have a return value.

**Example**::

  $PL->settings('plugin_name',
                'Group Title',
                'Group Description',
                array(
                    'no' => array(
                        'title' => 'Simple Yes/No Setting',
                        'description' => 'The default is no.',
                        ),
                    'yes' => array(
                        'title' => 'Simple Yes/No Setting',
                        'description' => 'This one is set to yes.',
                        'value' => 1,
                        ),
                    'text' => array(
                        'title' => 'Text setting',
                        'description' => 'Enter some text here.',
                        'optionscode' => 'text',
                        ),
                    'textarea' => array(
                        'title' => 'Text area setting',
                        'description' => 'Enter multiple lines of text.',
                        'optionscode' => 'textarea',
                        'value' => 'Default value for this setting.',
                        ),
                    )
      );

The above example will result in a setting group called *plugin_name*,
which contains four settings *plugin_name_no*, *plugin_name_yes*,
*plugin_name_text* and *plugin_name_textarea*.

delete_settings
---------------

**Description**:

  *void* **delete_settings** (*string* $name, *bool* $greedy=false)

  This function deletes one (or more) setting groups and settings.

**Parameters**:

  **name**
    The name of your plugin or setting group.

  **greedy** (optional)
    If set, delete all groups that start with *name*.
    Useful if your plugin has more than just one setting group.

**Return value**:

  This function does not have a return value.

**Example**::

  $PL->delete_settings('plugin_name');

The above example will delete the setting group *plugin_name* and all its settings.

Cache
#####

delete_cache
------------

**Description**:

  *void* **delete_cache** (*string* $name, *bool* $greedy=false)

  This function safely deletes one (or more) caches.

**Parameters**:

  **name**
    The name of your plugin or cache.

  **greedy** (optional)
    If set, delete all caches that start with *name*.
    Useful if your plugin uses several caches.

**Return value**:

  This function does not have a return value.

**Example**::

  $cache->update('plugin_name', $value);
  $value = $cache->read('plugin_name');
  $PL->delete_cache('plugin_name');

This example shows how to create/update/read a cache (built-in MyBB functionality), and how to delete a cache using *PluginLibrary*.

Corefile Edits
##############

edit_core
---------

**Description**:

  *mixed* **edit_core** (*string* $name, *string* $file, *array* $edits=array(), *bool* $apply=false)

  This function makes, updates, and undoes changes to PHP/JS/CSS files. Using search patterns,
  you can locate blocks of one or more code lines, and insert new code lines before/after,
  or replace them.

**Parameters**:

  **name**
    Name of your plugin or prefix. It will be used to identify your changes and to detect
    conflicts with edits made by other plugins.

  **file**
    Filename (path relative to MYBB_ROOT) of the file that should be edited.

  **edits** (optional)
    One or more arrays that describe edits that should be applied to the file.
    Each array may have several keys. Only *search* is mandatory. Previous
    edits will be undone and thus updated. If *edits* is omitted or empty,
    only the undo step will be performed.

    *search*
      The search pattern which is responsible for locating the code that should be modified.
      Detailed explanation on how search patterns work, see below.

    *before*
      Lines that should be inserted *before* the located code.

    *after*
      Lines that should be inserted *after* the located code.

    *replace*
      Lines that should *replace* the located code.

    *multi*
      If set, allow the search pattern to match more than once.
      By default, the edit has to be a unique match.

    *none*
      If set, allow the search pattern to not match at all.
      By default, the edit is mandatory to match.

    *matches* (debugging only)
      For debugging purposes, *edits* can be passed by reference, in which case
      an entry *matches* will be created, showing how often and in which lines
      a match was found.

  **apply** (optional)
    If set, try to apply the changes directly to the file (requires write permissions).

**Return value**:

  This function returns *false* if the edit could not be performed, *true* if
  the edit was already in place (no change) or applied successfully, or a
  *string* with the successfully edited file contents.

**Example**::

  $PL->function('example');

Description of the example.

Search Patterns
:::::::::::::::

Explain search patterns somehow.


function
--------

**Description**:

  *void* **function** (*type* $param)

  Description of the function.

**Parameters**:

  **param**
    Explanation of the param.

**Return value**:

  Explanation of the return value.

**Example**::

  $PL->function('example');

Description of the example.
