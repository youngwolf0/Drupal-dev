<?php

declare(strict_types=1);

namespace Drupal\scheduled_transitions\Event;

use Drupal\Core\Entity\RevisionableInterface;
use Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Used to determine the new revision for a transition.
 *
 * @see \Drupal\scheduled_transitions\Event\ScheduledTransitionsEvents
 */
class ScheduledTransitionsNewRevisionEvent extends Event {

  /**
   * The new revision to transition.
   *
   * @var \Drupal\Core\Entity\RevisionableInterface|null
   */
  protected ?RevisionableInterface $newRevision;

  /**
   * Constructs a new ScheduledTransitionsNewRevisionEvent.
   *
   * @param \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface $scheduledTransition
   *   Scheduled transition entity.
   */
  public function __construct(
      protected ScheduledTransitionInterface $scheduledTransition,
  ) {
  }

  /**
   * Gets the scheduled transition entity.
   *
   * @return \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface
   *   A scheduled transition entity.
   */
  public function getScheduledTransition(): ScheduledTransitionInterface {
    return $this->scheduledTransition;
  }

  /**
   * Get the new revision.
   *
   * @return \Drupal\Core\Entity\RevisionableInterface|null
   *   The new revision.
   */
  public function getNewRevision(): ?RevisionableInterface {
    return $this->newRevision;
  }

  /**
   * Sets the new revision.
   *
   * @param \Drupal\Core\Entity\RevisionableInterface $newRevision
   *   The new revision.
   */
  public function setNewRevision(RevisionableInterface $newRevision): void {
    $this->newRevision = $newRevision;
  }

}
