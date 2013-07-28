VisibilityBehavior
====================

[![Build Status](https://secure.travis-ci.org/cedriclombardot/VisibilityBehavior.png)](http://travis-ci.org/cedriclombardot/VisibilityBehavior)

The **VisibilityBehavior** behavior allows you to add visibility column per field and manage visible datas per user role.


Installation
------------

Cherry-pick the `VisibilityBehavior.php` file is `src/`, put it somewhere,
then add the following line to your `propel.ini` or `build.properties` configuration file:

``` ini
propel.behavior.visibility.class = path.to.VisibilityBehavior
```


Usage
-----

Just add the following XML tag in your `schema.xml` file:

``` xml
<behavior name="visibility">
    <parameter name="visibilities" value="me, friends, all" />
    <parameter name="default_visibility" value="me" />
    <!-- Hiearchy of visibilities -->
    <parameter name="hierarchy" value="when it's visible for friends it's visible for me" />
    <parameter name="hierarchy" value="when it's visible for all it's visible for friends" />
    <parameter name="hierarchy" value="when it's visible for all it's visible for me" />
    <!-- Choose columns to apply -->
    <parameter name="apply_to" value="my_field, my_other_field" />

    <!-- Optional parameters -->
    <parameter name="with_description" value="true" />
</behavior>
```

The **visibility** behavior requires four parameters to work:

* `visibilities`: a finite set of visibilities as comma separated values;
* `default_visibility`: the initial state, part of set of visibilities;
* `hierarchy`: a set of hierarchies. As you can see, you can add as many `hierarchy` parameters as you want.
* `apply_to`: the list of column to apply the visibility behavior

Each hierarchy has to follow this pattern:

    when it's visible for VISIBILITY_1 it's visible for VISIBILITY_2