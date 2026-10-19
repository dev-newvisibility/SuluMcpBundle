<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Mcp\Application\Metadata;

/**
 * Resolves the form keys that make up the seo / excerpt tabs.
 *
 * Sulu collects every form tagged `sulu_content.content_excerpt_form` /
 * `sulu_content.content_seo_form` (core and project forms alike) into the
 * `sulu_content.content_excerpt_forms` / `sulu_content.content_seo_forms`
 * parameters. Reading them instead of hard-coding the core keys makes project
 * extensions (e.g. a custom `excerpt/media` field) available to the MCP tools.
 *
 * @internal
 */
final readonly class ExtensionFormKeys
{
    private const DEFAULT_SEO_FORM_KEYS = ['content_seo_metadata'];
    private const DEFAULT_EXCERPT_FORM_KEYS = ['content_excerpt_metadata', 'content_excerpt_taxonomies'];

    /**
     * @param array<string, array{instanceOf: class-string, priority: int}> $excerptForms
     * @param array<string, array{instanceOf: class-string, priority: int}> $seoForms
     */
    public function __construct(
        private array $excerptForms = [],
        private array $seoForms = [],
    ) {
    }

    /**
     * @return list<string>
     */
    public function excerpt(): array
    {
        return $this->keys($this->excerptForms, self::DEFAULT_EXCERPT_FORM_KEYS);
    }

    /**
     * @return list<string>
     */
    public function seo(): array
    {
        return $this->keys($this->seoForms, self::DEFAULT_SEO_FORM_KEYS);
    }

    /**
     * Highest priority first, matching the order Sulu merges the sub-forms in the admin.
     *
     * @param array<string, array{instanceOf: class-string, priority: int}> $forms
     * @param list<string> $defaults
     *
     * @return list<string>
     */
    private function keys(array $forms, array $defaults): array
    {
        if ([] === $forms) {
            return $defaults;
        }

        \uasort($forms, static fn (array $a, array $b): int => $b['priority'] <=> $a['priority']);

        return \array_map(\strval(...), \array_keys($forms));
    }
}
