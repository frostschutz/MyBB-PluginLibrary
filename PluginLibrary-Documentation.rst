============================
 PluginLibrary 12 for MyBB 
============================

Documentation for Developers
============================

*PluginLibrary* is not a stand-alone plugin, but rather a library of
useful functions that can be used by plugins and plugin developers.
For example, it can help you manage settings of your plugin, or apply
edits to core files. The list of *PluginLibrary*'s features is
expected to grow over time and contributions are welcome.

*PluginLibrary* is Open Source Software, licensed under the
GNU Lesser General Public License, Version 3. This is the same
license as MyBB itself uses, so if you can use MyBB, you should
also be able to use *PluginLibrary*.

*PluginLibrary* is Copyright (C) 2011 Andreas Klauer (Andreas.Klauer@metamorpher.de)

.. contents::
  :backlinks: top

Installation
------------

  To install *PluginLibrary*, just upload *inc/plugins/pluginlibrary.php* to
  your *inc/plugins/* folder.

  For users, no other files are required and no further steps are necessary.

  Developers may also be interested in *inc/plugins/hello_pl.php*
  which is a sample plugin that demonstrates the features of *PluginLibrary*.

.. note::
  This documentation is intended for plugin developers.
  If you are not making your own plugins, you don't need to read this.

Integration into your Plugin
----------------------------

When integrating *PluginLibrary* into your plugin, you need to be aware of a
few things.

#. *PluginLibrary* may or may not be installed. If it's missing,
   you should display a friendly message when the admin tries to activate your plugin.
#. *PluginLibrary* may be installed, but not up to date. If you
   require at least a specific version of *PluginLibrary*, you should
   display a friendly message that it's too old.
#. *PluginLibrary* is not a plugin and is not loaded automatically.
   You have to load it before you can use it.

The following sections show how you can do all of this, while keeping
the code as simple as possible. There is also a sample plugin which
demonstrates this.

Define PLUGINLIBRARY
~~~~~~~~~~~~~~~~~~~~

Throughout your plugin, you will probably have to refer to the filename
of *PluginLibrary* several times. Defining the filename at the beginning
of your plugin will help keeping the code shorter later on.

::

  if(!defined("PLUGINLIBRARY"))
  {
      define("PLUGINLIBRARY", MYBB_ROOT."inc/plugins/pluginlibrary.php");
  }

Dependency Check
~~~~~~~~~~~~~~~~

If your plugin requires *PluginLibrary*, you should check whether it is
installed or not in the *install()* or *activate()* routine of your plugin,
and prevent the activation of your plugin if it's not present. Using the
built-in functions *flash_message()* and *admin_redirect()*, you can display
a friendly error message to the admin, preferably including a download link.

::

  if(!file_exists(PLUGINLIBRARY))
  {
      flash_message("PluginLibrary is missing.", "error");
      admin_redirect("index.php?module=config-plugins");
  }

Load PluginLibrary On Demand
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Since *PluginLibrary* is not required in every request, it is not loaded
automatically. Instead, you have to load it on demand whenever you want
to use it. This can be done with an additional line of code.

::

  global $PL;
  $PL or require_once PLUGINLIBRARY;

Version Check
~~~~~~~~~~~~~

If you require a specific version (for features added in a later
version of *PluginLibrary*), in addition to the Dependency Check,
you can also check the version number of *PluginLibrary*. The
following example checks that *PluginLibrary* is at least version 12.

::

  if($PL->version < 12)
  {
      flash_message("PluginLibrary is too old.", "error");
      admin_redirect("index.php?module=config-plugins");
  }

.. note::
  If you are unsure which version of *PluginLibrary* to depend on,
  use the latest version number that is current at the time you
  publish your plugin.

Function Reference
------------------

Settings
~~~~~~~~

settings()
++++++++++

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

settings_delete()
+++++++++++++++++

**Description**:

  *void* **settings_delete** (*string* $name, *bool* $greedy=false)

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

  $PL->settings_delete('plugin_name');

The above example will delete the setting group *plugin_name* and all its settings.

Templates
~~~~~~~~~

templates()
+++++++++++

**Description**:

  *void* **templates** (*string* $prefix, *string* $title, *array* $list)

  This function creates a template group and a list of templates.
  If the template group already exists, the templates belonging to
  that group will be updated. Templates edited by the user will
  show up in MyBB's *Find Updated Templates* feature. Templates
  that no longer exist will be removed.

**Parameters**:

  **prefix**
    The prefix used by templates in this group. Ideally related to
    your plugin name. Unlike setting groups, template prefix must
    not contain _. Make sure to use a unique prefix not used by
    anything else.

  **title**
    The title of your template group as it will show up in the
    template list. For translatable titles, you can use
    <lang:your_language_key> (make sure your plugin loads the
    language file in the Admin CP).

  **list**
    An array of templates. Each key is the template name (which
    will be prefixed unless it's an empty string), the value
    is the template code.

**Return Value**:

  This function does not have a return value.

**Example**::

  $PL->templates('myplugin',
                 'My Plugin',
                 array(
                     '' => 'This is the myplugin template.',
                     'example' => 'This is the myplugin_example template.',
                     )
      );

The above example creates a group which will show up as *My Plugin Templates*
in the Admin CP, and contain two templates *myplugin* and *myplugin_example*.


templates_delete()
++++++++++++++++++

**Description**:

  *void* **templates_delete** (*string* $prefix, *bool* $greedy=false)

  This function deletes one (or more) template groups and templates.

**Parameters**:

  **prefix**
    The prefix of your template group.

  **greedy** (optional)
    If set, delete all groups that start with your prefix.

**Example**::

  $PL->templates_delete('myplugin');

The above example deletes the template group *myplugin* as well as all templates
that belong to this group.

::

  $PL->templates_delete('myplugin', true);

The above example deletes the template group *myplugin* as well as the groups
*mypluginfoo* and *mypluginbar*, if they exist.

Stylesheets
~~~~~~~~~~~

stylesheet()
++++++++++++

**Description**:

  *void* **stylesheet** (*string* $name, *mixed* $styles, *mixed* $attachedto="")

  This function creates/updates/activates a stylesheet in the MyBB Master Style.
  It will be inherited by all themes, enabling the user to edit and revert
  like the official stylesheets.

**Parameters**:

  **name**
    The name of the stylesheet. Should be unique, e.g. myplugin_style.

  **styles**
    The styles for this stylesheet. This can either be a literal string,
    or an array structure of CSS [selector => [property => value]].

    ::

      array(
          "selector1" => array(
              "property1" => "value1",
              "property2" => "value2",
              ...
              ),
          "selector2" => array(
              ...
              ),
          ...
          )

  **attachedto** (optional)
    By default, the stylesheet is attached globally. You can attach
    to specific sites and actions. You can specify a literal attachto
    string using MyBB's "site1|site2?action,action|site3" format, or an array
    structure [site=>[action,action]].

    ::

      array(
          "site1" => 0, // all actions
          "site2" => "action",
          "site3" => array("action", "action", ...)
          )

**Example**::

  $PL->stylesheet('myplugin_red', 'body { border: solid red 8px; }');

The above example creates a stylesheet called myplugin_red which puts
a red border around the HTML body. The stylesheet is specified as a string.

Stylesheets can also be specified as arrays::

  $PL->stylesheet('myplugin_red', array('body' => array('border' => 'solid red 8px')));

This is equivalent to the previous example.

Use the optional attachedto parameter if you want to restrict styles
to specific sites::

  $PL->stylesheet('myplugin_sendpm',
                  array('td' => array('font-size' => '2em')),
                  array('private.php' => 'send'));

The above example creates a stylesheet which will only be used on private.php?action=send.

stylesheet_delete()
+++++++++++++++++++

**Description**

  *void* **stylesheet_delete** (*string* $name, *bool* $greedy=false)

  This function deletes one or more stylesheets, including any edits
  the user may have made.

**Parameters**

  **name**
    The name or prefix of the stylesheet to delete.

  **greedy** (optional)
    If set, it also deletes all stylesheets starting with name\_.

**Example**::

  $PL->stylesheet_delete('myplugin_red');

The above example deletes the myplugin_red.css stylesheet.

::

  $PL->stylesheet_delete('myplugin', true);

The above example deletes myplugin.css and myplugin_*.css. Useful if your plugin creates multiple stylesheets.

stylesheet_deactivate()
+++++++++++++++++++++++

**Description**

  *void* **stylesheet_deactivate** (*string* $name, *bool* $greedy=false)

  This function deactivates one or more stylesheets. Allows styles to be
  deactivated along with your plugin without losing any user edits.

**Parameters**

  **name**
    The name or prefix of the stylesheet to delete.

  **greedy** (optional)
    If set, it also deactivates all stylesheets starting with name\_.

**Example**::

  $PL->stylesheet_deactivate('myplugin_red');

The above example deactivates the myplugin_red.css stylesheet.

::

  $PL->stylesheet_deactivate('myplugin', true);

The above example deactivates all stylesheets of your plugin.

Cache
~~~~~

cache_read()
++++++++++++

**Description**:

  *mixed* **cache_read** (*string* $name)

  This function reads an on-demand cache and returns its value (if present).
  Note that on-demand cache is allowed to vanish any time.

**Parameters**:

  **name**
    The name of the cache.

**Return value**:

  Returns the contents that were previously stored, or false.

**Example**::

  $cache = $PL->cache_read('my_plugin_cache');

  if($cache)
  {
      echo $cache;
  }

Reads and prints the contents of the previous cache, if present.

cache_update()
++++++++++++++

**Description**:

  *bool* **cache_update** (*string* $name, *mixed* $contents)

  This function creates or updates an on-demand cache with contents.
  Unlike MyBB's built-in $cache, it does not use the database nor
  does it load the cache automatically. Instead it uses a more
  specialized cache handler (by default: disk) directly, and you
  have to load the cache on demand using $PL->cache_read().

**Parameters**:

  **name**
    The name of the cache. Don't use special characters.

  **contents**
    The contents of the cache.

**Return value**:

  Returns true on success and false on failure.

**Example**::

  $PL->update_cache('my_plugin_cache', $somedata);

This example stores $somedata in a cache called my_plugin_cache.
This cache will not be loaded automatically, but has to be loaded
on demand using $PL->cache_read().

cache_delete()
++++++++++++++

**Description**:

  *void* **cache_delete** (*string* $name, *bool* $greedy=false)

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
  $PL->cache_delete('plugin_name');

This example shows how to create/update/read a cache (built-in MyBB
functionality), and how to delete a cache using *PluginLibrary*.

Corefile Edits
~~~~~~~~~~~~~~

edit_core()
+++++++++++

**Description**:

  *mixed* **edit_core** (*string* $name, *string* $file, *array* $edits=array(), *bool* $apply=false, *array* &$debug=null)

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
      String or array of lines that should be inserted *before* the located code.

    *after*
      String or array of lines that should be inserted *after* the located code.

    *replace*
      String or array of lines that should *replace* the located code,
      or empty string or any true value if it should be removed (*replace* with nothing).

    *multi*
      If set, allow the search pattern to match more than once.
      By default, the edit has to be a unique match.

    *none*
      If set, allow the search pattern to not match at all.
      By default, the edit is mandatory to match.

    *matches* (debugging only, see below)
      For debugging purposes, *edits* can be passed by reference, in which case
      an entry *matches* will be created, showing how often and in which lines
      a match was found.

    *error* (debugging only, see below)
      Verbatim error message that explains why this edit failed

  **apply** (optional)
    If set, try to apply the changes directly to the file (requires write permissions).

  **debug** (optional)
    If you want to obtain debug info about the edit, pass a variable here.
    It will be filled with the modified **edits** array that has the debug
    info included.

    Before PluginLibrary 2, you could pass the $edits parameter as reference
    directly, however call_time_pass_reference is deprecated in PHP 5.3.

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

The values *before*, *after*, *replace* can be given as either strings
or arrays of strings.

::

  array('before' => "line1\nline2\nline3")

is equivalent to

::

  array('before' =>
      array(
          "line1",
          "line2",
          "line3",
      )
  )

Search Patterns
```````````````

A search pattern is an array of strings. A single string may also be used
instead of an array with just one element. The strings do not have special
characters, instead they are matched literally and case-sensitive.
For a pattern to match, each string has to match in the order
of the array, however there may be any amount of characters between
strings. A search pattern always finds the smallest possible match.

In other words, the following search pattern::

  array('foo', 'bar', 'baz')

Would be roughly equivalent to this regular expression::

  foo.*bar.*baz

Here's how the above search pattern would match the following text:

  | foo bar foo bar
  | bar **foo** and **bar** foo
  | and finally **baz**
  | followed by more baz bar foo.

Note how the first occurence of foo and bar in the first line
is ignored, as is baz in the last line. Instead, it finds the
smallest possible match in lines between.

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
`````````

If an edit does not work (correctly) and you want to find out why, you can
get some debug information by passing the optional debug parameter::

    $edits = array('search' => 'echo "Hello world!";',
                   'replace' => 'echo "Hello PluginLibrary!";');
    $PL->edit_core('plugin_name', 'hello.php', $edits, false, $debug);
    print_r($debug);

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
~~~~~~~~~~~~~~~~~~~~~~

is_member()
+++++++++++

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
~~~~~~~~~~~~~~~~

url_append()
++++++++++++

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

xml_export()
++++++++++++

**Description**:

  *string* **xml_export** (*mixed* $data, *string* $filename=false, *string* $comment='MyBB PluginLibrary XML-Export :: {time}', *string* $endcomment='End of file.')

  Export arbitrary data as XML, so it can be shared with other users.

**Parameters**:

  **data**
    The data that should be exported. Allowed types are bool, int, float,
    string and array. Arrays may be multi-dimensional, but they should
    not contain any other types (object instances won't be exported),
    and the array structure must not be self-referencing / recursive.

  **filename** (optional)
    If given, the exported XML will be offered as download instead of
    returning the XML string. For this to work, headers must not be
    already sent.

  **comment** (optional)
    By default the exported XML contains a short comment explaining
    what the file is and when it was created. A custom comment can
    be set here, or if the value is false, the comment can be omitted
    entirely. In the given string, {time} will be replaced with the
    current time and date.

  **endcomment** (optional)
    Another comment that is placed at the end of the file.

**Return value**:

  Unless the filename parameter was given, the data is returned as
  XML string.

**Example**::

  $PL->xml_export($some_data, 'some_data.xml');

Lets the user download $some_data as some_data.xml file.

xml_import()
++++++++++++

**Description**:

  *mixed* **xml_import** (*string* $xml, *array* &$error=null)

  Import data that was previously exported with xml_export().

.. note::
  This really only works with XML files created by xml_export()
  and nothing else. If you're looking for a generic XML parser,
  try MyBB's *inc/class_xml.php* or PHP's XML Parser.

**Parameters**:

  **xml**
    The string (or file contents) of the xml_export() data.

  **error** (optional)
    If you want debug info about what went wrong when parsing the XML data,
    pass a variable here. It will be filled with an array that contains
    line number, code in that line, XML Parser error code, and XML Parser
    error message elements.

**Return value**:

  On success, whatever data was exported in the XML, will be returned.

**Example**::

  $xml = file_get_contents('some_data.xml');
  $some_data = $PL->xml_import($xml);

Loads $some_data from a file called some_data.xml.

.. Template for additional functions:
..
.. function
.. ++++++++
..
.. **Description**:
..
..   *void* **function** (*type* $param)
..
..   Description of the function.
..
.. **Parameters**:
..
..   **param**
..     Explanation of the param.
..
.. **Return value**:
..
..   Explanation of the return value.
..
.. **Example**::
..
..   $PL->function('example');
..
.. Description of the example.

Sample Plugin
-------------

If you prefer code over documentation, here is a sample plugin file which
demonstrates most of *PluginLibrary*'s features. This file is also
included as *inc/plugins/hello_pl.php* in the *PluginLibrary* package.

.. include:: inc/plugins/hello_pl.php
  :literal:
