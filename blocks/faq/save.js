import { RichText, InnerBlocks } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { faqs = [] } = attributes;
    return (
        <div className="wp-block-lejournaldesactus-faq">
            {faqs.map((item, i) => (
                <div className="faq-item" key={i}>
                    <button className="faq-question" type="button" aria-expanded="false" aria-controls={`faq-answer-${i}`}>{item.question}</button>
                    <div className="faq-answer" id={`faq-answer-${i}`} hidden>
                        {item.image && (
                            <img className="faq-image" src={item.image} alt="" style={{maxWidth:'120px',maxHeight:'120px',marginBottom:'0.5rem'}} />
                        )}
                        <div className="faq-answer-content">
                            <InnerBlocks.Content />
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}
