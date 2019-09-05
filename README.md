alwaysblank/blade-generate
================================



[![Build Status](https://travis-ci.org/alwaysblank/blade-generate.svg?branch=master)](https://travis-ci.org/alwaysblank/blade-generate)

Quick links: [Using](#using) | [Installing](#installing) | [Contributing](#contributing)

## Using

Before using this plugin, make sure you have installed and activated a theme built on [Sage 9](https://github.com/roots/sage). While in theory this plugin could be modified to work with other blade-based WordPress systems, it is currently *only* designed to work with Sage 9.

**Note:** Sage 10 will make heavy use of the [Acorn project](https://github.com/roots/acorn), which looks to provide this functionality internally (take a look at the `ViewCacheCommand.php` and `ViewClearCommand.php` files [here](https://github.com/roots/acorn/tree/master/src/Acorn/Console/Commands)). Consequently, I do not currently intend to update this plugin to work with Sage 10, unless the Acorn functionality that it would replicate is removed or proves otherwise problematic.

### Compiling

#### `compile`

The primary function of this plugin is to compile Blades on demand.

```
wp blade compile
```

This will iterate through all `.blade.php` files in your theme directory and generate compiled versions.

If you only want to compile some of your templates, you can specify a directory using the `directory` parameter:

```
wp blade compile --directory=path/to/files
```

The directory is relative to your theme root.

### Clearing

Sometimes you want to clear out your Blade cache! This plugin can help you do that too.

#### `clear`

```
wp blade clear
```

This will look at all your Blades and remove their cached versions. It will _only_ remove cached files for existing Blades; it will _not_ clear out everything in your Blade cache directory.

It takes the same `--directory` argument as `compile`, and will only remove cached files for those Blades in the passed directory. For instance, if you pass:

```
wp blade clear --directory=path/to/files
```

Then _only_ the cached versions of files in `path/to/files` will be deleted.

If you want to clear out your entire cache (i.e. you have removed a Blade template), then use `wipe`.

#### `wipe`

```
wp blade wipe
```

This function takes no arguments, and simply removes every file it finds in your Blade cache. It determines where the cache is by calling Sage's `App\config('view.compiled')`;

## Installing

Installing this package requires WP-CLI v1.1.0 or greater. Update to the latest stable release with `wp cli update`.

Currently WP-CLI is not accepting new package submissions, but you can install this package directly from github with the following command: `wp package install git@github.com:alwaysblank/blade-generate.git`.

## Contributing

We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.

### Reporting a bug

Think you’ve found a bug? We’d love for you to help us get it fixed.

Before you create a new issue, you should [search existing issues](https://github.com/alwaysblank/blade-generator/issues?q=label%3Abug%20) to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version.

Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue](https://github.com/alwayblank/blade-generator/issues/new) with the following:

1. What you were doing (e.g. "When I run `wp post list`").
2. What you saw (e.g. "I see a fatal about a class being undefined.").
3. What you expected to see (e.g. "I expected to see the list of posts.")

Include as much detail as you can, and clear steps to reproduce if possible.

### Creating a pull request

Want to contribute a new feature? Please first [open a new issue](https://github.com/alwaysblank/blade-generator/issues/new) to discuss whether the feature is a good fit for the project.

Once you've decided to commit the time to seeing your pull request through, please follow our guidelines for creating a pull request to make sure it's a pleasant experience:

1. Create a feature branch for each contribution.
2. Submit your pull request early for feedback.
3. Include functional tests with your changes. [Read the WP-CLI documentation](https://wp-cli.org/docs/pull-requests/#functional-tests) for an introduction.
4. Follow the [WordPress Coding Standards](http://make.wordpress.org/core/handbook/coding-standards/).

