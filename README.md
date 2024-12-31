-----------

# KWCMS4 - kwcms_core

------------

Basic idea about KWCMS is framework with its own modular system. It contains basic tree in
which the application should exist on their own and without dependency on databases.

A many things has been ported from KWCMS1 and used here and many are brand new as necessity
calls.

The main thing about KWCMS framework is the orientation on file tree and controller tree.
I already had too many problems with usual frameworks which did not acknowledged these two
important parts of web life. Another thing is a hard way structured inputs and outputs.
Usually you get only output in format which results plaintext and input in PHP vars. Not here.
Here you get larger control over the input and output. Object way. This makes testing process
more simple. For both modules and libraries. Most of libraries are in separated projects where
they can be accessed as extra and contains simple tests. But the basics is here, in this project.

This is only example of work with KWCMS, it should be treated that way.

### So what is that

* vanilla php7.4, php8.1 (my virtual machines now)
* Tiny core, most things outside in modules and libraries
* Object-oriented code, precisely defined code tree ( \{author}{\project}\{module}\{paths\to\libraries} )
  simplifying autoloading
* Well-defined inputs and outputs
* Deep testing of libraries, possible for modules too
* Tree-oriented, filesystem-oriented data storage as default
* Simple for others to create own modules (just extend abstract module class)
* Little to no PSR, do things own way
* Yet libraries can be used under Composer
* Allow to have remote management in desktop app or another source, not just directly from webadmin

The core can be used for background as api to storage to your data, management itself with many ways
to access modules or presentation layer with all necessities for front-end.

### Setup

You just need kw_autoloader, kw_inputs and kw_modules. And then init and bootstrap. That's all. The
rest is just helping libraries and modules. That depends on your needs. 

Usual paths are __/modules__ for accessed modules, __/user__ for user data, __/web__ as primary dir
accessible for your webserver ans __/vendor__ for all external libraries, even for KWCMS core.

As file-oriented system these paths became important. Everything in _/vendor_ has path in format
_/{project_author}[/{project_name}]/{module_name}/[php-src|src]/Libraries/Path . This norm must
stand due limitations of _kw_autoloader_ which cannot find them otherwise. {project_author},
{project_name} and {module_name} is also part of namespace of each library. Similar rules are valid
too for things in _/modules_ . In fact they came true there first.

### What it can do

For single-page presentation there is same-named module which takes _index.htm_ from _/user_ dir
and parse it looking for other modules which can be added into the page as admin wants.

Management subsite begins with modules _/Router_ and _/Admin_ which represents translation of routed
paths into access to the backend. It has other dependencies.

And presentation path starts from _/Layout_ module and process into _/Page_ module which do the
similar thing like _SingleModule_ .

It also can run many subsites on one code - just with different bootstraps. And that also means
multiply code reusability and allowing manage them by just one administration. Yet it has different
users with different responsibilities on subsites as seen in this example.

### Kubernetes, AWS or similar services

KWCMS is based on files. It behaves like the older Unixes. For do the same thing like with other
distributed services or "serverless solutions" you need to set user directory as mountpoint somewhere
into NAS-like storage to behave like the usual external database. Then you need to set paths for
dynamic files there. That's all.

It is possible to run some parts of KWCMS on AWS or similar services, but still the main limit
here is need of file access. It's on your developer to choose which parts will be used and their
limits. Some parts like caches can stay on each node without problems, some need to be shared
across the all instances. Then some things might to be problematic - nearly all loaders are
filesystem-dependent.

KWCMS4 introduced usage of libraries *kw_files* which serves as interface to access files on
different storage engines. Many modules now works through it.

### Changes across the ages

- v1 -> initial one, really simple modules, can happen things with cross of admin and presentation
- v2 -> different way with objects, autoloading, different tree structure, playing with forms
- v3 -> total rewrite to changed autoloading, yet many things persists like hard-coded dependency
  on file system and paths as strings 
- v4 -> separation of controllers in modules, uses arrays as paths, user data are now available
  via appropriate libraries, direct access to user data need no more
