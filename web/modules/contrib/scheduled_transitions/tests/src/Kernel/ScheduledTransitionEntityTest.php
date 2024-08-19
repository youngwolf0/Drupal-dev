<?php

declare(strict_types=1);

namespace Drupal\Tests\scheduled_transitions\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\scheduled_transitions\Entity\ScheduledTransition;

/**
 * Tests scheduled transition entity.
 *
 * @group scheduled_transitions
 * @coversDefaultClass \Drupal\scheduled_transitions\Entity\ScheduledTransition
 */
class ScheduledTransitionEntityTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
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
    $this->installEntitySchema('scheduled_transition');
  }

  /**
   * Tests getEntityRevisionLanguage method.
   *
   * @covers ::getEntityRevisionLanguage
   */
  public function testScheduledRevision(): void {
    $langCode = 'foobar';
    $scheduledTransition = ScheduledTransition::create([
      'entity_revision_langcode' => $langCode,
    ]);
    $this->assertEquals($langCode, $scheduledTransition->getEntityRevisionLanguage());
  }

  /**
   * @covers ::isProcessed
   * @covers ::getProcessedDate
   * @covers ::setIsProcessed
   * @covers ::setIsNotProcessed
   * @covers ::getProcessedRevisions
   */
  public function testProcessed(): void {
    $scheduledTransition = ScheduledTransition::create();

    $this->assertFalse($scheduledTransition->isProcessed());
    $this->assertNull($scheduledTransition->getProcessedDate());
    $this->assertEquals([], $scheduledTransition->getProcessedRevisions());

    $scheduledTransition->setIsProcessed(
      new \DateTimeImmutable('9am 14 December 2012', new \DateTimeZone('Asia/Singapore')),
      ['1337', 3333],
    );
    $this->assertTrue($scheduledTransition->isProcessed());
    $this->assertEquals('Fri, 14 Dec 2012 01:00:00 +0000', $scheduledTransition->getProcessedDate()->format('r'));
    $this->assertEquals([1337, 3333], $scheduledTransition->getProcessedRevisions());

    $scheduledTransition->setIsNotProcessed();
    $this->assertFalse($scheduledTransition->isProcessed());
    $this->assertNull($scheduledTransition->getProcessedDate());
    $this->assertEquals([], $scheduledTransition->getProcessedRevisions());
  }

}
