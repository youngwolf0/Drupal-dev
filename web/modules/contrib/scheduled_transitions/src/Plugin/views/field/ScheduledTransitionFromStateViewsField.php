<?php

declare(strict_types=1);

namespace Drupal\scheduled_transitions\Plugin\views\field;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Scheduled transition from state.
 *
 * @ViewsField(\Drupal\scheduled_transitions\Plugin\views\field\ScheduledTransitionFromStateViewsField::PLUGIN_ID)
 */
class ScheduledTransitionFromStateViewsField extends FieldPluginBase {

  public const PLUGIN_ID = 'scheduled_transitions_transition_from';

  /**
   * Constructs a ScheduledTransitionFromStateViewsField object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
      array $configuration,
      $plugin_id,
      $plugin_definition,
      protected EntityTypeManagerInterface $entityTypeManager,
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
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    /** @var \Drupal\scheduled_transitions\Entity\ScheduledTransitionInterface $scheduledTransition */
    $scheduledTransition = $this->getEntity($values);

    $entity = $scheduledTransition->getEntity();

    $workflowPlugin = $scheduledTransition->getWorkflow()->getTypePlugin();
    $workflowStates = $workflowPlugin ? $workflowPlugin->getStates() : [];

    /** @var \Drupal\Core\Entity\RevisionableStorageInterface $entityStorage */
    $entityStorage = $this->entityTypeManager->getStorage($entity->getEntityTypeId());

    $entityRevisionId = $scheduledTransition->getEntityRevisionId();
    if (is_numeric($entityRevisionId) && $entityRevisionId > 0) {
      $entityRevision = $entityStorage->loadRevision($entityRevisionId);

      $revisionTArgs = ['@revision_id' => $entityRevisionId];
      if ($entityRevision) {
        $fromState = $workflowStates[$entityRevision->moderation_state->value] ?? NULL;
        return $fromState ? $fromState->label() : $this->t('- Missing from workflow/state -');
      }
      else {
        return $this->t('Deleted revision #@revision_id', $revisionTArgs);
      }
    }
    else {
      return '';
    }
  }

}
