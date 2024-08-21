<?php

namespace Drupal\assessment_content_migration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Batch\BatchBuilder;
use Drupal\assessment_content_migration\Controller\CSVBatchProcessor;

/**
 * Defines a form to upload a CSV file and process it.
 */
class CSVImportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'assessment_content_migration_csv_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['csv_file'] = [
      '#type' => 'file',
      '#title' => $this->t('CSV file'),
      '#description' => $this->t('Upload the CSV file to import content.'),
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import CSV'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $validators = ['file_validate_extensions' => ['csv']];
    $file = file_save_upload('csv_file', $validators, FALSE, 0);

    if ($file) {
      $module_path = \Drupal::service('extension.list.module')->getPath('assessment_content_migration');

      // Set up the batch.
      $batch_builder = (new BatchBuilder())
        ->setTitle($this->t('Importing CSV content'))
        ->setInitMessage($this->t('Starting CSV import'))
        ->setProgressMessage($this->t('Processed @current out of @total.'))
        ->setErrorMessage($this->t('An error occurred during CSV import.'))
        ->setFile($module_path . '/src/Controller/CSVBatchProcessor.php')
        ->addOperation([CSVBatchProcessor::class, 'processCSV'], [$file->getFileUri()])
        ->setFinishCallback([CSVBatchProcessor::class, 'finishedCallback']);

      batch_set($batch_builder->toArray());
    }
  }
}
