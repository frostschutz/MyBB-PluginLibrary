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
    The name of your plugin, which will also be used as prefix for your
    setting groups and settings.

  **title**
    The title of the setting group.

  **description**
    The description of the setting group.

  **list**
    An array of settings. Each key is a setting name (which will be
    prefixed by your plugin name), and each value is a setting array
    with a title, description, and (optionally) optionscode, value.

  **makelang** (optional)
    If set, instead of creating the setting group and settings, a language
    file will be printed, ready for inclusion in your plugin distribution.

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

This example shows how to create/update/read a cache (built-in MyBB
functionality), and how to delete a cache using *PluginLibrary*.

Corefile Edits
##############

edit_core
---------

**Description**:

  *mixed* **edit_core** (*string* $name, *string* $file, *array* $edits=array(), *bool* $apply=false)

  This function makes, updates, and undoes simple, line based changes to PHP/JS/CSS files.
  Using search patterns, it locates blocks of one or more lines of code, and inserts new code
  before or after them, or replaces them.

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

**Example**:

Assume you have an input file hello.php with these contents::

  <?php
  function hello_world()
  {
      echo "Hello world!";
  }
  ?>

If you want to change it to say "Hello PluginLibrary!" instead, you can edit it::

  $PL->edit_core('plugin_name', 'hello.php',
                 array('search' => 'echo "Hello world!";',
                       'replace' => 'echo "Hello PluginLibrary!";'),
                 true);

If the file could be written to, it should then look like this::

  <?php
  function hello_world()
  {
  /* - PL:plugin_name - /*     echo "Hello world!";
  /* + PL:plugin_name + */ echo "Hello PluginLibrary!";
  }
  ?>

Search Patterns
:::::::::::::::

A search pattern is an array of strings. A single string may also be used
instead of an array with just one element. The strings do not have special
characters, instead they are matched literally. For a pattern to match, each
string has to match in the order of the array, however there may be any
amount of characters between strings. A search pattern always finds the
smallest possible match.

In other words, the following search pattern::

  array('foo', 'bar', 'baz')

Would be roughly equivalent to this regular expression::

  foo.*bar.*baz

Here's how the above search pattern would match the following text:

  | foo bar foo bar
  | bar **foo** baz **bar** foo
  | and finally **baz**
  | followed by more foo bar.

Another example using the search pattern array('{', '}'):

  | function foobar($foo, $bar)
  | {
  |     if($foo > $bar)
  |     **{**
  |         foo($bar);
  |         bar($foo);
  |     **}**
  | }

Instead of matching the outer functions parentheses, it matches the inner
ones because that match is smaller. However, it does not matter how much
code there is between { } and what it looks like, and in most files there
are { } everywhere, so this match is not very useful.

When designing your pattern, you should make sure that all elements
you're matching are where you expect them to be, so you can achieve
a unique, concise match. A missing, but ambigous element, especially
at the beginning or end of the pattern, can cause the match to be a
much larger region than you intended. Going back to the first
example, if the **baz** you were looking for was missing, but if there
was another **baz** later on in the file, the match could also look
like this:

  | bar **foo** baz **bar** foo
  | ...a thousand lines that do not contain foo or baz...
  | and finally not the **baz** you were looking for

You have to choose your patterns carefully, as you would do with regular expressions.

Debugging
:::::::::

If an edit does not work (correctly) and you want to find out why, you can
get some debug information by passing the edits by reference::

    $edits = array('search' => 'echo "Hello world!";',
                   'replace' => 'echo "Hello PluginLibrary!";');
    $PL->edit_core('plugin_name', 'hello.php', &$edits);
    print_r($edits);

This will add a *matches* entry for each edit array, showing the byte positions
and actually matched patterns::

    [matches] => Array
        (
            [0] => Array
                (
                    [0] => 31
                    [1] => 56
                    [2] =>     echo "Hello world!";

                )

        )

This should help you determine why the edit failed; it may have matched
in the wrong place, more than once, or not at all.

Groups and Permissions
######################

is_member
---------

**Description**:

  *array* **is_member** (*mixed* $groups, *mixed* $user=false)

  This function checks if a user is member of one or more groups.
  Useful if your plugin has a setting to include/exclude one or more groups.

**Parameters**:

  **groups**
    The group(s) the user should be checked against. Can be
    a comma separated string of group IDs '1,2,3', or a number,
    or an array of numbers.

  **user** (optional)
    The user that should be checked for group memberships.
    By default, it's the current user. Alternatively, pass
    the UID or get_user() array of another user.

**Return value**:

  This function returns an array of the group IDs you were
  looking for and the user is actually a member of. If the
  user wasn't a member of any of the groups, the returned
  array will be empty.

**Example**::

  if($PL->is_member('3,4,6'))
  {
      show_secret_menu();
  }

This example checks whether the user is a super moderator, admin or moderator.

String functions
################

url_append
----------

**Description**:

  *string* **url_append** (*string* $url, *array* $params, *string* $sep='&amp;', *bool* $encode=true)

  Append one or more query parameters to an URL that may or may not
  have an existing ?query. The parameters will be encoded properly.

**Parameters**:

  **url**
    The URL that should be appended to. May also be a relative link.

  **params**
    Array of key => value pairs that should be appended to the URL.

  **sep** (optional)
    If the URL does not yet have any parameters, the first parameter will be separated by ?.
    The subsequent parameters will be separated with &amp; which is what you usually need
    for links that appear in HTML. You can pass a different separator (for example '&')
    here for plain text links.

  **encode** (optional)
    If values in URLs contain special characters, they have to be urlencoded properly.
    By default, this is done automatically for you. Set this to false if the values
    you are passing are already encoded properly, so they won't be encoded twice.

**Return value**:

  This function returns the new URL as a string.

**Example**::

  $PL->url_append('http://domain.tld/something', array('foo' => 'bar', 'bar' => 'foo'));

The result is '\http://domain.tld/something?foo=bar&amp;bar=foo'.

::

  $PL->url_append('showthread.php?tid=1', array('foo' => 'bar', 'bar' => 'foo'));

The result is 'showthread.php?tid=1&amp;foo=bar&amp;bar=foo'.

.. function
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

Sample Plugin
=============

If you prefer code over documentation, here is a sample plugin file which
demonstrates most of *PluginLibrary*'s features. This file is also
included as *inc/plugins/hello_pl.php* in the *PluginLibrary* package.

.. include:: inc/plugins/hello_pl.php
  :literal:

