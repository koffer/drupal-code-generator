<?php

namespace DrupalCodeGenerator\Tests\Generator\Other;

use DrupalCodeGenerator\Tests\Generator\BaseGeneratorTest;

/**
 * Test for misc:apache-virtual-host command.
 */
class ApacheVirtualHostTest extends BaseGeneratorTest {

  protected $class = 'Misc\ApacheVirtualHost';

  protected $interaction = [
    'Host name [example.local]:' => 'site.com',
    'Document root [/var/www/site.com/public]:' => '/var/www/example.com/public',
  ];

  protected $fixtures = [
    'site.com-ssl.conf' => '/_apache_virtual_host_ssl.conf',
    'site.com.conf' => '/_apache_virtual_host.conf',
  ];

}
