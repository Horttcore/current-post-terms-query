<?php

declare(strict_types=1);

namespace CurrentPostTermsQuery;

/**
 * Restricts terms-query blocks to terms assigned to the current post.
 */
class CurrentPostTermsQueryFilter
{
    public static function init(): void
    {
        add_filter('render_block_data', [self::class, 'filterBlockData'], 10, 3);
    }

    /**
     * @param array<string, mixed> $parsed_block
     * @param array<string, mixed> $source_block
     * @param \WP_Block|null       $parent_block
     * @return array<string, mixed>
     */
    public static function filterBlockData(
        array $parsed_block,
        array $source_block,
        ?\WP_Block $parent_block,
    ): array {
        if (($parsed_block['blockName'] ?? '') !== 'core/terms-query') {
            return $parsed_block;
        }

        $term_query = $parsed_block['attrs']['termQuery'] ?? [];
        if (!is_array($term_query) || empty($term_query['showCurrentPostTerms'])) {
            return $parsed_block;
        }

        $taxonomy = sanitize_key((string) ($term_query['taxonomy'] ?? ''));
        $post_id = (int) get_the_ID();

        if (!$taxonomy || !$post_id || !taxonomy_exists($taxonomy)) {
            return $parsed_block;
        }

        $current_post_term_ids = wp_get_post_terms($post_id, $taxonomy, [
            'fields' => 'ids',
        ]);

        if (is_wp_error($current_post_term_ids)) {
            $current_post_term_ids = [];
        }

        $current_post_term_ids = array_map('intval', $current_post_term_ids);
        $included_term_ids = array_map('intval', (array) ($term_query['include'] ?? []));

        $term_query['include'] = empty($included_term_ids)
            ? $current_post_term_ids
            : array_values(array_intersect($included_term_ids, $current_post_term_ids));

        if (empty($term_query['include'])) {
            $term_query['include'] = [0];
        }

        unset($term_query['showCurrentPostTerms']);
        $parsed_block['attrs']['termQuery'] = $term_query;

        return $parsed_block;
    }
}
