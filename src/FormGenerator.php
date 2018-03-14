<?php

namespace Drupal\entity_reference_widget_helpers;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Url;
use Drupal\Component\Utility\Html;


class FormGenerator {
    /**
     * convert an autocomplete widget to a <select>
     *
     * @param [type] $element
     * @param [type] $form_state
     * @param [type] $context
     * @param [type] $count
     * @return void
     */
    public static function useDropdown(&$element, $form_state, $context, $count = 0) {
        $entity_helper = \Drupal::service('entity_reference_widget_helpers.entity_helper');
        // @todo: need a plain autocoplete replace
        
        // ief add existing autocomplete
        if (isset($element['form']['entity_id'])) {
            // lookup the options
            $target = $element['form']['entity_id']['#target_type'];
            $bundles = $element['form']['entity_id']['#selection_settings']['target_bundles'];
            $options = $entity_helper->getOptions($target, $bundles);
            if ($count > 0) {
                if (count($options) > $count) {
                    return;
                }
            }
            $title = $element['form']['entity_id']['#title'];
            $required = $element['form']['entity_id']['#required'];
            $element['form']['entity_id'] = [
                '#type' => 'select',
                '#options' => $options,
                '#title' => $title,
                '#required' => $required,
            ];
        }
    }

    /**
     * add a link to a collection
     *
     * @param [type] $element
     * @param [type] $form_state
     * @param [type] $context
     * @return void
     */
    public static function collectionLink(&$element, $form_state, $context) {
        $entity_manager = \Drupal::entityManager();
        $field_settings = $context['items']->getSettings();
        $urls = [];
        if ($field_settings['target_type'] == 'taxonomy_term') {
            foreach ($field_settings['handler_settings']['target_bundles'] as $bundle) {
                $vocab = 'List ' . $entity_manager->getStorage('taxonomy_vocabulary')->load($bundle)->label() . ' terms';
                $urls[$vocab] = Url::fromRoute('entity.taxonomy_vocabulary.overview_form', ['taxonomy_vocabulary' => $bundle]);
            }
        }
        if ($field_settings['target_type'] == 'webform') {
            $urls['List webforms'] = Url::fromRoute('entity.webform.collection');
        }


        $element['collection_links'] = [
            '#type' => 'dropbutton',
            '#links' => [],
        ];
        foreach ($urls as $k => $url) {
            $element['collection_links']['#links'][Html::cleanCssIdentifier($k)] = [
                'title' => t($k),
                'url' => $url,
            ];
        }
    }
}