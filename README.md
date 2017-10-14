# vccw/scaffold-vccw

[![Build Status](https://travis-ci.org/vccw-team/scaffold-vccw.svg?branch=master)](https://travis-ci.org/vccw-team/scaffold-vccw)

This is a WP-CLI command that generates a [VCCW](http://vccw.cc/) envirionment.

```
$ wp scaffold vccw wordpress.dev --lang=ja
Generating:   100% [===========================] 0:03 / 0:06
Success: Generated.
```

## Install

Installing this package requires WP-CLI v0.23.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with:

```
$ wp package install vccw/scaffold-vccw:@stable
```

## Usage

```
NAME

  wp scaffold vccw

DESCRIPTION

  Generate a new VCCW environment.

SYNOPSIS

  wp scaffold vccw <directory> [--host=<hostname>] [--ip=<ip-address>]
  [--lang=<language>] [--update]

OPTIONS

  <directory>
    The directory of the new VCCW based guest machine.

  [--host=<hostname>]
    Hostname of the guest machine. Default is `vccw.test`.

  [--ip=<ip-address>]
    IP address of the guest machine. Default is `192.168.33.10`.

  [--lang=<language>]
    Language of the WordPress. Default is `en_US`.

  [--update]
    Update files of the VCCW to latest version.

EXAMPLES

    $ wp scaffold vccw wordpress.dev
    Generating:   100% [===========================] 0:03 / 0:06
    Success: Generated.

    $ wp scaffold vccw wordpress.dev --lang=ja
    Generating:   100% [===========================] 0:03 / 0:06
    Success: Generated.
```

## Customize your default site.yml

1. [Download default template from GitHub](https://raw.githubusercontent.com/vccw-team/scaffold-vccw/master/templates/site.yml.mustache).
2. Edit it.
3. Place it under the file name of `~/.wp-cli/vccw.yml.mustache`.
