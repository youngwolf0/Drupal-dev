<?php

declare(strict_types=1);

namespace Drupal\scheduled_transitions;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Hooks for Scheduled Transitions module.
 */
class ScheduledTransitionsHooks implements ContainerInjectionInterface {

  /**
   * Constructs a new ScheduledTransitionsHooks.
   *
   * @param \Drupal\scheduled_transitions\ScheduledTransitionsConfigInterface $scheduledTransitionsConfig
   *   Scheduled transitions configuration.
   * @param \Drupal\scheduled_transitions\ScheduledTransitionsJobsInterface $scheduledTransitionsJobs
   *   Job runner for Scheduled Transitions.
   */
  final public function __construct(
    private ScheduledTransitionsConfigInterface $scheduledTransitionsConfig,
    private ScheduledTransitionsJobsInterface $scheduledTransitionsJobs,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('scheduled_transitions.config'),
      $container->get('scheduled_transitions.jobs'),
    );
  }

  /**
   * Implements hook_cron().
   *
   * @see \scheduled_transitions_cron()
   */
  public function cron(): void {
    if ($this->scheduledTransitionsConfig->isCreatingQueueItemsInHookCron()) {
      $this->scheduledTransitionsJobs->jobCreator();
    }

    if ($this->scheduledTransitionsConfig->isRetentionDurationForever() === FALSE) {
      $this->scheduledTransitionsJobs->cleanupExpired();
    }
  }

}
