import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { testimonials = [] } = attributes;
    return (
        <div className="wp-block-lejournaldesactus-testimonials">
            {testimonials.map((item, i) => (
                <div className="testimonial-item" key={i}>
                    {item.image && (
                        <img className="testimonial-img" src={item.image} alt="" />
                    )}
                    <div className="testimonial-content">
                        <span className="testimonial-name">{item.name}</span>
                        {item.role && (
                            <span className="testimonial-role">{item.role}</span>
                        )}
                        <blockquote className="testimonial-text">{item.text}</blockquote>
                    </div>
                </div>
            ))}
        </div>
    );
}
