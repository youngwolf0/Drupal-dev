<?php

declare(strict_types=1);

namespace Drupal\scheduled_transitions;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface;

/**
 * Scheduled Transitions configuration.
 */
class ScheduledTransitionsConfig implements ScheduledTransitionsConfigInterface {

  /**
   * Creates a new ScheduledTransitionsConfig.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   *
   * @internal There is no extensibility promise for the constructor.
   */
  final public function __construct(
    protected ConfigFactoryInterface $configFactory,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function isCreatingQueueItemsInHookCron(): bool {
    return (bool) $this->configFactory
      ->get('scheduled_transitions.settings')
      ->get('automation.cron_create_queue_items');
  }

  /**
   * {@inheritdoc}
   */
  public function isRetainingAfterProcessing(ScheduledTransitionInterface $scheduledTransition): bool {
    return (bool) $this->configFactory
      ->get('scheduled_transitions.settings')
      ->get('retain_processed.enabled');
  }

  /**
   * {@inheritdoc}
   */
  public function isRetentionDurationForever(): bool {
    return $this->getRetentionDuration() === -1;
  }

  /**
   * {@inheritdoc}
   */
  public function getRetentionDuration(): int {
    return (int) $this->configFactory
      ->get('scheduled_transitions.settings')
      ->get('retain_processed.duration');
  }

  /**
   * {@inheritdoc}
   */
  public function getProcessedLinkTemplate(RevisionableInterface $entity): ?string {
    $linkTemplate = $this->configFactory
      ->get('scheduled_transitions.settings')
      ->get('retain_processed.link_template');
    return !empty($linkTemplate) ? $linkTemplate : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessageTransitionCopyLatestDraft(RevisionLogInterface $revision): string {
    return (string) $this->configFactory
      ->get('scheduled_transitions.settings')
      ->get('message_transition_copy_latest_draft');
  }

}
