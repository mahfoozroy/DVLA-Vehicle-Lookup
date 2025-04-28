import { registerBlockType } from '@wordpress/blocks';

registerBlockType('dvla-lookup/display-vehicle-details', {
    edit: () => {
        return (
            <p><strong>Vehicle Details will appear here on the front-end.</strong></p>
        );
    },
    save: () => {
        return null; // Server-rendered via PHP callback
    }
});
