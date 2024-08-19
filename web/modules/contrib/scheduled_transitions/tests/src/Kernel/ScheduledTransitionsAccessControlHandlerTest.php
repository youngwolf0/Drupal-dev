<?php

declare(strict_types=1);

namespace Drupal\Tests\scheduled_transitions\Kernel;

use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Access\AccessResultForbidden;
use Drupal\KernelTests\KernelTestBase;
use Drupal\scheduled_transitions\Entity\ScheduledTransition;
use Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface;
use Drupal\scheduled_transitions_test\Entity\ScheduledTransitionsTestEntity as TestEntity;
use Drupal\Tests\content_moderation\Traits\ContentModerationTestTrait;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Tests access control.
 *
 * @group scheduled_transitions
 * @coversDefaultClass \Drupal\scheduled_transitions\ScheduledTransitionsAccessControlHandler
 */
final class ScheduledTransitionsAccessControlHandlerTest extends KernelTestBase {

  use ContentModerationTestTrait;
  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'scheduled_transitions_test',
    'scheduled_transitions',
    'dynamic_entity_reference',
    'content_moderation',
    'workflows',
    'field',
    'user',
    'system',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('scheduled_transition');
    $this->installEntitySchema('st_entity_test');
    $this->installEntitySchema('content_moderation_state');
    $this->installConfig('scheduled_transitions');
  }

  /**
   * Tests operations when processed.
   *
   * @dataProvider providerOperationWhenProcessed
   */
  public function testOperationWhenProcessed(string $operation): void {
    $user = $this->createUser([
      'administer st_entity_test entities',
    ]);

    $workflow = $this->createEditorialWorkflow();
    $workflow->getTypePlugin()->addEntityTypeAndBundle('st_entity_test', 'st_entity_test');
    $workflow->save();

    $entity = TestEntity::create(['type' => 'st_entity_test']);
    $scheduledTransition = ScheduledTransition::create([
      'entity' => $entity,
      'entity_revision_id' => 2,
      'author' => $user,
      'workflow' => $workflow->id(),
      'moderation_state' => 'published',
      'transition_on' => (new \DateTime('2 Feb 2018 11am'))->getTimestamp(),
    ]);
    $scheduledTransition->save();

    $accessResult = $scheduledTransition->access($operation, account: $user, return_as_object: TRUE);
    $this->assertInstanceOf(AccessResultAllowed::class, $accessResult);

    $scheduledTransition->setIsProcessed(
      new \DateTimeImmutable('1 August 2012'),
      [1337],
    )->save();

    \Drupal::entityTypeManager()->getAccessControlHandler('scheduled_transition')->resetCache();
    $accessResult = $scheduledTransition->access($operation, account: $user, return_as_object: TRUE);
    $this->assertInstanceOf(AccessResultForbidden::class, $accessResult);
    $this->assertEquals(sprintf('Cannot `%s` when Scheduled Transition has been processed.', $operation), $accessResult->getReason());
  }

  /**
   * Data provider.
   */
  public function providerOperationWhenProcessed(): \Generator {
    yield 'delete' => ['delete'];
    yield 'reschedule' => [ScheduledTransitionInterface::ENTITY_OPERATION_RESCHEDULE];
    yield 'edit' => ['update'];
  }

}
