import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { TextControl, PanelBody } from '@wordpress/components';
import { createElement } from '@wordpress/element';

// Existing vehicle details block (already present)
registerBlockType('dvla-lookup/display-vehicle-details', {
    title: 'Vehicle Details (DVLA Lookup)',
    icon: 'car',
    category: 'widgets',
    edit: () => (
        <p><strong>Vehicle Details Block Preview</strong></p>
    ),
    save: () => null,
});

// NEW vehicle lookup form block
registerBlockType('dvla-lookup/vehicle-lookup-form', {
    title: 'Vehicle Lookup Form (DVLA)',
    icon: 'search',
    category: 'widgets',
    attributes: {
        redirect: {
            type: 'string',
            default: '/booking-page'
        }
    },
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();

        return (
            <>
                <InspectorControls>
                    <PanelBody title="Form Settings">
                        <TextControl
                            label="Redirect URL"
                            value={attributes.redirect}
                            onChange={(val) => setAttributes({ redirect: val })}
                        />
                    </PanelBody>
                </InspectorControls>
                <div {...blockProps}>
                    <p><strong>Vehicle Lookup Form Preview</strong></p>
                    <p>Redirects to: <code>{attributes.redirect}</code></p>
                </div>
            </>
        );
    },
    save: () => null // Rendered by PHP
});
