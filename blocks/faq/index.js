import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import './style.css';

registerBlockType('lejournaldesactus/faq', {
    title: 'FAQ (Accordéon)',
    icon: 'editor-help',
    category: 'widgets',
    attributes: {
        faqs: {
            type: 'array',
            default: [],
            source: 'query',
            selector: '.faq-item',
            query: {
                question: { type: 'string', source: 'text', selector: '.faq-question' },
            },
        },
    },
    edit: Edit,
    save,
});
