<?php

namespace DrupalCodeGenerator\Tests\Generator;

/**
 * Test for module:content-entity command.
 */
class ContentEntityTest extends BaseGeneratorTest {

  protected $class = 'ContentEntity';

  protected $interaction = [
    'Module name [%default_name%]:' => 'Foo',
    'Module machine name [foo]:' => 'foo',
    'Entity type label [Foo]:' => 'Example',
    'Entity type ID [example]:' => 'foo_example',
    'Entity base path [/admin/content/foo-example]:' => '/example',
    'Make the entity type fieldable? [Yes]:' => 'Yes',
    'Make the entity type revisionable? [No]:' => 'Yes',
    'Make the entity type translatable? [No]:' => 'No',
    'The entity type has bundle? [No]:' => 'No',
    'Create entity template? [Yes]:' => 'Yes',
    'Create CRUD permissions? [No]:' => 'No',
    'Add "label" base field? [Yes]:' => 'Yes',
    'Add "status" base field? [Yes]:' => 'Yes',
    'Add "created" base field? [Yes]:' => 'Yes',
    'Add "changed" base field? [Yes]:' => 'Yes',
    'Add "author" base field? [Yes]:' => 'Yes',
    'Add "description" base field? [Yes]:' => 'Yes',
    'Create REST configuration for the entity? [No]:' => 'No',
  ];

  protected $fixtures = [
    'foo.links.action.yml' => '/_content_entity/foo.links.action.yml',
    'foo.links.menu.yml' => '/_content_entity/foo.links.menu.yml',
    'foo.links.task.yml' => '/_content_entity/foo.links.task.yml',
    'foo.module' => '/_content_entity/foo.module',
    'foo.permissions.yml' => '/_content_entity/foo.permissions.yml',
    'foo.routing.yml' => '/_content_entity/foo.routing.yml',
    'src/FooExampleInterface.php' => '/_content_entity/src/ExampleInterface.php',
    'src/FooExampleListBuilder.php' => '/_content_entity/src/ExampleListBuilder.php',
    'templates/foo-example.html.twig' => '/_content_entity/templates/foo-example.html.twig',
    'src/Entity/FooExample.php' => '/_content_entity/src/Entity/Example.php',
    'src/Form/FooExampleForm.php' => '/_content_entity/src/Form/ExampleForm.php',
    'src/Form/FooExampleSettingsForm.php' => '/_content_entity/src/Form/ExampleSettingsForm.php',
  ];

}
