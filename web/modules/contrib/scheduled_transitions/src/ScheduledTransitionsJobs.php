<?php

declare(strict_types=1);

namespace Drupal\scheduled_transitions;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\scheduled_transitions\Plugin\QueueWorker\ScheduledTransitionJob;
use Psr\Log\LoggerInterface;

/**
 * Job runner for Scheduled Transitions.
 */
class ScheduledTransitionsJobs implements ScheduledTransitionsJobsInterface {

  /**
   * Duration a scheduled transition should be locked from adding to queue.
   */
  protected const LOCK_DURATION = 1800;

  /**
   * The scheduled transition job queue.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected QueueInterface $queue;

  /**
   * Constructs a new ScheduledTransitionsRunner.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   System time.
   * @param \Drupal\Core\Queue\QueueFactory $queueFactory
   *   The queue factory.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\scheduled_transitions\ScheduledTransitionsConfigInterface $scheduledTransitionsConfig
   *   Scheduled Transitions Configuration.
   */
  final public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected TimeInterface $time,
    QueueFactory $queueFactory,
    protected LoggerInterface $logger,
    private ScheduledTransitionsConfigInterface $scheduledTransitionsConfig,
  ) {
    $this->queue = $queueFactory->get(ScheduledTransitionJob::PLUGIN_ID);
  }

  /**
   * {@inheritdoc}
   */
  public function jobCreator(): void {
    $transitionStorage = $this->entityTypeManager
      ->getStorage('scheduled_transition');

    $now = $this->time->getRequestTime();
    $query = $transitionStorage->getQuery();
    $query->accessCheck(FALSE);
    $query->condition('transition_on', $now, '<=');
    $query->condition('is_processed', '1', '<>');
    $or = $query->orConditionGroup()
      ->condition('locked_on', NULL, 'IS NULL')
      ->condition('locked_on', $now - static::LOCK_DURATION, '>=');
    $query->condition($or);
    $ids = $query->execute();

    /** @var \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface[] $transitions */
    $transitions = $transitionStorage->loadMultiple($ids);
    foreach ($transitions as $transition) {
      $transition->setLockedOn($now)->save();
      $this->queue->createItem(ScheduledTransitionJob::createFrom($transition));
      $this->logger->info('Created scheduled transition job for #@id', [
        '@id' => $transition->id(),
      ]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function cleanupExpired(): void {
    if ($this->scheduledTransitionsConfig->isRetentionDurationForever()) {
      return;
    }

    /** @var int<0, max> $lifetimeSeconds */
    $lifetimeSeconds = $this->scheduledTransitionsConfig->getRetentionDuration();

    $transitionStorage = $this->entityTypeManager
      ->getStorage('scheduled_transition');

    $now = $this->time->getRequestTime();
    $query = $transitionStorage->getQuery();
    $ids = $query
      ->accessCheck(FALSE)
      ->condition('is_processed', '0', '<>')
      ->condition('processed_date', operator: 'IS NOT NULL')
      ->condition('processed_date', $now - $lifetimeSeconds, '<')
      ->execute();

    $transitionStorage->delete($transitionStorage->loadMultiple($ids));
  }

}
