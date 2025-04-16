import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import './style.css';

registerBlockType('lejournaldesactus/testimonials', {
    title: 'Témoignages',
    icon: 'format-quote',
    category: 'widgets',
    attributes: {
        testimonials: {
            type: 'array',
            default: [],
            source: 'query',
            selector: '.testimonial-item',
            query: {
                name: { type: 'string', source: 'text', selector: '.testimonial-name' },
                role: { type: 'string', source: 'text', selector: '.testimonial-role' },
                text: { type: 'string', source: 'text', selector: '.testimonial-text' },
                image: { type: 'string', source: 'attribute', selector: '.testimonial-img', attribute: 'src' },
            },
        },
    },
    edit: Edit,
    save,
});
