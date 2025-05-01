import { registerBlockType } from '@wordpress/blocks';
import { createElement } from '@wordpress/element'; // 👈 add this

registerBlockType('dvla-lookup/display-vehicle-details', {
    title: 'Vehicle Details (DVLA Lookup)',
    icon: 'car',
    category: 'widgets',
    edit: () => {
        return (
            <p><strong>Vehicle Details Block Preview (Editor)</strong></p>
        );
    },
    save: () => {
        return null;
    }
});
