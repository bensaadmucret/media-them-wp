import { Fragment } from '@wordpress/element';
import { RichText, MediaUpload, MediaUploadCheck, InnerBlocks } from '@wordpress/block-editor';
import { Button, IconButton } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { faqs = [] } = attributes;

    const updateFAQ = (index, field, value) => {
        const newFaqs = [...faqs];
        newFaqs[index] = { ...newFaqs[index], [field]: value };
        setAttributes({ faqs: newFaqs });
    };

    const addFAQ = () => {
        setAttributes({ faqs: [...faqs, { question: '', image: '' }] });
    };

    const removeFAQ = (index) => {
        const newFaqs = faqs.filter((_, i) => i !== index);
        setAttributes({ faqs: newFaqs });
    };

    return (
        <div className="wp-block-lejournaldesactus-faq">
            {faqs.map((item, i) => (
                <div className="faq-item" key={i}>
                    <RichText
                        tagName="div"
                        className="faq-question"
                        placeholder="Question..."
                        value={item.question}
                        onChange={val => updateFAQ(i, 'question', val)}
                        allowedFormats={[]}
                    />
                    <div className="faq-image-wrap">
                        <MediaUploadCheck>
                            <MediaUpload
                                onSelect={media => updateFAQ(i, 'image', media.url)}
                                allowedTypes={[ 'image' ]}
                                value={item.image}
                                render={({ open }) => (
                                    item.image ? (
                                        <Fragment>
                                            <img className="faq-image" src={item.image} alt="" style={{maxWidth:'120px',maxHeight:'120px',marginBottom:'0.5rem'}} />
                                            <IconButton icon="no-alt" label="Supprimer l'image" onClick={() => updateFAQ(i, 'image', '')} />
                                        </Fragment>
                                    ) : (
                                        <Button onClick={open} icon="format-image" label="Ajouter une image">Ajouter une image</Button>
                                    )
                                )}
                            />
                        </MediaUploadCheck>
                    </div>
                    <div className="faq-answer-content">
                        <InnerBlocks
                            allowedBlocks={[ 'core/paragraph', 'core/image', 'core/gallery', 'core/list', 'core/video' ]}
                            templateLock={false}
                        />
                    </div>
                    <IconButton icon="trash" label="Supprimer cette question" onClick={() => removeFAQ(i)} />
                </div>
            ))}
            <Button isPrimary onClick={addFAQ} icon="plus">Ajouter une question</Button>
        </div>
    );
}
