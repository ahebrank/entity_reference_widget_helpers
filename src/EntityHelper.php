<?php

namespace Drupal\entity_reference_widget_helpers;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DepenencyInjection\ContainerInterface;

class EntityHelper {
    /**
     * inheritdoc
     */
    public function __construct(QueryFactory $entity_query, EntityTypeManagerInterface $entity_manager) {
        $this->entity_query = $entity_query;
        $this->entity_manager = $entity_manager;
    }

    /**
     * inheritdoc
     */
    public static function create(ContainerInterface $container) {
        return new static(
          $container->get('entity.query'),
          $container->get('entity_type.manager')
        );
    }
    

    /**
     * generate a list of id => title options for a select list 
     *
     * @param string $type
     * @param array $bundles
     * @return array
     */
    public function getOptions($type, $bundles) {
        $query = $this->entity_query->get($type)
            ->condition('type', $bundles, 'IN');
        $ids = $query->execute();

        $entities = $this->entity_manager->getStorage($type)->loadMultiple($ids);
        $opts = [];
        foreach ($entities as $id => $entity) {
            $opts[$id] = $entity->label();
        }
        return $opts;
    }

}