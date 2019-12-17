<?php

namespace Drupal\listarticle_block\Plugin\Block;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a 'ListArticleBlock' block.
 *
 * @Block(
 *  id = "list_article_block",
 *  admin_label = @Translation("List Article Block"),
 * )
 */
class ListArticle extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $query = \Drupal::entityQuery('node')
            ->condition('type', 'article')
            ->condition('langcode', $lang)
            ->condition('uid', 1)
            ->pager(3);
        $filter_nids = $query->execute();
        //dsm($filter_nids);

        $nodes = Node::loadMultiple($filter_nids);

        foreach ($nodes as $nod) {
            $items[] = array(
                'id' => $nod->nid->value,
                'title' => $nod->title->value,
                'body' => $nod->body->value,
                'image' => file_create_url($nod->field_image->offsetGet(0)->get('entity')->getValue()->getFileUri()),
            );
        }
        return array(
            '#theme' => 'list_article',
            '#items' => $items,
        );
    }


}