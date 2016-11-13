Feature: Test that WP-CLI `vccw/scaffold-vccw` loads.

  Scenario: Run `wp scaffold vccw` without option
    Given an empty directory

    When I run `wp help scaffold vccw`
    Then the return code should be 0
    And STDOUT should contain:
      """
      Generate a new VCCW environment.
      """

    When I run `wp scaffold vccw vccw.dev`
    Then the return code should be 0
    And the vccw.dev directory should exist
    And STDOUT should contain:
      """
      Success: Generated.
      """
    And the vccw.dev/provision/default.yml file should exist
    And the vccw.dev/site.yml file should exist
    And the vccw.dev/site.yml file should contain:
      """
      hostname: vccw.dev
      ip: 192.168.33.10
      """
    And the vccw.dev/site.yml file should contain:
      """
      lang: en_US
      """

  Scenario: Run `wp scaffold vccw` with option
    Given an empty directory

    When I run `wp scaffold vccw . --host=wp.dev --ip=192.123.123.123 --lang=ja`
    Then the return code should be 0
    And STDOUT should contain:
      """
      Success: Generated.
      """
    And the provision/default.yml file should exist
    And the site.yml file should exist
    And the site.yml file should contain:
      """
      hostname: wp.dev
      ip: 192.123.123.123
      """
    And the site.yml file should contain:
      """
      lang: ja
      """

    When I run `wp scaffold vccw . --lang=ja`
    Then the return code should be 0
    And STDOUT should contain:
      """
      Success: Generated.
      """
    And the provision/default.yml file should exist
    And the site.yml file should exist
    And the site.yml file should contain:
      """
      hostname: vccw.dev
      ip: 192.168.33.10
      """
    And the site.yml file should contain:
      """
      lang: ja
      """

    When I run `wp scaffold vccw wp1.dev --ip=192.123.123.123`
    Then the return code should be 0
    And STDOUT should contain:
      """
      Success: Generated.
      """
    And the wp1.dev/provision/default.yml file should exist
    And the wp1.dev/site.yml file should exist
    And the wp1.dev/site.yml file should contain:
      """
      hostname: vccw.dev
      ip: 192.123.123.123
      """
    And the wp1.dev/site.yml file should contain:
      """
      lang: en_US
      """

    When I run `wp scaffold vccw wp2.dev --host=wp.dev`
    Then the return code should be 0
    And STDOUT should contain:
      """
      Success: Generated.
      """
    And the wp2.dev/provision/default.yml file should exist
    And the wp2.dev/site.yml file should exist
    And the wp2.dev/site.yml file should contain:
      """
      hostname: wp.dev
      ip: 192.168.33.10
      """
    And the wp2.dev/site.yml file should contain:
      """
      lang: en_US
      """
