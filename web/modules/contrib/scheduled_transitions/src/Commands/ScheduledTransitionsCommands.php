<?php

declare(strict_types=1);

namespace Drupal\scheduled_transitions\Commands;

use Drupal\scheduled_transitions\ScheduledTransitionsJobsInterface;
use Drush\Commands\DrushCommands;

/**
 * Drush command for Scheduled Transitions.
 */
class ScheduledTransitionsCommands extends DrushCommands {

  /**
   * Constructs a new ScheduledTransitionsCommands.
   *
   * @param \Drupal\scheduled_transitions\ScheduledTransitionsJobsInterface $jobs
   *   Job runner for Scheduled Transitions.
   */
  public function __construct(
    protected ScheduledTransitionsJobsInterface $jobs,
  ) {
  }

  /**
   * Fills queue with crawler jobs.
   *
   * @command scheduled-transitions:queue-jobs
   * @aliases sctr-jobs
   */
  public function crawlJobCreator(): void {
    $this->jobs->jobCreator();
    $this->logger()->success(dt('Scheduled transitions queued.'));
  }

}
