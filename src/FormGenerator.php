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
     * @return $element
     */
    public static function useDropdown($element, $form_state, $context, $count = 0) {
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
                    // leave unchanged
                    return $element;
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

        return $element;
    }

    /**
     * add a link to a collection
     *
     * @param [type] $element
     * @param [type] $form_state
     * @param [type] $context
     * @return $element
     */
    public static function collectionLink($element, $form_state, $context) {
        $entity_manager = \Drupal::entityManager();
        $field_settings = $context['items']->getSettings();
        $urls = [];
        if ($field_settings['target_type'] == 'taxonomy_term') {
            foreach ($field_settings['handler_settings']['target_bundles'] as $bundle) {
                $urls['list_' . $bundle . '_terms'] = [
                    'title' => t('List @bundle terms', 
                        ['@bundle' => $entity_manager->getStorage('taxonomy_vocabulary')->load($bundle)->label()]),
                    'url' => Url::fromRoute('entity.taxonomy_vocabulary.overview_form', 
                        ['taxonomy_vocabulary' => $bundle])
                ];
            }
        }
        if ($field_settings['target_type'] == 'webform') {
            $urls['list_webforms'] = [
                'title' => t('List webforms'),
                'url' => Url::fromRoute('entity.webform.collection')
            ];
        }

        if ($urls) {
            // stick link(s) in the widget suffix
            if (!isset($element['#suffix'])) {
                $element['#suffix'] = '';
            }
            $e = [
                'collection_links' => [
                    '#type' => 'dropbutton',
                    '#links' => $urls,
                ],
            ];
            $rendered_collection = \Drupal::service('renderer')->render($e);
            $element['#suffix'] .= $rendered_collection;
        }

        return $element;
    }

    /**
     * add the paragraph description to the dropdown
     *
     * @param [type] $element
     * @param [type] $form_state
     * @param [type] $context
     * @return $element
     */
    public static function showParagraphDescription($element, $form_state, $context) {
        

        return $element;
    }
}