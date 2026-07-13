import { __ } from "@wordpress/i18n";
import { PanelBody, ToggleControl } from "@wordpress/components";

type CurrentPostTermsToggleProps = {
  termQuery: Record<string, any>;
  setAttributes: (attributes: any) => void;
};

export const CurrentPostTermsToggle = ({
  termQuery,
  setAttributes,
}: CurrentPostTermsToggleProps) => {
  const taxonomy = termQuery.taxonomy || "";

  if (!taxonomy) {
    return null;
  }

  const showCurrentPostTerms = Boolean(termQuery.showCurrentPostTerms);

  const handleToggle = (value: boolean) => {
    const nextTermQuery = {
      ...termQuery,
    };

    if (value) {
      nextTermQuery.showCurrentPostTerms = true;
    } else {
      delete nextTermQuery.showCurrentPostTerms;
    }

    setAttributes({
      termQuery: nextTermQuery,
    });
  };

  return (
    <PanelBody title={__("Current post terms", "current-post-terms-query")}>
      <ToggleControl
        label={__("Show only terms of the current post", "current-post-terms-query")}
        help={
          showCurrentPostTerms
            ? __("The loop uses terms assigned to the current post.", "current-post-terms-query")
            : __("The loop uses all terms from the selected taxonomy.", "current-post-terms-query")
        }
        checked={showCurrentPostTerms}
        onChange={handleToggle}
      />
    </PanelBody>
  );
};
