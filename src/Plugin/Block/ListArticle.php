<?php

namespace Drupal\listarticle_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
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
        $config = $this->getConfiguration();
        $content_type = $config['type'];
        $rang = $config['rang'];

        //kint($rang);
        $query = \Drupal::entityQuery('node')
            ->condition('type', $content_type)
            ->condition('langcode', $lang)
            ->range(0, $rang);


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

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        return [
            'type' => 'article',
            'rang' => 3,
        ];

    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state)
    {

        $form['type_content'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Type Content'),
            '#description' => $this->t('Who do you want to say Type Content to?'),
            '#default_value' => $this->configuration['type']
        ];
        $form['rang_content'] = array(
            '#title' => $this->t('Number content'),
            '#description' => $this->t('This is Number content'),
            '#type' => 'number',
            '#default_value' => $this->configuration['rang'],
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {
        $this->configuration['type'] = $form_state->getValue('type_content');
        $this->configuration['rang'] = $form_state->getValue('rang_content');
    }


}