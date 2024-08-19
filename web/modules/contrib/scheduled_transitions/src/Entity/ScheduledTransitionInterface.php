<?php

declare(strict_types=1);

namespace Drupal\scheduled_transitions\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserInterface;
use Drupal\workflows\WorkflowInterface;

/**
 * Interface for Scheduled Transitions.
 */
interface ScheduledTransitionInterface extends ContentEntityInterface {

  /**
   * Entity operation for rescheduling transitions for a scheduled transition.
   */
  public const ENTITY_OPERATION_RESCHEDULE = 'reschedule';

  /**
   * Option to schedule latest revision.
   */
  public const OPTION_LATEST_REVISION = 'latest_revision';

  /**
   * Creates a new Scheduled Transition from known common metadata.
   *
   * Language code and revision ID are set with $revision context.
   *
   * @param \Drupal\workflows\WorkflowInterface $workflow
   *   The workflow.
   * @param string $state
   *   The state ID.
   * @param \Drupal\Core\Entity\RevisionableInterface $revision
   *   Set the revision this scheduled transition is for.
   * @param \DateTimeInterface $dateTime
   *   The transition date.
   * @param \Drupal\Core\Session\AccountInterface $author
   *   The scheduled transition author.
   *
   * @return static
   *   A new unsaved Scheduled Transition.
   */
  public static function createFrom(WorkflowInterface $workflow, string $state, RevisionableInterface $revision, \DateTimeInterface $dateTime, AccountInterface $author): static;

  /**
   * Get the entity this scheduled transition is for.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity.
   */
  public function getEntity(): ?EntityInterface;

  /**
   * Set the revision this scheduled transition is for.
   *
   * @param \Drupal\Core\Entity\RevisionableInterface $revision
   *   The revision to be transitioned.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setEntity(RevisionableInterface $revision);

  /**
   * Get the revision this scheduled transition is for.
   *
   * @return string|int|null
   *   The revision ID.
   */
  public function getEntityRevisionId(): string|int|null;

  /**
   * Set the revision this scheduled transition is for.
   *
   * @param string|int $revisionId
   *   The revision to be transitioned.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setEntityRevisionId($revisionId);

  /**
   * Get the language of the revision this scheduled transition is for.
   *
   * @return string|null
   *   The revision language code.
   */
  public function getEntityRevisionLanguage(): ?string;

  /**
   * Set the language of the revision this scheduled transition is for.
   *
   * @param string $langCode
   *   The revision language code.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setEntityRevisionLanguage(string $langCode);

  /**
   * Get the author for this scheduled transition.
   *
   * @return \Drupal\user\UserInterface|null
   *   The author.
   */
  public function getAuthor(): ?UserInterface;

  /**
   * Set the author of the scheduled transition.
   *
   * @param \Drupal\Core\Session\AccountInterface $author
   *   The scheduled transition author.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setAuthor(AccountInterface $author);

  /**
   * Get the workflow for this scheduled transition.
   *
   * @return \Drupal\workflows\WorkflowInterface|null
   *   The workflow.
   */
  public function getWorkflow(): ?WorkflowInterface;

  /**
   * Set the new workflow and state for this scheduled transition.
   *
   * @param \Drupal\workflows\WorkflowInterface $workflow
   *   The workflow.
   * @param string $state
   *   The state ID.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setState(WorkflowInterface $workflow, string $state);

  /**
   * Get the new state for this scheduled transition.
   *
   * @return string|null
   *   The state ID.
   */
  public function getState(): ?string;

  /**
   * Get the time this scheduled transition was created.
   *
   * @return int
   *   The creation time.
   */
  public function getCreatedTime(): int;

  /**
   * Set the time this scheduled transition was created.
   *
   * @param \DateTimeInterface $createDate
   *   The time this scheduled transition was created.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setCreatedDate(\DateTimeInterface $createDate);

  /**
   * Get the date this scheduled transition should execute.
   *
   * @return \DateTimeInterface
   *   The scheduled transition date.
   */
  public function getTransitionDate(): \DateTimeInterface;

  /**
   * Get the time this scheduled transition should execute.
   *
   * @return int
   *   The scheduled transition time.
   */
  public function getTransitionTime(): int;

  /**
   * Set the date this scheduled transition should execute.
   *
   * @param \DateTimeInterface $dateTime
   *   The transition date.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setTransitionDate(\DateTimeInterface $dateTime);

  /**
   * Sets the transition time.
   *
   * @param int $time
   *   The transition time.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setTransitionTime(int $time);

  /**
   * Sets the lock time.
   *
   * @param int $time
   *   The lock time.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setLockedOn(int $time);

  /**
   * Get whether this scheduled transition is processed.
   *
   * @return bool
   *   Whether this scheduled transition is processed.
   */
  public function isProcessed(): bool;

  /**
   * Get the scheduled transition process date.
   *
   * @return \DateTimeInterface|null
   *   The scheduled transition process date.
   */
  public function getProcessedDate(): ?\DateTimeInterface;

  /**
   * Mark the scheduled transition as processed.
   *
   * @param \DateTimeInterface $transitionDate
   *   The transition date.
   * @param array<string|int> $revisionIds
   *   The revisions created.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setIsProcessed(\DateTimeInterface $transitionDate, array $revisionIds);

  /**
   * Mark the scheduled transition as not processed.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setIsNotProcessed();

  /**
   * Get the revision IDs of revisions saved.
   *
   * @return array<string|int>
   *   The revisions created.
   */
  public function getProcessedRevisions(): array;

  /**
   * Get the options.
   *
   * @return array
   *   An array of options.
   */
  public function getOptions(): array;

  /**
   * Sets options.
   *
   * @param array $options
   *   An array of options.
   *
   * @return $this
   *   Returns entity for chaining.
   */
  public function setOptions(array $options);

}
