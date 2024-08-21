<?php

namespace Drupal\assessment_poster\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class AssessmentPosterController.
 *
 * Provides a page showing a css recreation of a poster.
 */
class AssessmentPosterController extends ControllerBase {

  /**
   * Content callback for the /assessment1 route.
   *
   * @return array
   *   A render array to be used with a template.
   */
  public function content() {
    $columns = $this->get_column_data();

    return [
      '#theme' => 'assessment_poster_page',
      '#columns' => $columns,
      '#attached' => [
        'library' => [
          'assessment_poster/assessment_style',
        ],
      ],
    ];
  }

  private function get_column_data() {

    $columns = [
      [
        'row' => '1',
        'width' => '2%',
        'rows' => [
          ['height' => '75%', 'color' => 'black'],
          ['height' => '15%', 'color' => 'brown'],
          ['height' => '10%', 'color' => 'purple'],
        ],
      ],
      [
        'row' => '2',
        'width' => '4%',
        'rows' => [
          ['height' => '55%', 'color' => 'dark_pink'],
          ['height' => '30%', 'color' => 'grey'],
          ['height' => '15%', 'color' => 'pink'],
        ],
      ],
      [
        'row' => '3',
        'width' => '5%',
        'rows' => [
          ['height' => '37.5%', 'color' => 'white'],
          ['height' => '37.5%', 'color' => 'black'],
          ['height' => '25%', 'color' => 'brown'],
        ],
      ],
      [
        'row' => '4',
        'width' => '9%',
        'rows' => [
          ['height' => '25%', 'color' => 'purple'],
          ['height' => '37%', 'color' => 'dark_pink'],
          ['height' => '38%', 'color' => 'grey'],
        ],
      ],
      [
        'row' => '5',
        'width' => '5%',
        'rows' => [
          ['height' => '17%', 'color' => 'pink'],
          ['height' => '29%', 'color' => 'white'],
          ['height' => '54%', 'color' => 'black'],
        ],
      ],
      [
        'row' => '6',
        'width' => '7%',
        'rows' => [
          ['height' => '8%', 'color' => 'brown'],
          ['height' => '17%', 'color' => 'purple'],
          ['height' => '75%', 'color' => 'dark_pink'],
        ],
      ],
      [
        'row' => '7',
        'width' => '2%',
        'rows' => [
          ['height' => '75%', 'color' => 'grey'],
          ['height' => '17%', 'color' => 'pink'],
          ['height' => '8%', 'color' => 'white'],
        ],
      ],
      [
        'row' => '8',
        'width' => '5%',
        'rows' => [
          ['height' => '54%', 'color' => 'black'],
          ['height' => '29%', 'color' => 'brown'],
          ['height' => '17%', 'color' => 'purple'],
        ],
      ],
      [
        'row' => '9',
        'width' => '7%',
        'rows' => [
          ['height' => '38%', 'color' => 'dark_pink'],
          ['height' => '37%', 'color' => 'grey'],
          ['height' => '25%', 'color' => 'pink'],
        ],
      ],
      [
        'row' => '10',
        'width' => '9%',
        'rows' => [
          ['height' => '25%', 'color' => 'white'],
          ['height' => '38%', 'color' => 'black'],
          ['height' => '37%', 'color' => 'brown'],
        ],
      ],
      [
        'row' => '11',
        'width' => '4%',
        'rows' => [
          ['height' => '17%', 'color' => 'purple'],
          ['height' => '29%', 'color' => 'dark_pink'],
          ['height' => '54%', 'color' => 'grey'],
        ],
      ],
      [
        'row' => '12',
        'width' => '7%',
        'rows' => [
          ['height' => '8%', 'color' => 'pink'],
          ['height' => '17%', 'color' => 'white'],
          ['height' => '75%', 'color' => 'black'],
        ],
      ],
      [
        'row' => '13',
        'width' => '2%',
        'rows' => [
          ['height' => '75%', 'color' => 'brown'],
          ['height' => '17%', 'color' => 'purple'],
          ['height' => '8%', 'color' => 'dark_pink'],
        ],
      ],
      [
        'row' => '14',
        'width' => '4%',
        'rows' => [
          ['height' => '54%', 'color' => 'grey'],
          ['height' => '29%', 'color' => 'pink'],
          ['height' => '17%', 'color' => 'white'],
        ],
      ],
      [
        'row' => '15',
        'width' => '7%',
        'rows' => [
          ['height' => '38%', 'color' => 'black'],
          ['height' => '37%', 'color' => 'brown'],
          ['height' => '25%', 'color' => 'purple'],
        ],
      ],
      [
        'row' => '16',
        'width' => '9%',
        'rows' => [
          ['height' => '25%', 'color' => 'pink'],
          ['height' => '37%', 'color' => 'grey'],
          ['height' => '38%', 'color' => 'pink'],
        ],
      ],
      [
        'row' => '17',
        'width' => '5%',
        'rows' => [
          ['height' => '17%', 'color' => 'white'],
          ['height' => '29%', 'color' => 'black'],
          ['height' => '54%', 'color' => 'brown'],
        ],
      ],
      [
        'row' => '18',
        'width' => '7%',
        'rows' => [
          ['height' => '8%', 'color' => 'purple'],
          ['height' => '17%', 'color' => 'pink'],
          ['height' => '75%', 'color' => 'grey'],
        ],
      ],

    ];

    return $columns;
  }

}
