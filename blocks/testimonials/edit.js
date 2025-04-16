import { Fragment } from '@wordpress/element';
import { MediaUpload, MediaUploadCheck, RichText, PlainText } from '@wordpress/block-editor';
import { Button, IconButton } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { testimonials = [] } = attributes;

    const updateTestimonial = (index, field, value) => {
        const newTestimonials = [...testimonials];
        newTestimonials[index] = { ...newTestimonials[index], [field]: value };
        setAttributes({ testimonials: newTestimonials });
    };

    const addTestimonial = () => {
        setAttributes({ testimonials: [...testimonials, { name: '', role: '', text: '', image: '' }] });
    };

    const removeTestimonial = (index) => {
        const newTestimonials = testimonials.filter((_, i) => i !== index);
        setAttributes({ testimonials: newTestimonials });
    };

    return (
        <div className="wp-block-lejournaldesactus-testimonials">
            {testimonials.map((item, i) => (
                <div className="testimonial-item" key={i}>
                    <div className="testimonial-img-wrap">
                        <MediaUploadCheck>
                            <MediaUpload
                                onSelect={media => updateTestimonial(i, 'image', media.url)}
                                allowedTypes={[ 'image' ]}
                                value={item.image}
                                render={({ open }) => (
                                    item.image ? (
                                        <Fragment>
                                            <img className="testimonial-img" src={item.image} alt="" />
                                            <IconButton icon="no-alt" label="Supprimer" onClick={() => updateTestimonial(i, 'image', '')} />
                                        </Fragment>
                                    ) : (
                                        <Button onClick={open} icon="format-image" label="Ajouter une photo">Ajouter une photo</Button>
                                    )
                                )}
                            />
                        </MediaUploadCheck>
                    </div>
                    <PlainText
                        className="testimonial-name"
                        placeholder="Nom"
                        value={item.name}
                        onChange={val => updateTestimonial(i, 'name', val)}
                    />
                    <PlainText
                        className="testimonial-role"
                        placeholder="Fonction (optionnelle)"
                        value={item.role}
                        onChange={val => updateTestimonial(i, 'role', val)}
                    />
                    <RichText
                        className="testimonial-text"
                        tagName="blockquote"
                        placeholder="Témoignage..."
                        value={item.text}
                        onChange={val => updateTestimonial(i, 'text', val)}
                    />
                    <IconButton icon="trash" label="Supprimer ce témoignage" onClick={() => removeTestimonial(i)} />
                </div>
            ))}
            <Button isPrimary onClick={addTestimonial} icon="plus">Ajouter un témoignage</Button>
        </div>
    );
}
