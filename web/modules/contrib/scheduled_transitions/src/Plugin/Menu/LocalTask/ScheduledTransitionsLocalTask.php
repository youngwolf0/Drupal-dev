<?php

declare(strict_types=1);

namespace Drupal\scheduled_transitions\Plugin\Menu\LocalTask;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Menu\LocalTaskDefault;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\scheduled_transitions\ScheduledTransitionsUtility;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a local task showing count of scheduled transitions for an entity.
 */
class ScheduledTransitionsLocalTask extends LocalTaskDefault implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * Constructs a new ScheduledTransitionsLocalTask.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   *   The string translation service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    array $plugin_definition,
    protected RouteMatchInterface $routeMatch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected LanguageManagerInterface $languageManager,
    TranslationInterface $stringTranslation,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->stringTranslation = $stringTranslation;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('string_translation'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle(Request $request = NULL) {
    $entity = $this->getEntityFromRouteMatch();
    if ($entity === NULL) {
      return NULL;
    }
    $count = $this->getEntityTransitionCount($entity);
    return $this->t('@title (@count)', [
      '@title' => parent::getTitle($request),
      '@count' => $count,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    $tags = parent::getCacheTags();

    $entity = $this->getEntityFromRouteMatch();
    if ($entity) {
      $tags[] = ScheduledTransitionsUtility::createScheduledTransitionsCacheTag($entity);
    }
    return $tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    $contexts = parent::getCacheContexts();
    $contexts[] = 'url';
    return $contexts;
  }

  /**
   * Get entity from route match.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface|null
   *   The entity from route match.
   */
  protected function getEntityFromRouteMatch(): ?ContentEntityInterface {
    [1 => $entityTypeId] = explode('.', $this->pluginDefinition['base_route']);

    // Get the first parameter in the route definition matching the entity type,
    // since the upcasted entity parameter could be something like {entity}.
    $parameters = $this->routeMatch->getParameters()->all();
    foreach ($parameters as $parameter) {
      if ($parameter instanceof ContentEntityInterface && $parameter->getEntityTypeId() === $entityTypeId) {
        return $parameter;
      }
    }

    return NULL;
  }

  /**
   * Gets the number of the transitions for an entity.
   *
   * @return int
   *   The number of transitions for an entity.
   */
  protected function getEntityTransitionCount(ContentEntityInterface $entity): int {
    $transitionStorage = $this->entityTypeManager->getStorage('scheduled_transition');
    return $transitionStorage->getQuery()
      ->accessCheck(FALSE)
      ->condition('entity__target_type', $entity->getEntityTypeId())
      ->condition('entity__target_id', $entity->id())
      ->condition('entity_revision_langcode', $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId())
      ->condition('is_processed', 0)
      ->count()
      ->execute();
  }

}
