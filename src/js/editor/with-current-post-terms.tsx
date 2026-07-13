import { InspectorControls } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { addFilter } from "@wordpress/hooks";

import { CurrentPostTermsToggle } from "./current-post-terms-toggle";

export const withCurrentPostTerms = (BlockEdit: any) => (props: any) => {
  if (props.name !== "core/terms-query") {
    return <BlockEdit {...props} />;
  }

  const termQuery = props.attributes?.termQuery || {};

  return (
    <>
      <BlockEdit {...props} />
      <InspectorControls>
        <CurrentPostTermsToggle
          termQuery={termQuery}
          setAttributes={props.setAttributes}
        />
      </InspectorControls>
    </>
  );
};

addFilter(
  "editor.BlockEdit",
  "current-post-terms-query/toggle",
  withCurrentPostTerms,
);

export const withCurrentPostTermsPreview = (BlockEdit: any) => (props: any) => {
  if (props.name !== "core/term-template") {
    return <BlockEdit {...props} />;
  }

  const termQuery = props.context?.termQuery || {};
  const taxonomy = termQuery.taxonomy || "";
  const showCurrentPostTerms = Boolean(termQuery.showCurrentPostTerms);
  const currentPostId = useSelect(
    (select: any) => select("core/editor").getCurrentPostId(),
    [],
  );

  const currentPostTerms = useSelect(
    (select: any) => {
      if (!showCurrentPostTerms || !taxonomy || !currentPostId) {
        return null;
      }

      return select("core").getEntityRecords("taxonomy", taxonomy, {
        post: currentPostId,
        per_page: 100,
      });
    },
    [showCurrentPostTerms, taxonomy, currentPostId],
  );

  if (!showCurrentPostTerms || !taxonomy || !currentPostId) {
    return <BlockEdit {...props} />;
  }

  const currentPostTermIds = currentPostTerms
    ? currentPostTerms.map((term: { id: number }) => term.id)
    : null;
  const configuredTermIds = Array.isArray(termQuery.include)
    ? termQuery.include
    : [];
  const include = currentPostTermIds
    ? configuredTermIds.length
      ? configuredTermIds.filter((termId: number) =>
          currentPostTermIds.includes(termId),
        )
      : currentPostTermIds
    : [0];

  const previewContext = {
    ...props.context,
    termQuery: {
      ...termQuery,
      include: include.length ? include : [0],
    },
  };

  return <BlockEdit {...props} context={previewContext} />;
};

addFilter(
  "editor.BlockEdit",
  "current-post-terms-query/preview",
  withCurrentPostTermsPreview,
);
