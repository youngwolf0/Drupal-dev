<?php

declare(strict_types=1);

namespace Drupal\scheduled_transitions\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface;
use Drupal\scheduled_transitions\Exception\ScheduledTransitionMissingEntity;
use Drupal\scheduled_transitions\ScheduledTransitionsRunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Runs a scheduled transition.
 *
 * @QueueWorker(
 *   id = \Drupal\scheduled_transitions\Plugin\QueueWorker\ScheduledTransitionJob::PLUGIN_ID,
 *   title = @Translation("Scheduled transition job"),
 *   cron = {"time" = 900}
 * )
 */
class ScheduledTransitionJob extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  public const PLUGIN_ID = 'scheduled_transition_job';

  /**
   * The key in data with the ID of a scheduled transition entity to process.
   */
  const SCHEDULED_TRANSITION_ID = 'scheduled_transition_id';

  /**
   * Constructs a new ScheduledTransitionJob.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $scheduledTransitionStorage
   *   Storage for scheduled transitions.
   * @param \Drupal\scheduled_transitions\ScheduledTransitionsRunnerInterface $scheduledTransitionsRunner
   *   Executes transitions.
   */
  public function __construct(
      array $configuration,
      string $plugin_id,
      $plugin_definition,
      protected EntityStorageInterface $scheduledTransitionStorage,
      protected ScheduledTransitionsRunnerInterface $scheduledTransitionsRunner,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('scheduled_transition'),
      $container->get('scheduled_transitions.runner'),
    );
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-param array{scheduled_transition_id: string|int} $data
   */
  public function processItem($data): void {
    $id = $data[static::SCHEDULED_TRANSITION_ID];
    /** @var \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface|null $transition */
    $transition = $this->scheduledTransitionStorage->load($id);
    if ($transition === NULL) {
      return;
    }

    try {
      $this->scheduledTransitionsRunner->runTransition($transition);
    }
    catch (ScheduledTransitionMissingEntity) {
      $transition->delete();
    }
  }

  /**
   * Creates data for a queue item, suitable for usage with ::processItem.
   *
   * @param \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface $scheduledTransition
   *   A scheduled transition.
   *
   * @return mixed
   *   Queue item data.
   *
   * @internal There is no backwards compatibility promise, the return value
   *   may change at any time.
   */
  public static function createFrom(ScheduledTransitionInterface $scheduledTransition): mixed {
    return [
      ScheduledTransitionJob::SCHEDULED_TRANSITION_ID => $scheduledTransition->id(),
    ];
  }

}
