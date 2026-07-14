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
        add_filter('render_block_context', [self::class, 'filterBlockContext'], 10, 3);
    }

    /**
     * Filters the context consumed by the core/term-template renderer.
     *
     * @param array<string, mixed> $context
     * @param array<string, mixed> $parsed_block
     * @param \WP_Block|null       $parent_block
     * @return array<string, mixed>
     */
    public static function filterBlockContext(
        array $context,
        array $parsed_block,
        ?\WP_Block $parent_block,
    ): array {
        if (($parsed_block['blockName'] ?? '') !== 'core/term-template') {
            return $context;
        }

        $term_query = $context['termQuery'] ?? [];
        if (!is_array($term_query) || empty($term_query['showCurrentPostTerms'])) {
            return $context;
        }

        $taxonomy = sanitize_key((string) ($term_query['taxonomy'] ?? ''));
        $post_id = self::getCurrentPostId($context, $parent_block);

        if (!$taxonomy || !$post_id || !taxonomy_exists($taxonomy)) {
            return $context;
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
        $context['termQuery'] = $term_query;

        return $context;
    }

    private static function getCurrentPostId(
        array $context,
        ?\WP_Block $parent_block,
    ): int
    {
        if (!empty($context['postId'])) {
            return (int) $context['postId'];
        }

        if ($parent_block instanceof \WP_Block) {
            $context_post_id = $parent_block->context['postId'] ?? 0;

            if ($context_post_id) {
                return (int) $context_post_id;
            }
        }

        // Template parts and standalone template previews do not have a
        // reliable post context. Leave the query unfiltered in that case.
        return 0;
    }
}
